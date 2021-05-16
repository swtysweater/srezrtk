<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\RequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Новая заявка', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute' => 'categoryID',
                'value' => function($model)
                {
                    return $model->category->name.' ('.$model->categoryID.')'; // Отображение названия категории с ID
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'statusID',
                'value' => function($model)
                {
                    return $model->status->name.' ('.$model->statusID.')'; // Отображение названия статуса с ID
                },
                'format' => 'html'
            ],
            'reject_msg',
            //'img_before',
            [
                'attribute' => 'img_before',
                'value' => function($model)
                {   
                    return Html::img('/'.$model->img_before, ['width' => 100]); // Отображение изображения ДО
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'img_after',
                'value' => function($model)
                {   
                    return Html::img('/'.$model->img_after, ['width' => 100]); // Отображение изображения ПОСЛЕ
                },
                'format' => 'html'
            ],
            //'img_after',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
