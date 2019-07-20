<?php

use yii\db\Migration;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\BaseFileHelper;

/**
 * Class m190713_234918_modules
 */
class m190713_234918_modules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%modules}}', [
            'id' => $this->primaryKey(),
            'module' => $this->string(64)->notNull()->unique(),
            'name' => $this->string(128)->notNull()->unique(),
            'description' => $this->string(255),
            'class' => $this->string(255)->notNull(),
            'bootstrap' => $this->string(255)->null(),
            'homepage' => $this->string(255)->null(),
            'support' => $this->text()->null(),
            'authors' => $this->text()->null(),
            'require' => $this->text()->null(),
            'type' => $this->string(64)->null(),
            'license' => $this->string(64)->null(),
            'version' => $this->string(64)->notNull(),
            'options' => $this->text(),
            'status' => $this->tinyInteger(1)->null()->defaultValue(0),
            'protected' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('{{%idx-modules}}', '{{%modules}}', ['module', 'name', 'version', 'status']);

        // List of preinstall modules
        $modules = [
            'wdmg/yii2-options',
            'wdmg/yii2-users',
            'wdmg/yii2-rbac',
        ];

        // Each modules who what to be pre installed
        foreach ($modules as $module_id) {
            $module = Yii::$app->extensions[$module_id];

            // Get alias of module
            $alias = array_key_first($module['alias']);

            // Read the module meta data
            $composer = BaseFileHelper::normalizePath(Yii::getAlias($alias) . '\composer.json');
            if (file_exists($composer)) {
                $string = file_get_contents($composer);

                // and decode them...
                if ($meta = Json::decode($string)) {

                    // Last check before filling in the attributes of a module
                    if ($module_id == $meta["name"]) {

                        // Prepare module options and attributes
                        $bootstrap = null;
                        if (isset($meta["autoload"]["psr-4"])) {
                            $path = array_key_first($meta["autoload"]["psr-4"]);
                            if (!empty($meta["autoload"]["psr-4"][$path])) {
                                $bootstrap = $meta["autoload"]["psr-4"][$path] . '\Bootstrap';
                            }
                        }

                        $homepage = null;
                        if (isset($meta["homepage"]))
                            $homepage = $meta["homepage"];

                        $support = null;
                        if (isset($meta["support"])) {
                            if (is_array($meta["support"]))
                                $support = serialize($meta["support"]);
                        }

                        $authors = null;
                        if (isset($meta["authors"])) {
                            if (is_array($meta["authors"]))
                                $authors = serialize($meta["authors"]);
                        }

                        $require = null;
                        if (isset($meta["require"])) {
                            if (is_array($meta["require"]))
                                $require = serialize($meta["require"]);
                        }

                        $type = null;
                        if (isset($meta["type"]))
                            $type = $meta["type"];

                        $license = null;
                        if (isset($meta["license"]))
                            $license = $meta["license"];

                        $options = null;
                        if (isset($meta["extra"]["options"])) {
                            if (is_array($meta["extra"]["options"]))
                                $options = serialize($meta["extra"]["options"]);

                        }

                        // Install module
                        $this->insert('{{%modules}}', [
                            'module' => substr(strstr($alias, '/'), 1, strlen($alias)),
                            'name' => $meta["name"],
                            'description' => $meta["description"],
                            'class' => str_replace('@', '', BaseFileHelper::normalizePath($alias . '\Module')),
                            'bootstrap' => $bootstrap,
                            'homepage' => $homepage,
                            'support' => $support,
                            'authors' => $authors,
                            'require' => $require,
                            'type' => $type,
                            'license' => $license,
                            'version' => $meta["version"],
                            'options' => $options,
                            'status' => 1,
                            'protected' => 1,
                            'created_at' => new Expression('NOW()'),
                            'created_by' => 100,
                            'updated_at' => new Expression('NOW()'),
                            'updated_by' => 100,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%modules}}');
        $this->dropIndex('{{%idx-modules}}', '{{%modules}}');
        $this->dropTable('{{%modules}}');
    }
}
