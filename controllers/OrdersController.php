<?php

namespace app\controllers;

use app\models\Orders;
use app\models\OrdersSearch;
use app\models\Sostav;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Orders models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, Yii::$app->user->identity->id);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Orders();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionMakeOrder()
    {
        if (!Yii::$app->user->isGuest) {
            $carzina = Yii::$app->session->get('carzina');
            $dataProvider = new ArrayDataProvider([
                'models' => $carzina
            ]);
            return $this->render('make-order', [
                'dataProvider' => $dataProvider
            ]);
        } else {
            return $this->goHome();
        }
    }

    public function actionSaveOrder()
    {
        if (!Yii::$app->user->isGuest) {
            $carzina = Yii::$app->session->get('carzina');
            if ($carzina && !empty($carzina) && Orders::checkSum($carzina) <= Yii::$app->user->identity->cash) {
                $order = new Orders();
                $order->user_id = Yii::$app->user->identity->id;
                $order->status = 'в ожидании';
                $order->save();

                foreach ($carzina as $key => $value) {
                    $model = new Sostav();
                    $model->order_id = $order->id;
                    $model->product_id = $key;
                    $model->quantity = $value['quantity'];
                    $model->save(false);
                }
                $user = User::findOne(Yii::$app->user->identity->id);
                $user->cash = Yii::$app->user->identity->cash - Orders::checkSum($carzina);
                $user->save(false);
                Yii::$app->session->remove('carzina');
                Yii::$app->session->setFlash('success', 'заказ сделан');
            }
        }
        return $this->goHome();
    }
}
