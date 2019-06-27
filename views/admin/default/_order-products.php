<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\shop\models\Product;
use yii\helpers\Html;

$symbol = Yii::$app->currency->active->symbol;

Pjax::begin([
    'id' => 'pjax-container-products',
    'enablePushState' => false,
    'linkSelector' => 'a:not(.linkTarget)'
]);

echo GridView::widget([
    //  'id' => 'orderedProducts',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $model->getOrderedProducts(),
    // 'filterModel' => $searchModel,
    'showFooter' => true,
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    'layoutOptions' => [
        'title' => $this->context->pageName,
        'buttons' => [
            [
                'label' => 'добавить товар',
                'url' => 'javascript:openAddProductDialog(' . $model->id . ');',
                'options' => ['class' => 'btn btn-success btn-sm']
            ]
        ]
    ],
    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function ($model) {
                return $model->originalProduct->renderGridImage('50x50');
            },
        ],
        [
            'attribute' => 'originalProduct.name',
            'format' => 'raw',
            //'footer' => $model->productsCount,
            'value' => function ($model) {
                return Html::a($model->originalProduct->name.$model->originalProduct->id,$model->originalProduct->getUrl());
            },
           // 'contentOptions' => [],

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
            'footer' => Yii::$app->currency->number_format($model->total_price) . ' ' . Yii::$app->currency->main->symbol,
            'value' => function ($model) {
                return Yii::$app->currency->number_format($model->price) . ' ' . Yii::$app->currency->main->symbol;
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $data, $key) {
                    return Html::a('<i class="icon-delete"></i>', '#', [
                        'title' => Yii::t('app', 'DELETE'),
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
                    <li class="list-group-item"><?php echo Yii::t('cart/Order', 'DELIVERY_PRICE') ?> <span
                                class="badge pull-right"><?= Yii::$app->currency->number_format($model->delivery_price); ?> <?= $symbol; ?></span>
                    </li>
                    <li class="list-group-item"><?php echo Yii::t('cart/admin', 'Сумма товаров') ?> <span
                                class="badge pull-right"><?= Yii::$app->currency->number_format($model->total_price) ?> <?= $symbol ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>