<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use yii\helpers\Html;

Pjax::begin([
    'dataProvider'=>$dataProvider
]);
?>
<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],
    'columns' => [
        'code',
        'discount' => [
            'attribute' => 'discount',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
        ],
        'used' => [
            'attribute' => 'used',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                $used = ($model->used > 0) ? '<span class="text-success">' . $model->used . '</span>' : $model->used;
                $max_use = '<span class="text-danger">' . $model->max_use . '</span>';
                return $used . '/' . $max_use;
            }
        ],
        'categories' => [
            'attribute' => 'categories',
            'format' => 'raw',
            'headerOptions' => ['style' => 'width:150px', 'class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {

                return null;
            }
        ],
        'created_at' => [
            'attribute' => 'created_at',
            'class' => 'panix\engine\grid\columns\jui\DatepickerColumn',
        ],
        'updated_at' => [
            'attribute' => 'updated_at',
            'class' => 'panix\engine\grid\columns\jui\DatepickerColumn',
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {delete}',
        ]
    ]
]);
?>
<?php Pjax::end(); ?>