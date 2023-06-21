<?php

namespace wdmg\admin\controllers;

use wdmg\users\models\Users;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\BaseFileHelper;
use wdmg\helpers\ArrayHelper;
use wdmg\helpers\StringHelper;
use wdmg\admin\models\Modules;
use wdmg\admin\models\ModulesSearch;
use wdmg\users\models\UsersSignin;

/**
 * AdminController implements the CRUD actions.
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
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'modules' => ['GET', 'POST'],
                    'restore' => ['GET', 'POST'],
                    'logout' => ['POST'],
                    'checkpoint' => ['POST'],
                    'favourites' => ['POST'],
                    'search' => ['POST'],
                    'error' => ['GET'],
                    'info' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
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
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        } else if ($this->module->moduleExist('admin/rbac')) { // Ok, then we check access according to the rules
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'except' => ['login', 'checkpoint', 'restore'],
                'rules' => [
                    [
                        'actions' => ['modules'],
                        'roles' => ['updateDashboard'],
                        'allow' => true
                    ], [
                        'roles' => ['viewDashboard'],
                        'allow' => true
                    ],
                ],
            ];
        }

	    if (!$this->module->isRestAPI())
		    $behaviors['access']['except'][] = 'error';

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {

        if (Yii::$app->hasModule('admin/users'))
            $module = Yii::$app->getModule('admin/users');
        else
            $module = Yii::$app->getModule('users');

        if ($module && isset(Yii::$app->params['admin.rememberDuration'])) {
            $module->rememberDuration = intval(Yii::$app->params['admin.rememberDuration']);
            Yii::$app->params['users.rememberDuration'] = $module->rememberDuration;
        }

        if ($module && isset(Yii::$app->params['admin.multiSignIn'])) {
            $module->multiSignIn = intval(Yii::$app->params['admin.multiSignIn']);
            Yii::$app->params['users.multiSignIn'] = $module->multiSignIn;
        }

        if ($module && isset(Yii::$app->params['admin.sessionTimeout'])) {
            $module->sessionTimeout = intval(Yii::$app->params['admin.sessionTimeout']);
            Yii::$app->params['users.sessionTimeout'] = $module->sessionTimeout;
        }

        return parent::beforeAction($action);
    }


    /**
     * Index action
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'dashboard';

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['admin/login']);
        } else {

            // Dashboard counter`s
            if ($this->module->moduleLoaded('admin/users'))
                $counters['users'] = $this->getNewUsersCount();

            if ($this->module->moduleLoaded('admin/store'))
                $counters['orders'] = $this->getNewOrdersCount();

            if ($this->module->moduleLoaded('admin/comments') || $this->module->moduleLoaded('admin/reviews')) {
                $counters['comments_and_reviews'] = $this->getNewCommentsCount();
            }

            if ($this->module->moduleLoaded('admin/billing'))
                $counters['transactions'] = $this->getNewTransactionsCount();

            if ($this->module->moduleLoaded('admin/newsletters'))
                $counters['newsletters'] = $this->getNewNewslettersCount();

            if ($this->module->moduleLoaded('admin/subscribers'))
                $counters['subscribers'] = $this->getNewSubscribersCount();

            // Dashboard widget`s
            if ($this->module->moduleLoaded('admin/pages'))
                $widgets['recentPages'] = $this->getRecentPages();

            if ($this->module->moduleLoaded('admin/news'))
                $widgets['recentNews'] = $this->getRecentNews();

            if ($this->module->moduleLoaded('admin/blog'))
                $widgets['recentPosts'] = $this->getRecentPosts();

            if ($this->module->moduleLoaded('admin/users'))
                $widgets['lastUsers'] = $this->getLastUsers();

            if ($this->module->moduleLoaded('admin/activity'))
                $widgets['recentActivity'] = $this->getRecentActivity();

            if ($this->module->moduleLoaded('admin/comments'))
                $widgets['recentComments'] = $this->getRecentComments();

            if ($this->module->moduleLoaded('admin/stats')) {
                $widgets['recentStats'] = $this->getRecentStats();
                $widgets['recentLoads'] = $this->getRecentLoads();
            }

            return $this->render('index', [
                'module' => $this->module,
                'counters' => (isset($counters)) ? $counters : [],
                'widgets' => (isset($widgets)) ? $widgets : []
            ]);
        }
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
                        $_model = $model->findOne(['id' => intval($id)]);
                        if (intval($_model->protected) == 0) {
                            if ($_model->updateAttributes(['status' => intval($status)]))
                                return true;
                            else
                                return false;
                        }
                    }
                } elseif ($action == "view") {
                    if (Yii::$app->request->get('id', null)) {
                        $id = Yii::$app->request->get('id');
                        $status = Yii::$app->request->get('value', 0);
                        $_model = $model->findOne(['id' => intval($id)]);
                        return $this->renderAjax('view', [
                            'model' => $_model,
                            'module' => $this->module
                        ]);
                    }
                }
            } else {
                if ($id = Yii::$app->request->get('id', null)) {
                    if ($_model = $model->findOne(['id' => intval($id)])) {
                        if (intval($_model->protected) == 0) {
                            switch ($action) {

                                case "delete":

                                    if ($_model->updateAttributes(['status' => $model::MODULE_STATUS_NOT_INSTALL])) {
                                        Yii::$app->getSession()->setFlash(
                                            'success',
                                            Yii::t(
                                                'app/modules/admin',
                                                'OK! Module `{module}` successfully deleted.',
                                                [
                                                    'module' => $_model->name
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
                                                    'module' => $_model->name
                                                ]
                                            )
                                        );
                                    }
                                    break;

                                case "activate":
                                case "disable":

                                    if ($action == "activate")
                                        $status = $model::MODULE_STATUS_ACTIVE;
                                    else
                                        $status = $model::MODULE_STATUS_DISABLED;

                                    if ($_model->updateAttributes(['status' => $status])) {
                                        Yii::$app->getSession()->setFlash(
                                            'success',
                                            Yii::t(
                                                'app/modules/admin',
                                                'OK! Module `{module}` properties successfully updated.',
                                                [
                                                    'module' => $_model->name
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
                                                    'module' => $_model->name
                                                ]
                                            )
                                        );
                                    }
                                    break;

                                case "clear":

                                    // Errors flag
                                    $errors = false;

                                    // Delete all module options from DB
                                    if (isset(Yii::$app->options) && !is_null($_model->module)) {

                                        if (!Yii::$app->options->deleteAll($_model->module))
                                            $errors = true;

                                        Yii::$app->options->clearCache();
                                    }

                                    // Delete module entry from DB
                                    if ($_model->delete() && !$errors) {
                                        Yii::$app->getSession()->setFlash(
                                            'success',
                                            Yii::t(
                                                'app/modules/admin',
                                                'OK! Module `{module}` date successfully cleared.',
                                                [
                                                    'module' => $_model->name
                                                ]
                                            )
                                        );
                                    } else {
                                        Yii::$app->getSession()->setFlash(
                                            'danger',
                                            Yii::t(
                                                'app/modules/admin',
                                                'An error occurred while clearing a module `{module}` data.',
                                                [
                                                    'module' => $_model->name
                                                ]
                                            )
                                        );
                                    }
                                    break;

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
            if (($post = Yii::$app->request->post()) && !Yii::$app->request->isAjax) {

                if ($module_id = $post['Modules']['extensions']) {
                    $module = Yii::$app->extensions[$module_id];

                    // Polyfill for array_key_first() for PHP <= 7.3.0
                    if (!function_exists('array_key_first')) {
                        function array_key_first(array $arr) {
                            foreach($arr as $key => $unused) {
                                return $key;
                            }
                            return NULL;
                        }
                    }
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

                                $model->setAttribute('class', str_replace('/', '\\', str_replace('@', '', BaseFileHelper::normalizePath($alias . '\Module'))));

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

                                    // Prepare to module check
                                    $module_id = 'admin/' . $model->module;
                                    $module = Yii::$app->getModule($module_id);

                                    // Checking accessibility of module
                                    if ($module) {

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
                                    } else {
                                        Yii::$app->getSession()->setFlash(
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
                'module' => $this->module,
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

                // Log activity
                if (isset(Yii::$app->components['activity'])) {
                    $username = Yii::$app->user->identity->username;
                    Yii::$app->activity->set('User `' . $username . '` has successfully login.', 'login', 'info', 2);
                }
            });

            try {
                if ($model->login()) {
                    return $this->redirect(['admin/index']);
                } else {
                    // Log activity
                    if (isset(Yii::$app->components['activity'])) {
                        $username = $model->username;
                        Yii::$app->activity->set('An error occurred while user `'.$username.'` has attempt login. Wrong password.', 'login', 'danger', 2);
                    }
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
            if (isset(Yii::$app->components['activity'])) {
                $username = Yii::$app->user->identity->username;
                Yii::$app->activity->set('User `'.$username.'` has successfully logout.', 'logout', 'info', 2);
            }
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
                $module = Yii::$app->getModule('admin/users', true);
            else
                $module = Yii::$app->getModule('users', true);

            $resetTokenExpire = $module->passwordReset['resetTokenExpire'];
            if (isset(Yii::$app->params['admin.resetTokenExpire']))
                $resetTokenExpire = Yii::$app->params['admin.resetTokenExpire'];

            $supportEmail = $module->passwordReset['supportEmail'];
            if (isset(Yii::$app->params['admin.supportEmail']))
                $supportEmail = Yii::$app->params['admin.supportEmail'];

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

                // Log activity
                if (isset(Yii::$app->components['activity'])) {
                    $username = $model->username;
                    Yii::$app->activity->set('User `'.$username.'` has request password restore.', 'restore', 'info', 2);
                }

                Yii::$app->session->setFlash('success', Yii::t('app/modules/admin', 'Check your email for further instructions.'));

                return $this->redirect(['admin/login']);
            } else {

                // Log activity
                if (isset(Yii::$app->components['activity'])) {
                    $username = $model->username;
                    Yii::$app->activity->set('An error occurred while user `'.$username.'` has request password restore.', 'restore', 'danger', 2);
                }

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


    /**
     * Add/remove from favourites
     *
     * @return Response
     */
    public function actionFavourites()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->isGuest) {
	        if ($url = \Yii::$app->request->post('url')) {
		        if ($label = \Yii::$app->request->post('label')) {

			        $favourites = Users::getOptions('favourites', []);
			        if (is_array($favourites)) {
				        if (!empty($favourites)) {
					        $skipped = false;
					        foreach ($favourites as $key => $row) {
						        if ($row['url'] == $url) {
									$skipped = true;
							        unset($favourites[$key]);
								}
					        }
					        if (!$skipped) {
						        $favourites[] = [
							        'label' => $label,
							        'url' => $url,
						        ];
					        }
				        } else {
					        $favourites[] = [
						        'label' => $label,
						        'url' => $url,
					        ];
				        }

				        $favourites = array_map("unserialize", array_unique(array_map("serialize", $favourites)));
			        }

					Yii::$app->getView()->params['favourites'] = $favourites;
			        Users::setOptions(['favourites' => $favourites], false);

					if (!$skipped)
						return $this->redirect($url);
					else
						return $this->redirect(\Yii::$app->request->referrer);
		        }
	        }
        }
    }


    public function actionSearch()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $results = [];
        if (!is_null($model = \Yii::$app->dashboard->search)) {
            if ($query = \Yii::$app->request->post('query')) {
                $results = $model->search($query, true);
            }
        }

        return ['results' => $results];
    }

    /**
     * Bugreport action.
     * @return mixed
     */
    public function actionBugreport()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['admin/login']);
        } else {
            $model = new \yii\base\DynamicModel(['name', 'email', 'message', 'screenshots', 'report']);
            $model->addRule(['name'], 'string', ['max' => 64]);
            $model->addRule(['email'], 'email');
            $model->addRule(['message'], 'string', ['max' => 255]);
            $model->addRule(['message', 'email'], 'required');
            $model->addRule(['screenshots'], 'file', ['skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 3, 'maxSize' => (1024 * 1024 * 2)]);
            $model->report = json_encode($this->getSystemData());
            if (Yii::$app->request->isPost && $model->validate()) {

                $uploadDir = Yii::getAlias('@webroot/uploads/attachments');
                if(!is_dir($uploadDir))
                    yii\helpers\FileHelper::createDirectory($uploadDir);


                // Attach report file
                $reports = null;
                if (Yii::$app->request->isPost) {
                    $jsonfile = $uploadDir .'/report-'.time().'-'.uniqid($model->name).'.json';
                    $fp = fopen($jsonfile, 'w+');
                    if (fwrite($fp, $model->report)) {
                        $reports[] = $jsonfile;
                    }
                    fclose($fp);
                }

                // Attach screenshots
                $screenshots = null;
                if (Yii::$app->request->isPost) {
                    $model->screenshots = \yii\web\UploadedFile::getInstances($model, 'screenshots');
                    if ($model->screenshots) {
                        foreach ($model->screenshots as $screenshot) {
                            $screenshotfile = $uploadDir .'/'. $screenshot->baseName . '.' . $screenshot->extension;
                            if($screenshot->saveAs($screenshotfile, false)) {
                                $screenshots[] = $screenshotfile;
                                Yii::warning('Attach screenshots '.'uploads/' . $screenshot->baseName . '.' . $screenshot->extension);
                            }
                        }
                    }
                }

                if ($model->load(Yii::$app->request->post(), 'DynamicModel')) {

                    $message = Yii::$app->mailer
                        ->compose([
                            'html' => '@vendor/wdmg/yii2-admin/mail/bugReport-html',
                            'text' => '@vendor/wdmg/yii2-admin/mail/bugReport-text',
                        ], [
                            'name' => $model->name,
                            'email' => $model->email,
                            'message' => $model->message,
                        ])
                        ->setFrom([$model->email => $model->name])
                        ->setTo('butterflycms.com@gmail.com')
                        ->setSubject(Yii::t('app/modules/admin', 'Bug report from {appname}', [
                            'appname' => Yii::$app->name,
                        ]));

                    $attachments = \yii\helpers\ArrayHelper::merge(
                        (is_array($reports) ? $reports : []),
                        (is_array($screenshots) ? $screenshots : [])
                    );

                    foreach ($attachments as $attachment) {
                        if (!empty($attachment))
                            $message->attach($attachment);
                    }

                    if ($message->send()) {
                        Yii::warning('Our bug report is sent successfully!');
                        Yii::$app->session->setFlash('success', Yii::t('app/modules/admin','Your bug report is sent successfully!'));
                    } else {
                        Yii::warning('Failed to send error report.');
                        Yii::$app->session->setFlash('error', Yii::t('app/modules/admin','Failed to send error report.'));
                    }

                    return $this->redirect(['admin/index']);
                }
            }

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('_bugreport', [
                    'model' => $model
                ]);
            } else {
                return $this->redirect(['admin/index']);
            }
        }
    }

    public function getRecentPages($limit = 5) {
        $model = new \wdmg\pages\models\Pages();
        if (class_exists('\wdmg\users\models\Users')) {
            $users = new \wdmg\users\models\Users();
            return $model::find()->select([$model::tableName() . '.id', $model::tableName() . '.name', $model::tableName() . '.created_by', $model::tableName() . '.updated_at'])
                ->joinWith(['createdBy' => function ($query) use ($users) {
                    $query->select([$users::tableName() . '.id', $users::tableName() . '.username']);
                }])->asArray()->limit(intval($limit))
                ->orderBy([$model::tableName() . '.updated_at' => SORT_DESC])->all();
        } else {
            return $model::find()->select('id, name, created_by, updated_at')->where(['status' => true])->asArray()->limit(intval($limit))->orderBy(['updated_at' => SORT_DESC])->all();
        }
    }

    public function getRecentNews($limit = 5) {
        $model = new \wdmg\news\models\News();
        if (class_exists('\wdmg\users\models\Users')) {
            $users = new \wdmg\users\models\Users();
            return $model::find()->select([$model::tableName() . '.id', $model::tableName() . '.name', $model::tableName() . '.created_by', $model::tableName() . '.updated_at'])
                ->joinWith(['createdBy' => function ($query) use ($users) {
                    $query->select([$users::tableName() . '.id', $users::tableName() . '.username']);
                }])->asArray()->limit(intval($limit))
                ->orderBy([$model::tableName() . '.updated_at' => SORT_DESC])->all();
        } else {
            return $model::find()->select('id, name, created_by, updated_at')->where(['status' => true])->asArray()->limit(intval($limit))->orderBy(['updated_at' => SORT_DESC])->all();
        }
    }

    public function getRecentPosts($limit = 5) {
        $model = new \wdmg\blog\models\Posts();
        if (class_exists('\wdmg\users\models\Users')) {
            $users = new \wdmg\users\models\Users();
            return $model::find()->select([$model::tableName() . '.id', $model::tableName() . '.name', $model::tableName() . '.created_by', $model::tableName() . '.updated_at'])
                ->joinWith(['createdBy' => function ($query) use ($users) {
                    $query->select([$users::tableName() . '.id', $users::tableName() . '.username']);
                }])->asArray()->limit(intval($limit))
                ->orderBy([$model::tableName() . '.updated_at' => SORT_DESC])->all();
        } else {
            return $model::find()->select('id, name, created_by, updated_at')->where(['status' => true])->asArray()->limit(intval($limit))->orderBy(['updated_at' => SORT_DESC])->all();
        }
    }

    public function getLastUsers($limit = 5) {
        $model = new \wdmg\users\models\Users();
        return $model::find()->select('id, username, created_at')->where(['status' => $model::USR_STATUS_ACTIVE])->orWhere(['status' => $model::USR_STATUS_WAITING])->asArray()->limit(intval($limit))->orderBy(['created_at' => SORT_DESC])->all();
    }

    public function getRecentActivity($limit = 5) {
        $model = new \wdmg\activity\models\Activity();
        return $model::find()->select('type, message, action, created_at, created_by')->asArray()->limit(intval($limit))->orderBy(['id' => SORT_DESC, 'created_at' => SORT_ASC])->all();
    }

    public function getRecentComments($limit = 5) {
        $model = new \wdmg\comments\models\Comments();
        return $model::find()->select('id, name, context, target, comment, created_at')->where(['status' => $model::COMMENT_STATUS_PUBLISHED])->asArray()->limit(intval($limit))->orderBy(['id' => SORT_DESC, 'created_at' => SORT_ASC])->all();
    }

    public function getRecentStats() {
        $model = new \wdmg\stats\models\VisitorsSearch();
        $dataProvider = $model->search(['period' => 'week']);
        $visitors = $dataProvider->query->all();

        $dateTime = new \DateTime('00:00:00', new \DateTimeZone(ini_get('date.timezone')));
        $timestamp = $dateTime->modify('+2 day')->getTimestamp();

        $format = 'd M';
        $metrik = 'days';
        $iterations = 7;
        $labels = [];

        foreach ($visitors as $visitor) {
            for ($i = 1; $i <= $iterations; $i++) {

                if($visitor->datetime <= strtotime('-'.$i.' '.$metrik, $timestamp) && $visitor->datetime > strtotime('-'.($i + 1).' '.$metrik, $timestamp))
                    $output1[$i][] = $visitor->datetime;

                if($visitor->unique == 1 && $visitor->datetime <= strtotime('-'.$i.' '.$metrik, $timestamp) && $visitor->datetime > strtotime('-'.($i + 1).' '.$metrik, $timestamp))
                    $output2[$i][] = $visitor->datetime;

            }
        }

        for ($i = 1; $i <= $iterations; $i++) {

            $labels[] = date($format, strtotime('-'.($i+1).' '.$metrik, $timestamp));

            if(isset($output1[$i]))
                $all_visitors[] = count($output1[$i]);
            else
                $all_visitors[] = 0;

            if(isset($output2[$i]))
                $unique_visitors[] = count($output2[$i]);
            else
                $unique_visitors[] = 0;
        }

        return [
            'labels' => array_reverse($labels),
            'datasets' => [
                [
                    'label'=> Yii::t('app/modules/stats', 'Views'),
                    'data' => array_values(array_reverse($all_visitors)),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)'
                    ],
                    'borderWidth' => 1
                ],
                [
                    'label'=> Yii::t('app/modules/stats', 'Visitors'),
                    'data' => array_values(array_reverse($unique_visitors)),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(255,99,132,1)'
                    ],
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    public function getRecentLoads() {
        $model = new \wdmg\stats\models\VisitorsSearch();
        $dataProvider = $model->search(['period' => 'week']);
        $visitors = $dataProvider->query->all();

        $timezone = \date_default_timezone_get();
        $dateTime = new \DateTime('00:00:00', new \DateTimeZone($timezone));
        $timestamp = $dateTime->modify('+2 day')->getTimestamp();

        $format = 'd M';
        $metrik = 'days';
        $iterations = 7;
        $labels = [];

        $elapsed_time_avrg = [];
        $memory_usage_avrg = [];
        $db_queries_avrg = [];
        $db_time_avrg = [];

        foreach ($visitors as $visitor) {
            for ($i = 0; $i <= $iterations; $i++) {
                if ($visitor->datetime <= strtotime('-'.$i.' '.$metrik, $timestamp) && $visitor->datetime > strtotime('-'.($i + 1).' '.$metrik, $timestamp)) {
                    $output[$i][] = $visitor->params;
                } else {
                    $output[$i][] = [];
                }
            }
        }

        for ($i = 1; $i <= $iterations; $i++) {

            $labels[] = date($format, strtotime('-'.($i+1).' '.$metrik, $timestamp));

            if (isset($output[$i])) {

                $et = 0;
                $mu = 0;
                $dbq = 0;
                $dbt = 0;
                $et_count = 0;
                $mu_count = 0;
                $dbq_count = 0;
                $dbt_count = 0;

                foreach ($output[$i] as $item) {
                    if (isset($item['et'])) {
                        $et += $item['et'];
                        $et_count++;
                    }
                    if (isset($item['mu'])) {
                        $mu += $item['mu'];
                        $mu_count++;
                    }
                    if (isset($item['dbq'])) {
                        $dbq += $item['dbq'];
                        $dbq_count++;
                    }
                    if (isset($item['dbt'])) {
                        $dbt += $item['dbt'];
                        $dbt_count++;
                    }
                }

                $elapsed_time_avrg[] = round((($et_count) ? ($et / $et_count) : $et), 4);
                $memory_usage_avrg[] = round((($mu_count) ? ($mu / $mu_count) : $mu), 2);
                $db_queries_avrg[] = round((($dbq_count) ? ($dbq / $dbq_count) : $dbq), 4);
                $db_time_avrg[] = round((($dbt_count) ? ($dbt / $dbt_count) : $dbt), 4);
            }
        }

        return [
            'labels' => array_reverse($labels),
            'datasets' => [
                [
                    'label'=> Yii::t('app/modules/stats', 'Elapsed time, sec.'),
                    'data' => array_reverse($elapsed_time_avrg),
                    'backgroundColor' => [
                        'rgba(118, 207, 41, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(101, 176, 34, 1)'
                    ],
                    'borderWidth' => 1
                ],
                [
                    'label'=> Yii::t('app/modules/stats', 'Memory usage, Mb'),
                    'data' => array_reverse($memory_usage_avrg),
                    'backgroundColor' => [
                        'rgba(251, 163, 35, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(213, 139, 29, 1)'
                    ],
                    'borderWidth' => 1
                ],
                [
                    'label'=> Yii::t('app/modules/stats', 'DB queries'),
                    'data' => array_reverse($db_queries_avrg),
                    'backgroundColor' => [
                        'rgba(65, 148, 226, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(50, 126, 192, 1)'
                    ],
                    'borderWidth' => 1
                ],
                [
                    'label'=> Yii::t('app/modules/stats', 'DB time, sec.'),
                    'data' => array_reverse($db_time_avrg),
                    'backgroundColor' => [
                        'rgba(146, 61, 253, 0.2)'
                    ],
                    'borderColor' => [
                        'rgba(124, 51, 215, 1)'
                    ],
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    public function actionInfo()
    {
        $this->layout = 'dashboard';
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['admin/login']);
        } else {
            return $this->render('info', [
                'data' => $this->getSystemData()
            ]);
        }
    }

    public function actionPhpinfo($cli = false)
    {
        $this->layout = null;
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['admin/login']);
        } else {
            if ($cli) {
                $data = $this->module->runConsole('php -i - phpinfo', true, true, false);
                return \nl2br($data[1]);
            } else {
                ob_start();
                \phpinfo();
                return ob_get_clean();
            }
        }
    }

    public function actionError()
    {

        if (Yii::$app->user->isGuest)
            $this->layout = 'welcome';
        else
            $this->layout = 'dashboard';

        $type = 'default';
        $exception = Yii::$app->errorHandler->exception;
        $response = Yii::$app->getResponse();
        $statuses = $response::$httpStatuses;
        $this->view->params['main']['options']['class'] = 'main bg';

        if (!$response->getIsInvalid()) {
            if ($response->getIsClientError() && $response->getIsServerError() && $response->getIsForbidden() && $response->getIsNotFound()) {
                $type = 'danger';
            } else if ($response->getIsInformational()) {
                $type = 'info';
            } else if ($response->getIsSuccessful() && $response->getIsOk()) {
                $type = 'success';
            } else if ($response->getIsRedirection() && $response->getIsEmpty()) {
                $type = 'warning';
            }
        }

        if ($exception !== null) {
            return $this->render('error', ['type' => $type, 'statuses' => $statuses, 'code' => ((isset($exception->statusCode)) ? $exception->statusCode : ""), 'message' => $exception->getMessage()]);
        }
    }

    private function getSystemLimits() {
        if (function_exists("posix_getrlimit")) {
            if (!$limits = posix_getrlimit()) {
                return null;
            } else {
                return $limits;
            }
        } else {
            return null;
        }
    }

    private function getUptime() {
        $output = $this->module->runConsole('uptime', true, true);
        if (isset($output[1])) {
            return $output[1];
        } else {
            return null;
        }
    }

    private function getDiskSpace() {
        return [
            'free_space' => \disk_free_space(Yii::getAlias("@app")),
            'total_space' => \disk_total_space(Yii::getAlias("@app"))
        ];
    }

    private function getMemoryUsage() {
        $mem = \memory_get_usage(true);
        if ($mem < 1024)
            $$memory = $mem .' B';
        elseif ($mem < 1048576)
            $memory = round($mem / 1024, 2) .' Kb';
        else
            $memory = round($mem / 1048576, 2) .' Mb';

        return $memory;
    }

    private function getSystemLoad($coreCount = 2, $interval = 1) {
        $rs = \sys_getloadavg();
        $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
        $load = floatval($rs[$interval]);

        if (intval($coreCount) !== 0)
            $load_avg = ($load * 100) / intval($coreCount);
        else
            $load_avg = 0;

        return round($load_avg, 2);
    }

    private function getCoresCount() {

        $output = $this->module->runConsole('uname', true, true);
        $os = \mb_strtolower(trim($output[1]));

        switch ($os) {
            case('linux'):
                $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
                break;
            case('freebsd'):
            case('darwin'):
                $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
                break;
            default:
                unset($cmd);
        }

        if (isset($cmd)) {
            $output = $this->module->runConsole($cmd, true, true, false);
            return intval(trim($output[1]));
        }

        return 1;
    }

    private function getDbVersion() {
        if ($db = Yii::$app->getDb()) {
            return [
                'type' => Yii::$app->getDb()->driverName,
                'version' => Yii::$app->getDb()->serverVersion
            ];
        } else {
            return null;
        }
    }

    private function getServerDatetime() {
        $timestamp = time();
        $timezone = \date_default_timezone_get();
        $date = new \DateTime('now', new \DateTimeZone($timezone));
        $datetime = $date->format('Y-m-d H:i:sP');
        return [
            'timezone' => $timezone,
            'datetime' => \Yii::$app->formatter->format($datetime, 'datetime')
        ];
    }

    /**
     * @param $name
     * @param $dsn
     * @return mixed|null
     */
    protected static function getDsnAttribute($name, $dsn) {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    /**
     * @param null $config
     * @return array|null
     * @throws \yii\db\Exception
     */
    private static function getDbStatus($config = null) {
        if (!is_null($config)) {
            try {
                $connection = new \yii\db\Connection($config);
                $connection->open();
                $host = self::getDsnAttribute('host', $connection->dsn);
                $dbname = self::getDsnAttribute('dbname', $connection->dsn);
                $node = $host . ":" .$dbname;
                return [
                    $node => [
                        'status' => ($connection->getIsActive()) ? 'ok' : 'inactive',
                        'driver' => $connection->getDriverName(),
                        'version' => $connection->getServerVersion()
                    ]
                ];
                $connection->close();
            } catch (\Exception $e) {
                $host = self::getDsnAttribute('host', (is_object($config)) ? $config->dsn : $config['dsn']);
                $dbname = self::getDsnAttribute('dbname', (is_object($config)) ? $config->dsn : $config['dsn']);
                $node = $host . ":" .$dbname;
                return [
                    $node => [
                        'status' => 'offline'
                    ]
                ];
            }
        }
        return null;
    }

    private function getActiveProcessesCount() {
        $system = [
            'httpd',
            'mysqld',
            'postgres',
            'apache2',
            'fcgi',
            'nginx',
            'smtpd',
            'qmqpd',
            'sendmail',
            'searchd',
            'indexer',
            'memcached',
            'crond',
            'node',
            'sh',
            'sshd',
            'ssh-agent',
            'bash',
            'syslogd',
            'rsyslogd',
            'uwsgi',
            'php',
            'php-cgi',
            'crontab',
            'perl',
            'ruby',
            'python',
        ];
        foreach ($system as $extension) {
            $output[$extension] = `pgrep ^$extension$ | wc -l`;
        }

        $loaded_extensions = array_values(\get_loaded_extensions());
        foreach ($loaded_extensions as $extension) {
            $output[$extension] = `pgrep ^$extension$ | wc -l`;
        }

        return array_map("trim", $output);
    }

    private function getDbPrimary() {
        $config = Yii::$app->getDb();
        $output = self::getDbStatus([
            'dsn' => $config->dsn,
            'username' => $config->username,
            'password' => $config->password
        ]);
        return $output;
    }

    private function getDbMasters() {
        $output = [];
        if ($masters = Yii::$app->db->masters) {
            foreach ($masters as $master) {
                $config = \yii\helpers\ArrayHelper::merge($master, Yii::$app->db->masterConfig);
                $output = array_merge($output, $this->getDbStatus($config));
            }
        }
        return $output;
    }

    private function getDbSlaves() {
        $output = [];
        if ($slaves = Yii::$app->db->slaves) {
            foreach ($slaves as $slave) {
                $config = \yii\helpers\ArrayHelper::merge($slave, Yii::$app->db->slaveConfig);
                $output = array_merge($output, $this->getDbStatus($config));
            }
        }
        return $output;
    }

    private function getSystemData() {

        $processes = $this->getActiveProcessesCount();
        $data = [
            'phpVersion' => PHP_VERSION,
            'yiiVersion' => Yii::getVersion(),
            'application' => [
                'id' => Yii::$app->id,
                'name' => Yii::$app->name,
                'version' => Yii::$app->version,
                'host' => \yii\helpers\Url::base(true),
                'language' => Yii::$app->language,
                'sourceLanguage' => Yii::$app->sourceLanguage,
                'i18n' => (function_exists('locale_get_default')) ? \locale_get_default() : null,
                'charset' => Yii::$app->charset,
                'env' => YII_ENV,
                'debug' => YII_DEBUG,
            ],
            'db' => [
                'primary' => $this->getDbPrimary(),
                'masters' => $this->getDbMasters(),
                'slaves' => $this->getDbSlaves(),
            ],
            'php' => [
                'version' => PHP_VERSION,

                'openssl' => extension_loaded('openssl'),
                'curl' => extension_loaded('curl'),
                'imap' => extension_loaded('imap'),
                'simplexml' => extension_loaded('simplexml'),

                'ftp' => extension_loaded('ftp'),
                'ssh2' => extension_loaded('lib'),
                'exif' => extension_loaded('exif'),

                'soap' => extension_loaded('soap'),
                'sockets' => extension_loaded('sockets'),

                'uploadprogress' => extension_loaded('uploadprogress'),
                'oauth' => extension_loaded('oauth'),
                'gmp' => extension_loaded('gmp'),

                'zip' => extension_loaded('zip'),
                'zlib' => extension_loaded('zlib'),
                'pdflib' => extension_loaded('pdflib'),

                'xdebug' => extension_loaded('xdebug'),

                'apc' => extension_loaded('apc'),
                'apcu' => extension_loaded('apcu'),
                'memcache' => extension_loaded('memcache'),
                'memcached' => extension_loaded('memcached'),
                'opcache' => extension_loaded('opcache'),

                'iconv' => extension_loaded('iconv'),
                'intl' => extension_loaded('intl'),
                'geoip' => extension_loaded('geoip'),

                'imagick' => extension_loaded('imagick'),
                'ffmpeg' => extension_loaded('ffmpeg'),
                'gd' => extension_loaded('gd'),
                'smtp' => strlen(ini_get('SMTP')) > 0,

                'expose_php' => (empty(ini_get('expose_php'))),
                'allow_url_include' => (empty(ini_get('allow_url_include'))),

            ],

            'server' => Yii::$app->getRequest()->getServerName(),
            'host' => \yii\helpers\Url::base(true),

            'php_sapi_name' => php_sapi_name(),

            'ip' => (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : null,
            'port' => Yii::$app->getRequest()->getServerPort() . "/" . Yii::$app->getRequest()->getSecurePort(),

            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => $this->getMemoryUsage(),

            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),

            'max_input_time' => ini_get('max_input_time'),
            'max_execution_time' => ini_get('max_execution_time'),
            
            'file_limit' => min(StringHelper::sizeToBytes(ini_get('upload_max_filesize')), StringHelper::sizeToBytes(ini_get('post_max_size')), StringHelper::sizeToBytes(ini_get('memory_limit'))),
            'total_limit' => min(StringHelper::sizeToBytes(ini_get('post_max_size')), StringHelper::sizeToBytes(ini_get('memory_limit'))),

            'client' => Yii::$app->getRequest()->getUserIP() .
                ((Yii::$app->getRequest()->getUserHost()) ? " (" . Yii::$app->getRequest()->getUserHost() . ") " : "") .
                ((Yii::$app->getRequest()->getUserAgent()) ? ", " . Yii::$app->getRequest()->getUserAgent() : ""),

            'protocol' => Yii::$app->getResponse()->version,

            'charset' => Yii::$app->getResponse()->charset,
            'language' => ((isset(Yii::$app->sourceLanguage)) ? Yii::$app->sourceLanguage : "") . ((isset(Yii::$app->language)) ? "/" .Yii::$app->language : ""),

            'engine' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '',
            'dbVersion' => $this->getDbVersion(),
            'extensions' => Yii::$app->extensions,
            'components' => Yii::$app->getComponents(),
            'datetime' => $this->getServerDatetime(),
            'limits' => $this->getSystemLimits(),
            'system_load' => $this->getSystemLoad($this->getCoresCount()),
            'uptime' => $this->getUptime(),
            'disk_space' => $this->getDiskSpace(),
            'params' => Yii::$app->params,
            'processes' => $processes
        ];

        $data['sphinx'] = null;
        if (isset(Yii::$app->sphinx) && array_key_exists('searchd', $processes)) {
            try {
                if ($sphinx = Yii::$app->sphinx->createCommand('SHOW STATUS;')->queryAll()) {
                    $data['sphinx'] = \wdmg\helpers\ArrayHelper::map($sphinx, 'Counter', 'Value');
                }
            } catch (\Exception $e) {
                $data['sphinx']['error'] = $e->getMessage();
            }
        }

        return $data;
    }


    private function getNewUsersCount() {
        $counts = \wdmg\users\models\Users::getStatsCount(true, true);
        return [
            'count' => ($counts['count']) ? "+" . $counts['count'] : "0",
            'online' => ($counts['online']) ? $counts['online'] : "n/a",
            'total' => ($counts['total']) ? $counts['total'] : "n/a",
        ];
    }

    private function getNewOrdersCount() {
        $counts = \wdmg\store\models\Orders::getStatsCount(true);
        return [
            'count' => ($counts['count']) ? "+" . $counts['count'] : "0",
            'total' => ($counts['total']) ? $counts['total'] : "n/a",
        ];
    }

    private function getNewCommentsCount() {
        $counts = \wdmg\comments\models\Comments::getStatsCount(true);
        $counts2 = \wdmg\reviews\models\Reviews::getStatsCount(true);
        return [
            'count' => ($counts['count'] || $counts2['count']) ? "+" . intval($counts['count']) + intval($counts2['count']) : "0",
            'comments' => ($counts['total']) ? $counts['total'] : "n/a",
            'reviews' => ($counts2['total']) ? $counts2['total'] : "n/a",
            'total' => ($counts['total'] || $counts2['total']) ? intval($counts['total']) + intval($counts2['total']) : "n/a"
        ];
    }

    private function getNewTransactionsCount() {
        $counts = \wdmg\billing\models\Transactions::getStatsCount(true);
        return [
            'count' => ($counts['count']) ? "+" . $counts['count'] : "0",
            'total' => ($counts['total']) ? $counts['total'] : "n/a",
        ];
    }

    private function getNewNewslettersCount() {
        $counts = \wdmg\newsletters\models\Newsletters::getStatsCount(true);
        return [
            'count' => ($counts['count']) ? "+" . $counts['count'] : "0",
            'total' => ($counts['total']) ? $counts['total'] : "n/a",
        ];
    }

    private function getNewSubscribersCount() {
        $counts = \wdmg\subscribers\models\Subscribers::getStatsCount(true);
        return [
            'count' => ($counts['count']) ? "+" . $counts['count'] : "0",
            'total' => ($counts['total']) ? $counts['total'] : "n/a",
        ];
    }

}
