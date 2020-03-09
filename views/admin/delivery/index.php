<?php

use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;


Pjax::begin([
    'dataProvider'=>$dataProvider
]);
echo GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => [
        'title' => $this->context->pageName,
        'buttons' => [
            [
                'icon' => 'add',
                'label' => Yii::t('app/default', 'CREATE'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-sm btn-success']
            ]
        ]
    ],
    'columns' => [
        [
            'class' => '\panix\engine\grid\sortable\Column',
        ],
        'name',
        'description:html',
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
Pjax::end();