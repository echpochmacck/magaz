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
            ]

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<div><?php if (!empty(yii::$app->session->get('carzina'))): ?>
        Сумма заказа:
        <div class="sum"><?= Orders::checkSum(yii::$app->session->get('carzina')) ?></div>
        <?php if (Orders::checkSum(yii::$app->session->get('carzina')) <= Yii::$app->user->identity->cash): ?>
        <?=Html::a('заказать', '/orders/save-order', ['class' => 'btn btn-success'])?>
        <?php else: ?>
            Не хватает денег
    <?php endif ?>
    <?php else: ?>
        карзина пуста
    <?php endif ?>
</div>