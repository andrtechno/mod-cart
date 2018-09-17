<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\shop\models\Product;
use yii\helpers\Html;

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
    'layout' => $this->render('@admin/views/layouts/_grid_layout', [
        'title' => $this->context->pageName,
        'buttons'=>[
            [
                'label'=>'добавить товар',
                'url'=>'javascript:openAddProductDialog(' . $model->id . ');',
                'options'=>['class'=>'btn btn-success btn-xs']
            ]
        ]
            ]), //'{items}{pager}{summary}'
    'columns' => [
        [
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function($model) {
        return $model->originalProduct->renderGridImage('50x50');
    },
        ],
        'originalProduct.name',
        [
            'attribute' => 'quantity',
            'contentOptions' => ['class' => 'text-center quantity'],
        ],
        [
            'attribute' => 'price',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function($model) {
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


        <script type="text/javascript">
            var orderTotalPrice = '<?php echo $model->total_price ?>';
            $(function () {
                var total_pcs = function () {
                    var sum = 0;
                    $('.quantity').each(function (key, index) {
                        sum += Number($(this).text());
                    });
                    return sum;
                };
                $('#total_pcs').text(total_pcs);
            });
        </script>
        <?php
        $symbol = Yii::$app->currency->active->symbol;
        ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="panel-container">
                    <ul class="list-group">
                        <li class="list-group-item"><?php echo Yii::t('cart/admin', 'FOR_PAYMENT') ?> <span class="badge pull-right"><?= Product::formatPrice($model->full_price) ?> <?= $symbol ?></span></li>
                        <li class="list-group-item"><?php echo Yii::t('cart/admin', 'QUANTITY') ?> <span class="badge pull-right" id="total_pcs"></span></li>
                        <?php if ($model->delivery_price > 0) { ?>
                            <li class="list-group-item"><?php echo Yii::t('cart/Order', 'DELIVERY_PRICE') ?> <span class="badge pull-right"><?= Product::formatPrice($model->delivery_price); ?> <?= $symbol; ?></span></li>
                            <li class="list-group-item"><?php echo Yii::t('cart/admin', 'Сумма товаров') ?> <span class="badge pull-right"><?= Product::formatPrice($model->total_price) ?> <?= $symbol ?></span></li>
                            <?php } ?>
            </ul>
        </div>
    </div>
</div>