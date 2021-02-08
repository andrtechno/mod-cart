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
        [
            'class' => \panix\engine\grid\sortable\Column::class,
        ],
        [
            'attribute' => 'name',
            'format' => 'html',
            'value' => function ($model) {
                $function = '';
                if ($model->id == \panix\mod\cart\models\Order::STATUS_SUBMITTED) {
                    $function .= '<br/><span class="text-warning"><i class="icon-warning"></i> На этот статус производиться <strong>начисление</strong> бонусов</span>';
                } elseif ($model->id == \panix\mod\cart\models\Order::STATUS_RETURN) {
                    $function .= '<br/><span class="text-warning"><i class="icon-warning"></i> На этот статус производиться <strong>снятие</strong> бонусов</span>';
                }


                return $model->name . $function;
            }
        ],
        [
            'attribute' => 'ordersCount',
            'format' => 'html',
            'header' => Yii::t('cart/OrderStatus', 'COUNT'),
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
