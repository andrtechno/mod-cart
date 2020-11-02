<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\cart\models\OrderProduct;
use yii\helpers\Html;

/**
 * @var \panix\mod\cart\models\Order $model
 */
$symbol = Yii::$app->currency->active['symbol'];

Pjax::begin([
    'id' => 'pjax-container-products',
    // 'enablePushState' => false,
    // 'linkSelector' => 'a:not(.linkTarget)'
]);

echo GridView::widget([
    //  'id' => 'orderedProducts',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $model->getOrderedProducts(),
    // 'filterModel' => $searchModel,
    'showFooter' => true,
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    'layoutOptions' => [
        'title' => Yii::t('cart/admin', 'ORDER_PRODUCTS'),
        'buttons' => [
            [
                'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
                'url' => 'javascript:openAddProductDialog(' . $model->id . ');',
                'options' => ['class' => 'btn btn-success btn-sm']
            ]
        ]
    ],
    'columns' => [
        'image' => [
            'class' => 'panix\engine\grid\columns\ImageColumn',
            'attribute' => 'image',
            'header' => Yii::t('cart/OrderProduct', 'IMAGE'),
            // 'filter'=>true,
            'value' => function ($model) {
                /** @var $model OrderProduct */
                return $model->getProductImage();
            },
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                /** @var $model OrderProduct */
                if ($model->currency_id) {
                    $priceValue = Yii::$app->currency->convert($model->price, $model->currency_id);
                } else {
                    $priceValue = $model->price;
                }

                $variantsConfigure = '';
                if ($model->variantsConfigure) {
                    foreach ($model->variantsConfigure as $configure) {
                        $variantsConfigure .= "<div>{$configure->name}: <strong>{$configure->value}</strong></div>";
                    }
                }
                /*$productName = $model->name;
                if ($model->configurable_name) {
                    $productName = $model->configurable_name;
                    if($model->id != $model->configurable_id){
                        $productName.= $model->configureProduct->id;
                    }

                }*/
                $price = Yii::$app->currency->number_format($priceValue) . ' ' . Yii::$app->currency->main['symbol'];
                return $model->getProductName() . '<br/>' . $variantsConfigure . $price;
            },
        ],
        [
            'attribute' => 'quantity',
            'footer' => $model->productsCount,
            'contentOptions' => ['class' => 'text-center quantity'],

        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'footer' => Yii::$app->currency->number_format($model->total_price) . ' ' . Yii::$app->currency->main['symbol'],
            'value' => function ($model) {
                return Yii::$app->currency->number_format($model->price) . ' ' . Yii::$app->currency->main['symbol'];
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $data, $key) {
                    return Html::a('<i class="icon-delete"></i>', '#', [
                        'title' => Yii::t('app/default', 'DELETE'),
                        'class' => 'btn btn-sm btn-danger',
                        'onClick' => "return deleteOrderedProduct($data->id, $data->order_id);"
                    ]);
                }
            ]
        ]
    ]
]);
Pjax::end();

?>


<div class="panel-container">
    <ul class="list-group">
        <?php if ($model->delivery_price > 0) { ?>
            <li class="list-group-item">
                <?= Yii::t('cart/Order', 'DELIVERY_PRICE') ?>: <strong
                        class="float-right"><?= Yii::$app->currency->number_format($model->delivery_price); ?> <?= $symbol; ?></strong>
            </li>
        <?php } ?>
        <li class="list-group-item">
            <?= Yii::t('cart/default', 'ORDER_PRICE') ?>: <strong
                    class="float-right"><?= Yii::$app->currency->number_format($model->total_price) ?> <span
                        class="text-muted"><?= $symbol ?></span></strong>
        </li>
        <?php if ($model->delivery_price > 0) { ?>
            <li class="list-group-item">
                <?= Yii::t('cart/default', 'DELIVERY_PRICE') ?>: <strong
                        class="float-right"><?= Yii::$app->currency->number_format($model->delivery_price) ?> <span
                            class="text-muted"><?= $symbol ?></span></strong>
            </li>
        <?php } ?>
        <?php if ($model->discount) { ?>
            <li class="list-group-item">
                <?= $model::t('DISCOUNT') ?>:
                <?php if ('%' === substr($model->discount, -1, 1)) { ?>
                    <strong class="float-right"><?= $model->discount; ?></strong>
                <?php } else { ?>
                    <strong class="float-right"><?= Yii::$app->currency->number_format($model->discount) ?> <span
                                class="text-muted"><?= $symbol ?></span></strong>
                <?php } ?>
            </li>
        <?php } ?>
        <li class="list-group-item d-flex justify-content-between">
            <span class="d-flex align-items-center mr-4"><?= $model::t('FULL_PRICE') ?>:</span>
            <h4 class="m-0"><?= Yii::$app->currency->number_format($model->full_price); ?>
                <small class="text-muted"><?= $symbol; ?></small>
            </h4>
        </li>
    </ul>
</div>
