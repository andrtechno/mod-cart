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
                'label' => 'добавить товар',
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
                return ($model->originalProduct) ? $model->originalProduct->renderGridImage() : Html::tag('span', 'удален', ['class' => 'badge badge-danger']);
            },
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                /** @var $model OrderProduct */
                return ($model->originalProduct) ? Html::a($model->name . ' [' . $model->product_id . ']', $model->originalProduct->getUrl()) : $model->name;
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


<div class="card">
    <div class="card-body">
        <div class="panel-container">
            <ul class="list-group">
                <?php if ($model->delivery_price > 0) { ?>
                    <li class="list-group-item">
                        <?= Yii::t('cart/Order', 'DELIVERY_PRICE') ?>: <strong
                                class="pull-right"><?= Yii::$app->currency->number_format($model->delivery_price); ?> <?= $symbol; ?></strong>
                    </li>
                <?php } ?>
                <li class="list-group-item">
                    <?= Yii::t('cart/default', 'ORDER_PRICE') ?>: <strong
                            class="pull-right"><?= Yii::$app->currency->number_format($model->total_price) ?> <?= $symbol ?></strong>
                </li>
                <li class="list-group-item">
                    <?= Yii::t('cart/default', 'TOTAL_PAY') ?>: <strong
                            class="pull-right"><?= Yii::$app->currency->number_format($model->total_price + $model->delivery_price) ?> <?= $symbol ?></strong>
                </li>

            </ul>
        </div>
    </div>
</div>