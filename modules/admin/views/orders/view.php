<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="orders-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'status',
            'order_date',
            'sum',
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $goods, 
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'], 
            [
                'attribute' => 'product_id',
                'label' => 'Product',
                'value' => function ($model) {
                    return $model->product_title; 
                },
            ],
            'quantity', 

        ],
    ]); ?>


</div>