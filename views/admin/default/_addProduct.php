<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderProductSearch;

$searchModel = new ProductSearch();
$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

$dataProvider->pagination->pageSize = 10;
$dataProvider->pagination->route = '/admin/cart/default/add-product-list';


Pjax::begin([
    'id' => 'pjax-container-productlist',
   // 'clientOptions' => ['method' => 'POST'],
   // 'enablePushState' => false,
    //'linkSelector' => 'a:not(.linkTarget)'
]);

echo GridView::widget([
    'filterUrl' => ['/admin/cart/default/add-product-list', 'id' => $model->id],
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'enableLayout'=>false,
    //'filterModel' => $searchModel,

    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function ($model) {
                /** @var \panix\mod\shop\models\Product $model */
                return $model->renderGridImage();
            },
        ],
        'name',
        'sku',
        [
            'attribute' => 'price',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                /** @var \panix\mod\shop\models\Product $model */
                return Html::textInput("price_{$model->id}", $model->price, ['id' => "price_{$model->id}", 'class' => 'form-control']);
            }
        ],
        [
            'attribute' => 'quantity',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                /** @var \panix\mod\shop\models\Product $model */
                return \yii\jui\Spinner::widget([
                    'id' => "count_{$model->id}",
                    'name' => "count_{$model->id}",
                    'value' => 1,
                    'clientOptions' => ['max' => 999],
                    'options' => ['class' => 'cart-spinner']
                ]);
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{add}',
            'buttons' => [
                'add' => function ($url, $data, $key) {
                    return Html::a('<i class="icon-add"></i>', $data->id, [
                        'title' => Yii::t('yii', 'VIEW'),
                        'class' => 'btn btn-sm btn-success addProductToOrder',
                        'onClick' => 'return addProductToOrder(this, ' . Yii::$app->request->get('id') . ');'
                    ]);
                }
            ]
        ]
    ]
]);
Pjax::end();
