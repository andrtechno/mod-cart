<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;


Pjax::begin([
    'dataProvider' => $dataProvider
]);

echo GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],

    'columns' => [
        'name',
        [
            'attribute' => 'ordersCount',
            'format' => 'html',
            'header' => 'Заказов',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return Html::a($model->ordersCount, ['/admin/cart/default/index', 'OrderSearch[status_id]' => $model->id]);
            }
        ],
        [
            'attribute' => 'color',
            'format' => 'html',
            'contentOptions' => function ($model, $index, $widget, $grid) {
                return ['style' => 'background-color:' . $model->color . ';', 'class' => 'text-center'];
            },
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
Pjax::end();
