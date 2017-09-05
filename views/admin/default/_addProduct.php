<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use panix\mod\shop\models\search\ShopProductSearch;

?>


<?php

//   if (!isset($dataProvider))
//    $dataProvider = new ShopProduct('search');
// Fix sort url
//   $dataProvider = $dataProvider->search();
$searchModel = new ShopProductSearch();
$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

 $dataProvider->pagination->pageSize=1;
 
/**
 * Add new product to order.
 * Display products list.
 */
//if (!isset($dataProvider))
//  $dataProvider = new ShopProduct('search');
// Fix sort url
// $dataProvider = $dataProvider->search();
// $dataProvider->sort->route = 'addProductList';
$dataProvider->pagination->route = '/admin/cart/default/add-product-list';


/*
  $columns = array();
  $columns[] = array(
  'class' => 'IdColumn',
  'name' => 'id',
  'type' => 'text',
  'value' => '$data->id',
  'filter' => false
  );
  $columns[] = array(
  'type' => 'raw',
  'value' => 'Html::link(Html::image($data->getMainImageUrl("50x50"),$data->name,array("class"=>"img-thumbnail")))'
  );
  $columns[] = array(
  'name' => 'name',
  'type' => 'raw',
  );
  $columns[] = array(
  'name' => 'sku',
  'value' => '$data->sku',
  );
  $columns[] = array(
  'type' => 'raw',
  'name' => 'price',
  'value' => 'Html::textField("price_{$data->id}", $data->price, array("class"=>"form-control","style"=>"text-align:center;width:80px;border:1px solid silver;padding:1px;"))',
  );
  $columns[] = array(
  'type' => 'raw',
  'value' => 'Html::textField("count_{$data->id}", 1, array("class"=>"spinner form-control"))',
  'header' => Yii::t('cart/OrderProduct', 'QUANTITY'),
  );

  $columns[] = array(
  'class' => 'CLinkColumn',
  'header' => '',
  'linkHtmlOptions' => array('class' => 'btn btn-success'),
  //'type' => 'raw',
  'label' => '<i class="icon-add"></i>',
  // 'value' => 'Html::link("<i class=\"icon-add\"></i>", "#", array("class"=>"btn btn-success","onclick"=>"addProductToOrder(this, ' . $model->id . ');"))',
  'urlExpression' => '$data->id',
  'htmlOptions' => array(
  'class' => 'addProductToOrder',
  'onClick' => 'return addProductToOrder(this, ' . $model->id . ');'
  ),
  );
 */
$orderId = $model->id;

Pjax::begin([
    'id' => 'pjax-container-productlist',
    //'enablePushState' => false,
    //'linkSelector' => 'a:not(.linkTarget)'
]);

echo GridView::widget([
    'filterUrl'=>['/admin/cart/test'],
   // 'id' => 'orderedProducts',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
     'filterModel' => $searchModel,
    // 'layout' => $this->render('@app/web/themes/admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]), //'{items}{pager}{summary}'
    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function($data) {
        return $data->renderGridImage('50x50');
    },
        ],
        'name',
            'sku',
        [
            'attribute' => 'price',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function($data) {
        return Html::textInput("price_{$data->id}", $data->price,['id'=>"price_{$data->id}"]);
    }
        ],
        [
            'attribute' => 'quantity',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function($data) {
        return \yii\jui\Spinner::widget([
            'id'=>"count_{$data->id}",
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
                                'class' => 'btn btn-sm btn-info addProductToOrder',
                        'onClick' => 'return addProductToOrder(this, ' . Yii::$app->request->get('id') . ');'
                    ]);
                }
                    ]
        ]
    ]
]);
Pjax::end();


/*  $this->widget('ext.adminList.GridView', array(
  'filter' => $dataProvider->model,
  'enableHeader' => false,
  'autoColumns' => false,
  'dataProvider' => $dataProvider,
  //'ajaxType'=>'POST',
  'ajaxUrl' => Yii::app()->createUrl('/cart/admin/default/addProductList', array('id' => $model->id)),
  'selectableRows' => 0,
  'columns' => $columns,
  'template' => '{items}',
  )); */
?>

