<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Products;
use app\models\User;
use yii\data\ActiveDataProvider;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        if (!Yii::$app->user->isGuest) {

            if (!Yii::$app->session->has('carzina')) {
                Yii::$app->session->set('carzina', []);
            }
            $carzina = Yii::$app->session->get('carzina');


            
            if ($product_id = Yii::$app->request->get('product_id')) {
                if (array_key_exists($product_id, $carzina)) {
                    $carzina[$product_id]['quantity'] += 1;
                } else {
                    $carzina[$product_id] = [];
                    $carzina[$product_id]['quantity'] = 1;
                    $carzina[$product_id]['price'] = Products::findOne($product_id)->price;
                    $carzina[$product_id]['title'] = Products::findOne($product_id)->title;
                    $carzina[$product_id]['id'] = Products::findOne($product_id)->id;
                }
                Yii::$app->session->set('carzina', $carzina);
            }


            $dataProvider = new ActiveDataProvider([
                'query' => Products::getShowcase(),
                'pagination' => [
                    'pageSize' => 2,
                ]
            ]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->run('register');
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
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
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionRegister()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->register();
                Yii::$app->user->login($model);
                Yii::$app->session->setFlash('success', 'Успешна зарегался');
                return $this->goHome();
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }
}
