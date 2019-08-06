<?php

namespace wdmg\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\BaseFileHelper;
use yii\helpers\ArrayHelper;
use wdmg\admin\models\Modules;
use wdmg\admin\models\ModulesSearch;
use wdmg\users\models\UsersSignin;
//use wdmg\users\models\UsersSignup;
//use wdmg\users\models\UsersPasswordRequest;
//use wdmg\users\models\UsersResetPassword;

/**
 * AdminController implements the CRUD actions for API model.
 */
class AdminController extends Controller
{
    public $defaultAction = 'index';
    public $layout = 'welcome';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'restore' => ['GET', 'POST'],
                    'logout' => ['POST'],
                    'checkpoint' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'checkpoint', 'restore'],
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'except' => ['login', 'checkpoint', 'restore'],
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ]
                ],
            ];
        }

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $this->view->params['langs'] = [
            ['label' => 'English', 'url' => '?lang=en-US', 'active'=> (Yii::$app->language == 'en-US') ? true : false, 'options' => ['class' => (Yii::$app->language == 'en-US') ? ['class' => 'active'] : false]],
            ['label' => 'Русский', 'url' => '?lang=ru-RU', 'active'=> (Yii::$app->language == 'ru-RU') ? true : false, 'options' => ['class' => (Yii::$app->language == 'ru-RU') ? ['class' => 'active'] : false]],
        ];

        return parent::beforeAction($action);
    }

    /**
     * Index action
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'dashboard';

        if (Yii::$app->user->isGuest)
            return $this->redirect(['admin/login']);
        else
            return $this->render('index');
    }

    /**
     * Modules action
     * @return mixed
     */
    public function actionModules()
    {
        $this->layout = 'dashboard';
        $this->viewPath = $this->viewPath . '/modules';

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['admin/login']);
        } else {

            // We will receive a copy of model of modules
            $model = new Modules();
            $action = Yii::$app->request->get('action', null);

            // Change model status (aJax request by switcher)
            if (Yii::$app->request->isAjax) {
                if (Yii::$app->request->get('change') == "status") {
                    if (Yii::$app->request->post('id', null)) {
                        $id = Yii::$app->request->post('id');
                        $status = Yii::$app->request->post('value', 0);
                        $model = $model->findOne(['id' => intval($id)]);
                        if (intval($model->protected) == 0) {
                            if ($model->updateAttributes(['status' => intval($status)]))
                                return true;
                            else
                                return false;
                        }
                    }
                } elseif ($action == "view") {
                    if (Yii::$app->request->get('id', null)) {
                        $id = Yii::$app->request->get('id');
                        $status = Yii::$app->request->get('value', 0);
                        $model = $model->findOne(['id' => intval($id)]);
                        return $this->renderAjax('view', [
                            'model' => $model
                        ]);
                    }
                }
            } else {
                if ($action == "delete") {
                    if (Yii::$app->request->get('id', null)) {
                        $id = Yii::$app->request->get('id');
                        $status = Yii::$app->request->get('value', 0);
                        $model = $model->findOne(['id' => intval($id)]);
                        if (intval($model->protected) == 0) {
                            if ($model->updateAttributes(['status' => $model::MODULE_STATUS_NOT_INSTALL])) {
                                Yii::$app->getSession()->setFlash(
                                    'success',
                                    Yii::t(
                                        'app/modules/admin',
                                        'OK! Module `{module}` successfully deleted.',
                                        [
                                            'module' => $model->name
                                        ]
                                    )
                                );
                            } else {
                                Yii::$app->getSession()->setFlash(
                                    'danger',
                                    Yii::t(
                                        'app/modules/admin',
                                        'An error occurred while deleting a module `{module}`.',
                                        [
                                            'module' => $model->name
                                        ]
                                    )
                                );
                            }
                        }
                    }
                } elseif ($action == "activate" || $action == "disable") {
                    if (Yii::$app->request->get('id', null)) {
                        $id = Yii::$app->request->get('id');

                        if ($action == "activate")
                            $status = $model::MODULE_STATUS_ACTIVE;
                        else
                            $status = $model::MODULE_STATUS_DISABLED;

                        $model = $model->findOne(['id' => intval($id)]);
                        if (intval($model->protected) == 0) {
                            if ($model->updateAttributes(['status' => $status])) {
                                Yii::$app->getSession()->setFlash(
                                    'success',
                                    Yii::t(
                                        'app/modules/admin',
                                        'OK! Module `{module}` properties successfully updated.',
                                        [
                                            'module' => $model->name
                                        ]
                                    )
                                );
                            } else {
                                Yii::$app->getSession()->setFlash(
                                    'danger',
                                    Yii::t(
                                        'app/modules/admin',
                                        'An error occurred while updating a module `{module}` properties.',
                                        [
                                            'module' => $model->name
                                        ]
                                    )
                                );
                            }
                        }
                    }
                }
            }

            // Get the list of supported modules
            $support = $this->module->getSupportModules();

            // Perform a sample of already installed system modules
            $modules = $model::getModules(false);

            // Let's go around the sample of available (preinstalled by the package manager) system modules
            $extensions = $model::getExtensions($modules, $support);

            // Prepare a list of system modules with subsequent filtering.
            $searchModel = new ModulesSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            // Adding a new module model
            if ($post = Yii::$app->request->post()) {
                if ($module_id = $post['Modules']['extensions']) {
                    $module = Yii::$app->extensions[$module_id];
                    $alias = array_key_first($module['alias']);

                    $activate = false;
                    if (isset($post['Modules']['autoActivate']))
                        $activate = $post['Modules']['autoActivate'];

                    // Read the module meta data
                    $composer = BaseFileHelper::normalizePath(Yii::getAlias($alias) . '\composer.json');
                    if (file_exists($composer)) {
                        $string = file_get_contents($composer);

                        // and decode them...
                        if ($meta = Json::decode($string)) {

                            // Last check before filling in the attributes of a module
                            if ($module_id == $meta["name"]) {

                                $model->setAttribute('module', substr(strstr($alias, '/'), 1, strlen($alias)));

                                $model->setAttribute('name', $meta["name"]);
                                $model->setAttribute('description', $meta["description"]);

                                $model->setAttribute('class', str_replace('@', '', BaseFileHelper::normalizePath($alias . '\Module')));

                                if (isset($meta["autoload"]["psr-4"])) {
                                    $path = array_key_first($meta["autoload"]["psr-4"]);
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
                                    ], (is_array($model->options)) ? $model->options : unserialize($model->options)));

                                    // Checking accessibility of module
                                    $module = Yii::$app->getModule('admin/' . $model->module);
                                    if ($module->install()) {

                                        // Setting priority of loading
                                        $model->priority = intval($module->getPriority());

                                        // Save module item
                                        if($model->save()) {

                                            // Remove added modules from extensions list
                                            unset($extensions[$model->name]);

                                            Yii::$app->getSession()->setFlash(
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
                                        } else {
                                            Yii::$app->getSession()->setFlash(
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
                                        Yii::$app->getSession()->setFlash(
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
                                }
                            }
                        } else {
                            Yii::$app->getSession()->setFlash(
                                'danger',
                                Yii::t(
                                    'app/modules/admin',
                                    'An error occurred while parsing `composer.json` of module `{module}`.',
                                    [
                                        'module' => $model->name
                                    ]
                                )
                            );
                        }
                    } else {
                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/admin',
                                'Error! File `composer.json` of `{module}` module not exist.',
                                [
                                    'module' => $model->name
                                ]
                            )
                        );
                    }
                } else {
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/admin',
                            'Error! Module `{module}` not present as extensions of application. Is install from Composer?',
                            [
                                'module' => $model->name
                            ]
                        )
                    );
                }
            }

            return $this->render('index', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'extensions' => $extensions,
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {

        $this->layout = 'welcome';

        if (!Yii::$app->user->isGuest)
            return $this->redirect(['admin/index']);

        $model = new UsersSignin();
        if ($model->load(Yii::$app->request->post())) {

            Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGIN, function($e) {
                if(isset(Yii::$app->components['activity']))
                    Yii::$app->activity->set('User has successfully login.', 'login', 'info', 2);
            });

            try {
                if ($model->login()) {
                    return $this->redirect(['admin/index']);
                }
            } catch (\DomainException $error) {
                Yii::$app->session->setFlash('error', $error->getMessage());
                return $this->redirect(['admin/login']);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {

        Yii::$app->user->on(\yii\web\User::EVENT_BEFORE_LOGOUT, function($e) {
            if(isset(Yii::$app->components['activity']))
                Yii::$app->activity->set('User has successfully logout.', 'logout', 'info', 2);
        });

        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Restore password.
     *
     * @return mixed
     */
    public function actionRestore()
    {
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['admin/index']);

        // Checkout password reset token
        $token = Yii::$app->request->get('token');
        if ($token) {

            $model = new \wdmg\users\models\UsersResetPassword($token, [], true);
            if ($model->userIsFound() && Yii::$app->request->isGet) {
                return $this->render('reset', [
                    'model' => $model,
                ]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app/modules/admin', 'Incorrect password reset token.'));
            }

            if (!Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
                Yii::$app->session->setFlash('success', Yii::t('app/modules/admin', 'New password saved!'));
                return $this->redirect(['admin/index']);
            }

        }

        $model = new \wdmg\users\models\UsersPasswordRequest();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            // Get `Users` module
            if (Yii::$app->hasModule('admin/users'))
                $module = Yii::$app->getModule('admin/users');
            else
                $module = Yii::$app->getModule('users');

            $resetTokenExpire = $module->passwordReset['resetTokenExpire'];
            if(isset(Yii::$app->params['resetTokenExpire']))
                $resetTokenExpire = Yii::$app->params['resetTokenExpire'];

            $supportEmail = $module->passwordReset['supportEmail'];
            if(isset(Yii::$app->params['supportEmail']))
                $supportEmail = Yii::$app->params['supportEmail'];

            $module->passwordReset = [
                'resetTokenExpire' => $resetTokenExpire,
                'checkTokenRoute' => '/admin/restore',
                'supportEmail' => $supportEmail,
                'emailViewPath' => [
                    'html' => '@vendor/wdmg/yii2-admin/mail/passwordReset-html',
                    'text' => '@vendor/wdmg/yii2-admin/mail/passwordReset-text',
                ],
            ];

            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app/modules/admin', 'Check your email for further instructions.'));
                return $this->redirect(['admin/login']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app/modules/admin', 'Sorry, we are unable to reset password for the provided email address.'));
            }
        }

        return $this->render('restore', [
            'model' => $model,
        ]);
    }

    /**
     * Check of user still auth.
     *
     * @return Response
     */
    public function actionCheckpoint()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest)
            return ['loggedin' => true];
        else
            return ['loggedin' => false];
    }
}
