<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\cart\models\Order;

/**
 * @var $this \yii\web\View
 */
\panix\mod\cart\HammerAsset::register($this);
?>
<?= Html::beginForm('/admin/cart/default/pdf-orders', 'GET',['id'=>'filter-cart-form']); ?>
<?php echo $this->render('_filter_pdf'); ?>
<?= Html::endForm(); ?>
<?php
$this->registerJs('
$(document).on("click", "#collapse-grid-filter button" , function(event,k) {
    var data = $("#grid-orders").yiiGridView("data");
    console.log(data.settings.filterUrl,data.settings.filterSelector);
    $.pjax({
        url: data.settings.filterUrl,
        container: \'#pjax-grid-orders\',
        type:"GET",
        push:false,
        timeout:false,
        scrollTo:false,
        data:$("#collapse-grid-filter input, #collapse-grid-filter select").serialize()
    });
    return false;
});

$("#grid-orders tr[data-url]").hammer({}).bind("doubletap", function(e){
	$("#grid-orders").addClass("pjax-loader");
	 window.location.href = $(this).data("url");
});

');
Pjax::begin(['id' => 'pjax-grid-orders']);


$filterCount = 0;
if (isset($searchModel->call_confirm) && !empty($searchModel->call_confirm)) {
    $filterCount += 1;
}
if (isset($searchModel->paid) && !empty($searchModel->paid)) {
    $filterCount += 1;
}
if (isset($searchModel->delivery_city) && !empty($searchModel->delivery_city)) {
    $filterCount += 1;
}
if (isset($searchModel->buyOneClick) && !empty($searchModel->buyOneClick)) {
    $filterCount += 1;
}
if (isset($searchModel->status_id) && !empty($searchModel->status_id)) {
    $filterCount += 1;
}
if (isset($searchModel->apply_user_points) && !empty($searchModel->apply_user_points)) {
    $filterCount += 1;
}
if (isset($searchModel->delivery_address) && !empty($searchModel->delivery_address)) {
    $filterCount += 1;
}
if (isset($searchModel->ttn) && !empty($searchModel->ttn)) {
    $filterCount += 1;
}


echo GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'showFooter' => true,
    'id' => 'grid-orders',
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    //'rowOptions' => function ($model, $index, $widget, $grid) {
    //    return ['style' => 'background-color:' . $model->status->color . ';'];
    //},
    'rowOptions' => function ($model, $index, $widget, $grid) {
        if (\panix\engine\CMS::isMobile()) {
            return ['data-url' => \yii\helpers\Url::to(['update', 'id' => $model->id])];
        }
        return [];
    },
    'layoutOptions' => [
        'title' => $this->context->pageName,
        'beforeContent' => $this->render('_grid_filter', ['model' => $searchModel]),
        'buttons' => [
            [
                'label' => Html::icon('filter') . (($filterCount) ? '<span class="badge badge-danger" style="font-size:75%">' . $filterCount . '</span>' : ''),
                'url' => '#collapse-grid-filter',
                'options' => [
                    'data-toggle' => "collapse",
                    'aria-expanded' => "false",
                    'aria-controls' => "collapse-grid-filter",
                    'class' => 'btn btn-sm btn-outline-secondary'
                ]
            ]
        ],
    ],
    /*  'columns' => [
          [
              'class' => 'panix\engine\grid\columns\CheckboxColumn',
          ],
          [
              'attribute' => 'id',
              'header' => Yii::t('cart/Order', 'ORDER_ID'),
              'format' => 'html',
              'contentOptions' => ['class' => 'text-left'],
              'value' => function ($model) {
                  return $model->getGridStatus() . ' ' . $model::t('NEW_ORDER_ID', ['id' => \panix\engine\CMS::idToNumber($model->id)]);
              }
          ],
          'user_name',
          [
              'attribute' => 'user_email',
              'format' => 'email',
              'contentOptions' => ['class' => 'text-center'],
          ],
          [
              'attribute' => 'user_phone',
              'format' => 'raw',
              'contentOptions' => ['class' => 'text-center'],
              'value' => function ($model) {

                  return ($model->user_phone) ? Html::tel($model->user_phone) : $model->user_phone;
              }
          ],
          [
              'attribute' => 'total_price',
              'format' => 'html',
              'class' => 'panix\engine\grid\columns\jui\SliderColumn',
              'max' => (int)Order::find()->aggregateTotalPrice('MAX'),
              'min' => (int)Order::find()->aggregateTotalPrice('MIN'),
              'prefix' => '<sup>' . Yii::$app->currency->main['symbol'] . '</sup>',
              'contentOptions' => ['class' => 'text-center'],
              'minCallback' => function ($value) {
                  return Yii::$app->currency->number_format($value);
              },
              'maxCallback' => function ($value) {
                  return Yii::$app->currency->number_format($value);
              },
              'value' => function ($model) {

                  $priceHtml = Yii::$app->currency->number_format(Yii::$app->currency->convert($model->total_price));
                  $symbol = Html::tag('sup', Yii::$app->currency->main['symbol']);
                  return Html::tag('span', $priceHtml, ['class' => 'text-success font-weight-bold']) . ' ' . $symbol;
              }
          ],
          [
              'class' => 'panix\engine\grid\columns\ActionColumn',
              'template' => '{view} {print} {update}',
              'buttons' => [
                  'print' => function ($url, $model, $key) {
                      return Html::a(Html::icon('print'), ['print', 'id' => $model->id], [
                          'title' => Yii::t('cart/admin', 'ORDER_PRINT'),
                          'class' => 'btn btn-sm btn-info',
                          'data-pjax' => 0,
                          'target' => '_blank'
                      ]);
                  },
                  'view' => function ($url, $model, $key) {
                      return Html::a(Html::icon('search'), $model->getUrl(), [
                          'title' => Yii::t('cart/admin', 'ORDER_VIEW'),
                          'class' => 'btn btn-sm btn-outline-secondary',
                          'data-pjax' => 0,
                          'target' => '_blank'
                      ]);
                  }
              ]
          ]
      ]*/
]);
?>


<?php Pjax::end(); ?>
<?php
$this->registerJs("
$(document).on('submit','#filter-cart-form',function(){
    var that = this;
    
    $('[name=\"selection[]\"]').each(function () {
        if (this.checked) {
            $('<input>', {
                type: 'hidden',
                id: 'input-id-'+$(this).val(),
                name: 'ids[]',
                value: $(this).val()
            }).appendTo(that);
        }
    });
    
    return true;
});
");
