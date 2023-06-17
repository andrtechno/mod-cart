<?php

use panix\mod\shop\models\Product;
use panix\engine\Html;
use yii\helpers\Url;

?>
<div class="table-responsive">
    <table class="cart-table">
        <thead>
        <tr>
            <th scope="col" style="width:500px"><?= Yii::t('cart/default', 'TABLE_PRODUCT') ?></th>

            <th scope="col"><?= Yii::t('cart/default', 'QUANTITY') ?></th>
            <th scope="col"><?= Yii::t('default','QUANTITY_PAIRS'); ?></th>
            <th scope="col"><?= Yii::t('default','PRICE_PER'); ?></th>
            <th scope="col"><?= Yii::t('default','PRICE_PER_BOX'); ?></th>
            <th scope="col"><?= Yii::t('default','SUM'); ?></th>
            <th class="cart-delete-row"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $index => $product) { ?>
            <?php
            $price = Product::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
            ?>
            <tr id="product-<?= $index; ?>">

                <td class="none">
                    <?= Html::a('<i class="icon-delete"></i> <span class="d-inline-block d-lg-none">'.Yii::t('app/default','DELETE').'</span>', ['/cart/default/remove', 'id' => $index], ['data-product' => $index, 'class' => 'cart-remove remove d-block d-md-none mb-3']) ?>
                    <div class="d-flex align-items-center">
                        <div class="text-left">
                            <?= Html::a(Html::img(Url::to($product['model']->getMainImage('240x')->url), ['class' => 'img-fluid1', 'width' => 120, 'alt' => $product['model']->name]), $product['model']->getUrl()); ?>
                        </div>
                        <div class="text-left">
                            <?= Html::a(Html::encode($product['model']->name), $product['model']->getUrl(), ['class' => 'product-name']); ?>
                        </div>
                    </div>

                </td>

                <td class="order-2 spinner-col" data-label="<?= Yii::t('cart/default', 'QUANTITY') ?>">
                    <div class="d-inline-block m-auto">
                        <div class="spinner ">
                            <?= Html::button('-', ['class' => 'spinner--down', 'data-event' => 'down']); ?>
                            <?= Html::textInput("quantities[$index]", $product['quantity'], ['data' => [
                                'step' => $product['model']->in_box,
                                'min' => $product['model']->quantity_min,
                                //'max' => 999,
                                'product_id' => $index
                            ]]); ?>
                            <?= Html::button('+', ['class' => 'spinner--up', 'data-event' => 'up']); ?>
                        </div>
                    </div>
                </td>
                <td data-label="<?= Yii::t('default','PER_BOX'); ?>"><span
                            class="price"><?= $product['in_box']; ?></span></td>
                <td data-label="<?= Yii::t('default','PRICE_PER'); ?>">
                       <span class="price">
                              <?= Yii::$app->currency->number_format($price / $product['in_box']); ?>
                              <span class="currency"><?= Yii::$app->currency->active['symbol']; ?></span>
                       </span>
                </td>
                <td data-label="<?= Yii::t('default','PRICE_PER_BOX'); ?>">
                       <span class="price">

                                                <?= Yii::$app->currency->number_format($price); ?>
                                                <span class="currency"><?= Yii::$app->currency->active['symbol']; ?></span>

                       </span>
                </td>
                <td data-label="<?= Yii::t('default','SUM'); ?>" id="price-<?= $index ?>">

                                <span class="price">
                                    <span class="row-total-price<?= $index ?>"><?= Yii::$app->currency->number_format($price / $product['in_box'] * $product['quantity']); ?></span>
                                     <span class="currency"><?= Yii::$app->currency->active['symbol']; ?></span>
                                </span>

                </td>
                <td class="cart-delete-row text-left text-lg-center">
                    <?= Html::a('<i class="icon-delete"></i> <span class="d-inline-block d-lg-none">'.Yii::t('app/default','DELETE').'</span>', ['/cart/default/remove', 'id' => $index,'popup'=>$popup], ['data-product' => $index, 'class' => 'cart-remove remove d-none d-md-block']) ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>
