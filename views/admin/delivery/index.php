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
        [
            'attribute' => 'system',
            'header' => Yii::t('cart/Delivery', 'SYSTEM'),
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'filter' => $searchModel->getDeliverySystemsArray(),
            'filterInputOptions' => ['class' => 'form-control', 'prompt' => html_entity_decode('&mdash;')],
            'value' => function ($model) {
                /** @var $model self */
                if($model->system){
                    return $model->getDeliverySystemsArray()[$model->system];
                }


            },
        ],
        'description:html',
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
Pjax::end();