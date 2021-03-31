<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\cart\models\OrderProduct;
use yii\helpers\Html;
use panix\engine\CMS;

/**
 * @var \panix\mod\cart\models\Order $model
 */
$symbol = Yii::$app->currency->active['symbol'];

Pjax::begin([
    'id' => 'pjax-container-products',
    // 'enablePushState' => false,
    // 'linkSelector' => 'a:not(.linkTarget)'
]);
$buttons = [];
if (!$model->apply_user_points && $model->status_id != $model::STATUS_SUBMITTED) {

    $buttons[] = [
        'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
        'url' => '#',
        'options' => ['class' => 'btn btn-success btn-sm', 'data-toggle' => "modal", 'data-target' => "#cart-add-product"]
    ];

}
echo GridView::widget([
    //  'id' => 'orderedProducts',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $model->getOrderedProducts(),
    // 'filterModel' => $searchModel,
    'showFooter' => true,
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    'layoutOptions' => [
        'title' => Yii::t('cart/admin', 'ORDER_PRODUCTS'),
        'buttons' => $buttons
    ],
    'columns' => [
        'image' => [
            'class' => 'panix\engine\grid\columns\ImageColumn',
            'attribute' => 'image',
            'header' => Yii::t('cart/OrderProduct', 'IMAGE'),
            // 'filter'=>true,
            'value' => function ($model) {
                /** @var $model OrderProduct */

                if($model->getProduct()){
                    return Html::a(Html::img($model->getProductImage('50x50')), $model->getProductImage(),['data-pjax'=>false]);
                }else{
                    return \panix\engine\Html::tag('span', 'товар удален', ['class' => 'badge badge-danger']);
                }

            },
        ],

        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                /** @var $model OrderProduct */
                if ($model->currency_id && $model->currency_rate) {
                    $priceValue = Yii::$app->currency->convert($model->price / $model->currency_rate,$model->currency_id);
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
                return $model->getProductName(false,['data-pjax'=>'0']) . '<br/>' . $variantsConfigure . $price;
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
            'contentOptions' => ['class' => 'text-center','style'=>'min-width:120px'],
            'footer' => Yii::$app->currency->number_format($model->total_price) . ' ' . Yii::$app->currency->main['symbol'],
            'value' => function ($model) {
                /** @var $model OrderProduct */
                //if ($model->currency_id && $model->currency_rate) {
                //    $priceValue = Yii::$app->currency->convert($model->price, $model->currency_id);
               // } else {
                    $priceValue = $model->price * $model->quantity;
              //  }
                return Yii::$app->currency->number_format($priceValue) . ' ' . Yii::$app->currency->main['symbol'];
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $data, $key) use ($model) {
                    if (!$model->apply_user_points && $model->status_id != $model::STATUS_SUBMITTED) {
                        return Html::a('<i class="icon-delete"></i>', '#', [
                            'title' => Yii::t('app/default', 'DELETE'),
                            'class' => 'btn btn-sm btn-danger',
                            'onClick' => "return deleteOrderedProduct($data->id, $data->order_id);"
                        ]);
                    }
                }
            ]
        ]
    ]
]);
Pjax::end();

?>


<div class="panel-container">
    <ul class="list-group">
        <?php if ($model->user_id) { ?>
            <li class="list-group-item">
                Бонусы к зачаслению:
                <h5 class="m-0 float-right"><?= floor($model->total_price * Yii::$app->settings->get('user', 'bonus_ratio')) ?>
                    <span class="text-muted"><?= $symbol ?></span></h5>
            </li>
        <?php } ?>
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


<div class="card mt-4">
    <div class="card-header">
        <h5><?= Yii::t('cart/admin', 'Доп информация'); ?></h5>
    </div>
    <div class="card-body">

        <?php
        $browser = new \panix\engine\components\Browser($model->user_agent);
        ?>

        <div class="list-group-item d-flex justify-content-between">
            <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('ip_create'); ?>:</span>
            <span class="m-0"><?= CMS::ip($model->ip_create); ?></span>
        </div>
        <div class="list-group-item d-flex justify-content-between">
            <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('created_at'); ?>:</span>
            <span class="m-0"><?= CMS::date($model->created_at); ?></span>
        </div>
        <div class="list-group-item d-flex justify-content-between">
            <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('updated_at'); ?>:</span>
            <span class="m-0"><?= CMS::date($model->updated_at); ?></span>
        </div>
        <div class="list-group-item d-flex justify-content-between">
            <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('user_agent'); ?>:</span>
            <span class="m-0 text-right">
                    <?= $browser->getBrowser(); ?> (v <?= $browser->getVersion(); ?>)
                    <br/>
                <?= $browser->getPlatformIcon(); ?> <?= $browser->getPlatform(); ?>
                </span>
        </div>
    </div>
</div>