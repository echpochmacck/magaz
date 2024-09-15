<?php

use app\models\Orders;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Количество',
                'value' => 'quantity',

            ],
            [
                'label' => 'Название',
                'value' => 'title',
            ],
            [
                'label' => 'Добавить в корзину',
                'format' => 'html',
                'value' => fn($model) => Html::a('добавить', ['', 'product_id' => $model['id']], ['class' => 'btn btn-primary product-order '])
            ],
            [
                'label' => 'Удалить из корзины',
                'format' => 'html',
                'value' => fn($model) => Html::a('Удалить', ['', 'product_id' => $model['id'], 'delete' => 1], ['class' => 'btn btn-danger product-order delete-order'])
            ]

        ],
    ]); ?>


</div>

<?php Pjax::end(); ?>
Сумма заказа:
<div class="sum"><?= $sum ?></div>
<div><?php if ((!empty(yii::$app->session->get('carzina')))): ?>
        <?php if (Orders::checkSum(yii::$app->session->get('carzina')) <= Yii::$app->user->identity->cash): ?>
            <?= Html::a('заказать', '/orders/save-order', ['class' => 'btn btn-success']) ?>

        <?php else: ?>
            Не хватает денег
        <?php endif ?>
        <?= Html::a('Очистить корзину', '/orders/delete-corzina', ['class' => 'btn btn-danger']) ?>
    <?php endif ?>
</div>