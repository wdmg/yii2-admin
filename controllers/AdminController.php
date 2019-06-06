<?php

namespace wdmg\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use wdmg\users\models\UsersSignin;
//use wdmg\users\models\UsersSignup;
use wdmg\users\models\UsersPasswordRequest;
use wdmg\users\models\UsersResetPassword;

/**
 * AdminController implements the CRUD actions for API model.
 */
class AdminController extends Controller
{

    public $defaultAction = 'index';
    public $layout = 'auth';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ], [
                        'roles' => ['?'],
                        'allow' => false
                    ], [
                        'roles' => ['admin'],
                        'actions' => ['logout'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get', 'post'],
                    'logout' => ['post'],
                ],
            ],
        ];
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
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['admin/index']);

        $model = new UsersSignin();
        if ($model->load(Yii::$app->request->post())) {
            try {

                if($model->login()) {
                    Yii::$app->activity->set( 'User has successfully login.', 'login', 'info', 2);
                    return $this->goBack();
                }

            } catch (\DomainException $error) {
                Yii::$app->session->setFlash('error', $error->getMessage());
                return $this->redirect(['admin/index']);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

}
