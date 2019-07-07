<?php

namespace wdmg\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
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
