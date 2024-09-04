<?php

use app\models\Products;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Витрина';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="products-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class = "user-cash"><h3>Ваш баланс: <br><?=Yii::$app->user->identity->cash?></h3></div>


    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
           'class' =>  yii\bootstrap5\LinkPager::class
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'название',
                'attribute' => 'title',
            ],
            [
                'label' => 'Описание',
                'attribute' => 'description',
            ],
            [

                'label' => 'цена',
                'attribute' => 'price',
            ],
            [
                'label' => 'Добавить в корзину',
                'format' => 'html', 
                'value' => fn ($model) => Html::a('добавить', ['', 'product_id' => $model->id], ['class' => 'btn btn-primary','name' => 'product'])
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?=Html::a('Заказать', '/orders/make-order', ['class'=>'btn btn-primary'])?>
