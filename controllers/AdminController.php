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
                    'logout' => ['POST'],
                ],
            ],
            /*'access' => [
                'class' => AccessControl::className(),
                'except' => ['login'],
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ]
                ],
            ],*/
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'except' => ['login'],
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

    /**
     * Auth action
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
