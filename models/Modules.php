<?php

namespace wdmg\admin\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
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
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
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
    public function getModules($onlyActive = true)
    {
        if ($onlyActive)
            $cond = ['status' => self::MODULE_STATUS_ACTIVE];
        else
            $cond = '`status` >= ' . self::MODULE_STATUS_DISABLED;

        return self::find()
            ->where($cond)
            ->asArray()
            ->indexBy('name')
            ->orderBy(['priority' => SORT_ASC])
            ->all();
    }

    /**
     * Get preinstalled extensions
     *
     * @note Function get extensions list from extensions.php (composer)
     * @param $modules array of available modules
     * @param $support array of support modules
     * @return array of extensions
     */
    public function getExtensions($modules = [], $support = [])
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']))
            return $this->hasOne(\wdmg\users\models\Users::className(), ['id' => 'created_by']);
        else
            return null;
    }
}
