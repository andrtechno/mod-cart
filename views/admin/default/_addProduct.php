<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderProductSearch;

$searchModel = new ProductSearch();
$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

$dataProvider->pagination->pageSize = 10;
$dataProvider->pagination->route = '/admin/cart/default/add-product-list';


?>


<div class="modal fade" id="cart-add-product" tabindex="-1" aria-labelledby="cart-add-productLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cart-add-productLabel"><?= Yii::t('shop/admin', 'CREATE_PRODUCT') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <?php
                Pjax::begin([
                    'dataProvider' => $dataProvider,
                    'enablePushState' => false,
                    'enableReplaceState' => false
                ]);

                echo GridView::widget([
                    'filterUrl' => ['/admin/cart/default/add-product-list', 'id' => $model->id],
                    'tableOptions' => ['class' => 'table table-striped'],
                    'dataProvider' => $dataProvider,
                    'enableLayout' => false,
                    'filterModel' => $searchModel,
                    'pager' => [
                        'options' => ['class' => 'pagination justify-content-center'],
                        'class' => 'panix\engine\widgets\LinkPager'
                    ],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center', 'style' => 'width:50px'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */
                                return $model->id;
                            },
                        ],
                        [
                            'class' => 'panix\engine\grid\columns\ImageColumn',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center image'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */
                                return $model->renderGridImage();
                            },
                        ],
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-left'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */
                                return Html::a($model->name, $model->getUrl(), ['target' => '_blank', 'data-pjax' => 0]);
                            },
                        ],
                        [
                            'attribute' => 'sku',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-left'],
                            'headerOptions' => ['style' => 'width:150px'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */
                                return $model->sku;
                            },
                        ],
                        [
                            'attribute' => 'price',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center'],
                            'headerOptions' => ['style' => 'width:150px'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */
                                $discount = '';
                                if ($model->hasDiscount) {
                                    $discount = 'Скидка ' . $model->discountSum;
                                }
                                $html = $discount;
                                $html .= '<div class="input-group">';

                                $price = $model->price;

                                $html .= Html::textInput("price_{$model->id}", $price, ['id' => "price_{$model->id}", 'class' => 'form-control']);
                                $html .= '<div class="input-group-append">';
                                $html .= '<span class="input-group-text">' . (($model->currency_id) ? Yii::$app->currency->getById($model->currency_id)->iso : Yii::$app->currency->main['iso']) . '</span>';
                                $html .= '</div></div>';
                                return $html;
                            }
                        ],
                        [
                            'attribute' => 'quantity',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center'],
                            'headerOptions' => ['style' => 'width:80px'],
                            'value' => function ($model) {
                                /** @var \panix\mod\shop\models\Product $model */

                                $html = '<div class="spinner">';
                                $html .= Html::button('-', ['class' => 'spinner--down', 'data-event' => 'down']);
                                $html .= Html::textInput("count_{$model->id}", $model->quantity_min, [
                                    'id' => "count_{$model->id}",
                                    'data' => [
                                        'step' => $model->in_box,
                                        'min' => $model->quantity_min,
                                        'max' => 999,

                                    ]
                                ]);
                                $html .= Html::button('+', ['class' => 'spinner--up', 'data-event' => 'up']);
                                $html .= '</div>';
                                return $html;
                                /*return \yii\jui\Spinner::widget([
                                    'id' => "count_{$model->id}",
                                    'name' => "count_{$model->id}",
                                    'value' => $model->quantity_min,
                                    'clientOptions' => [
                                        'max' => 999,
                                        'min' => $model->quantity_min,
                                        'step' => $model->in_box
                                    ],
                                    'options' => ['class' => 'cart-spinner', 'style' => 'width:80px']
                                ]);*/
                            }
                        ],
                        [
                            'class' => 'panix\engine\grid\columns\ActionColumn',
                            'template' => '{add}',
                            'filter' => false,
                            //'contentOptions' => ['class' => 'text-center', 'style' => 'width:80px'],
                            'headerOptions' => ['style' => 'width:80px'],
                            'buttons' => [
                                'add' => function ($url, $data, $key) {
                                    return Html::a(Html::icon('add'), $data->id, [
                                        'class' => 'btn btn-sm btn-success addProductToOrder',
                                        'onClick' => 'return addProductToOrder(this, ' . Yii::$app->request->get('id') . ');'
                                    ]);
                                }
                            ]
                        ]
                    ]
                ]);
                Pjax::end();
                ?>
            </div>
        </div>
    </div>
</div>
