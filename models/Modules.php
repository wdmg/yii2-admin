<?php

namespace wdmg\admin\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use \wdmg\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%bookmarks}}".
 *
 * @property int $id
 * @property string $module
 * @property string $name
 * @property string $description
 * @property string $class
 * @property string $bootstrap
 * @property string $homepage
 * @property string $support
 * @property string $authors
 * @property string $require
 * @property string $type
 * @property string $license
 * @property string $version
 * @property string $options
 * @property int $status
 * @property int $protected
 * @property int $priority
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 * @property Users $user
 */

class Modules extends \yii\db\ActiveRecord
{

    const MODULE_STATUS_NOT_INSTALL = -1; // Module not installed
    const MODULE_STATUS_DISABLED = 0; // Module installed but not active
    const MODULE_STATUS_ACTIVE = 1; // Module enabled

    public $extensions;
    public $autoActivate;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%modules}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['module', 'name'], 'unique'],
            [['module', 'name', 'class', 'status'], 'required'],
            [['module', 'type', 'license', 'version'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
            [['description', 'class', 'bootstrap', 'homepage'], 'string', 'max' => 255],
            [['support', 'authors', 'require', 'options'], function ($attribute, $params) {
                if(!is_array($this->$attribute)){
                    $this->addError($attribute,'Attribute `'.$attribute.'` is not array!');
                }
            }],
            [['status', 'priority'], 'integer'],
            [['status'], 'default', 'value' => self::MODULE_STATUS_NOT_INSTALL],
            [['protected'], 'boolean'],
            [['protected', 'priority'], 'default', 'value' => 0],
            [['autoActivate'], 'boolean'],
            [['autoActivate'], 'default', 'value' => 1],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $rules[] = [['created_by', 'updated_by'], 'required'];
            $rules[] = [['created_by', 'updated_by'], 'integer'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/admin', 'ID'),
            'module' => Yii::t('app/modules/admin', 'Module ID'),
            'name' => Yii::t('app/modules/admin', 'Name'),
            'description' => Yii::t('app/modules/admin', 'Description'),
            'class' => Yii::t('app/modules/admin', 'Module class'),
            'bootstrap' => Yii::t('app/modules/admin', 'Bootstrap class'),
            'homepage' => Yii::t('app/modules/admin', 'Homepage URL'),
            'support' => Yii::t('app/modules/admin', 'Support URL'),
            'authors' => Yii::t('app/modules/admin', 'Authors'),
            'require' => Yii::t('app/modules/admin', 'Requires'),
            'type' => Yii::t('app/modules/admin', 'Type'),
            'license' => Yii::t('app/modules/admin', 'License'),
            'version' => Yii::t('app/modules/admin', 'Version'),
            'options' => Yii::t('app/modules/admin', 'Options'),
            'status' => Yii::t('app/modules/admin', 'Status'),
            'autoActivate' => Yii::t('app/modules/admin', '- auto activate'),
            'protected' => Yii::t('app/modules/admin', 'Protected'),
            'priority' => Yii::t('app/modules/admin', 'Priority'),
            'created_at' => Yii::t('app/modules/admin', 'Created at'),
            'created_by' => Yii::t('app/modules/admin', 'Created by'),
            'updated_at' => Yii::t('app/modules/admin', 'Updated at'),
            'updated_by' => Yii::t('app/modules/admin', 'Updated by'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        if (is_array($this->support))
            $this->support = serialize($this->support);

        if (is_array($this->authors))
            $this->authors = serialize($this->authors);

        if (is_array($this->require))
            $this->require = serialize($this->require);

        if (is_array($this->options))
            $this->options = serialize($this->options);

        return parent::beforeSave($insert);
    }

    /**
     * Get avialibles modules
     *
     * @note Function get modules list from DB
     * @param $onlyActive boolean flag, if need only active modules
     * @return array of modules
     */
    public static function getModules($onlyActive = true)
    {
        if ($onlyActive)
            $cond = ['status' => self::MODULE_STATUS_ACTIVE];
        else
            $cond = '`status` >= ' . self::MODULE_STATUS_DISABLED;

        $modules = self::find()
            ->where($cond)
            ->asArray()
            ->indexBy('name')
            ->orderBy(['priority' => SORT_ASC])
            ->all();

        return $modules;
    }

    /**
     * Get preinstalled extensions
     *
     * @note Function get extensions list from extensions.php (composer)
     * @param $modules array of available modules
     * @param $support array of support modules
     * @return array of extensions
     */
    public static function getExtensions($modules = [], $support = [])
    {
        if (!is_array($modules) || !is_array($support))
            return [];

        $extensions = [];
        foreach (Yii::$app->extensions as $key => $extension) {
            // Limit the output of only those modules that are supported by the system.
            // and also check if the module was installed and activated before
            if (in_array($extension['name'], $support) && (!array_key_exists($extension['name'], $modules))) {
                $extensions[$key] = $extension["name"];
            }
        }
        return $extensions;
    }

    public function installModule($module_id = null, $activate = false)
    {
        if (is_null($module_id))
            return;

        if (isset(Yii::$app->extensions[$module_id])) {
            $module = Yii::$app->extensions[$module_id];
            $alias = ArrayHelper::keyFirst($module['alias']);
            $model = new self();

            // Read the module meta data
            $composer = BaseFileHelper::normalizePath(Yii::getAlias($alias) . '\composer.json');
            if (file_exists($composer)) {
                $string = file_get_contents($composer);

                // and decode them...
                if ($meta = Json::decode($string)) {
                    // Last check before filling in the attributes of a module
                    if ($module_id == $meta["name"] && $module_id !== "wdmg/yii2-base") {
                        $model->setAttribute('module', substr(strstr($alias, '/'), 1, strlen($alias)));
                        $model->setAttribute('name', $meta["name"]);
                        $model->setAttribute('description', $meta["description"]);
                        $model->setAttribute('class', str_replace('/', '\\', str_replace('@', '', BaseFileHelper::normalizePath($alias . '\Module'))));

                        if (isset($meta["autoload"]["psr-4"])) {
                            $path = ArrayHelper::keyFirst($meta["autoload"]["psr-4"]);
                            if (!empty($meta["autoload"]["psr-4"][$path])) {
                                $model->setAttribute('bootstrap', $meta["autoload"]["psr-4"][$path] . '\Bootstrap');
                            } else {
                                $model->setAttribute('bootstrap', null);
                            }
                        } else {
                            $model->setAttribute('bootstrap', null);
                        }

                        if (isset($meta["homepage"]))
                            $model->setAttribute('homepage', $meta["homepage"]);

                        if (isset($meta["support"])) {
                            if (is_array($meta["support"]))
                                $model->setAttribute('support', $meta["support"]);
                            else
                                $model->setAttribute('support', null);

                        }

                        if (isset($meta["authors"])) {
                            if (is_array($meta["authors"]))
                                $model->setAttribute('authors', $meta["authors"]);
                            else
                                $model->setAttribute('authors', null);

                        }

                        if (isset($meta["require"])) {
                            if (is_array($meta["require"]))
                                $model->setAttribute('require', $meta["require"]);
                            else
                                $model->setAttribute('require', null);

                        }

                        if (isset($meta["type"]))
                            $model->setAttribute('type', $meta["type"]);

                        if (isset($meta["license"]))
                            $model->setAttribute('license', $meta["license"]);

                        if (isset($meta["version"]))
                            $model->setAttribute('version', $meta["version"]);


                        if (isset($meta["extra"]["options"])) {
                            if (is_array($meta["extra"]["options"]))
                                $model->setAttribute('options', $meta["extra"]["options"]);
                            else
                                $model->setAttribute('options', null);

                        }

                        if ($activate)
                            $model->setAttribute('status', $model::MODULE_STATUS_ACTIVE);
                        else
                            $model->setAttribute('status', $model::MODULE_STATUS_DISABLED);

                        // Let's go through validation and save the model in the database
                        if ($model->validate()) {

                            Yii::$app->getModule('admin')->setModule($model->module, ArrayHelper::merge([
                                'class' => $model->class
                            ], (is_array($model->options)) ? $model->options : (
                                is_string($model->options) ? unserialize($model->options): []
                            )));

                            // Prepare to module check
                            $module_id = 'admin/' . $model->module;

                            // Checking accessibility of module
                            if ($module = Yii::$app->getModule($module_id)) {

                                if ($module->install()) {

                                    // Setting priority of loading
                                    $model->priority = intval($module->getPriority());

                                    // Save module item
                                    if ($model->save()) {
                                        Yii::$app->getSession()->addFlash(
                                            'success',
                                            Yii::t(
                                                'app/modules/admin',
                                                'OK! Module `{module}` successfully {status}.',
                                                [
                                                    'module' => $model->name,
                                                    'status' => ($activate) ? Yii::t('app/modules/admin', 'added and activated') : Yii::t('app/modules/admin', 'added')
                                                ]
                                            )
                                        );

                                        return true;
                                    } else {
                                        Yii::$app->getSession()->addFlash(
                                            'danger',
                                            Yii::t(
                                                'app/modules/admin',
                                                'An error occurred while adding a module `{module}`.',
                                                [
                                                    'module' => $model->name
                                                ]
                                            )
                                        );
                                    }
                                } else {
                                    Yii::$app->getSession()->addFlash(
                                        'danger',
                                        Yii::t(
                                            'app/modules/admin',
                                            'An error occurred while install a module `{module}`.',
                                            [
                                                'module' => $model->name
                                            ]
                                        )
                                    );
                                }
                            } else {
                                Yii::$app->getSession()->addFlash(
                                    'danger',
                                    Yii::t(
                                        'app/modules/admin',
                                        'Unable to resolve child module `{module}`.',
                                        [
                                            'module' => $module_id
                                        ]
                                    )
                                );
                            }
                        }
                    }
                } else {
                    Yii::$app->getSession()->addFlash(
                        'danger',
                        Yii::t(
                            'app/modules/admin',
                            'An error occurred while parsing `composer.json` of module `{module}`.',
                            [
                                'module' => $module_id
                            ]
                        )
                    );
                }
            } else {
                Yii::$app->getSession()->addFlash(
                    'danger',
                    Yii::t(
                        'app/modules/admin',
                        'Error! File `composer.json` of `{module}` module not exist by path `{path}`.',
                        [
                            'module' => $module_id,
                            'path' => $composer
                        ]
                    )
                );
            }
        } else {
            Yii::$app->getSession()->addFlash(
                'danger',
                Yii::t(
                    'app/modules/admin',
                    'Error! Module `{module}` not present as extensions of application. Is install from Composer?',
                    [
                        'module' => $module_id
                    ]
                )
            );
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return null;
    }
}
