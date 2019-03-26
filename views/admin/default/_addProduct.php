<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderProductSearch;

?>


<?php

//   if (!isset($dataProvider))
//    $dataProvider = new Product('search');
// Fix sort url
//   $dataProvider = $dataProvider->search();
$searchModel = new ProductSearch();
$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

$dataProvider->pagination->pageSize = 10;
$dataProvider->pagination->route = '/admin/cart/default/add-product-list';


Pjax::begin([
    'id' => 'pjax-container-productlist',
    'clientOptions' => ['method' => 'POST'],
    'enablePushState' => false,
    //'linkSelector' => 'a:not(.linkTarget)'
]);

echo GridView::widget([
    'filterUrl' => ['/admin/cart/default/add-product-list', 'id' => $model->id],
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,

    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function ($data) {
                return $data->renderGridImage('50x50');
            },
        ],
        'name',
        'sku',
        [
            'attribute' => 'price',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return Html::textInput("price_{$data->id}", $data->price, ['id' => "price_{$data->id}", 'class' => 'form-control']);
            }
        ],
        [
            'attribute' => 'quantity',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($data) {
                return \yii\jui\Spinner::widget([
                    'id' => "count_{$data->id}",
                    'name' => "count_{$data->id}",
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
