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
                    // 'dataProvider' => $dataProvider,
                    'enablePushState' => false,
                    'enableReplaceState' => false,
                    'id' => 'pjax-grid-order-add-products',
                ]);

                echo GridView::widget([
                    'id' => 'grid-order-add-products',
                    'filterUrl' => ['/admin/cart/default/add-product-list', 'id' => $model->id],
                    'tableOptions' => ['class' => 'table table-striped'],
                    'dataProvider' => $dataProvider,
                    'enableLayout' => false,
                    'filterModel' => $searchModel,
                    //'dataColumns'=>(new \panix\mod\cart\models\OrderProduct())->getGridColumns(),
                    'pager' => [
                        'options' => ['class' => 'pagination justify-content-center'],
                        'class' => 'panix\engine\widgets\LinkPager'
                    ],

                ]);
                Pjax::end();
                ?>
            </div>
        </div>
    </div>
</div>
