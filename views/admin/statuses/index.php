<?php


use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\engine\CMS;

Pjax::begin([
    'dataProvider'=>$dataProvider
]);

echo GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName],
    'rowOptions' => function ($model, $index, $widget, $grid) {
        return ['style' => 'background-color:' . $model->color . ';'];
    },
    'columns' => [
        'name',
        'color',
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
Pjax::end();
