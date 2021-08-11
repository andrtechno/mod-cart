<?php

use panix\mod\shop\models\Product;
use panix\engine\Html;
use yii\helpers\Url;
use yii\jui\Spinner;

/**
 * @var $this \yii\web\View
 */

\panix\mod\cart\CartAsset::register($this);
?>
<div style="max-width: 70%">
    <div class="table-responsive">
        <table id="cart-table" class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th style="width:30%"><?= Yii::t('cart/default', 'TABLE_PRODUCT') ?></th>
                <th style="width:30%"><?= Yii::t('cart/default', 'QUANTITY') ?></th>
                <th style="width:30%">Сумма</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items['items'] as $index => $product) { ?>
                <?php


                $price = Product::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                ?>
                <tr id="product-<?= $index ?>">
                    <td width="110px" align="center">

                        <?= Html::img(Url::to($product['model']->getMainImage('100x')->url), ['alt' => $product['model']->name]); ?>

                    </td>
                    <td>
                        <h5><?= Html::a(Html::encode($product['model']->name), $product['model']->getUrl()); ?></h5>
                        <?php
                        //\panix\engine\CMS::dump($product['attributes_data']->attrbiutes);
                        //  print_r($product['attributes_data']['attributes']);
                        $attributesData = (array)$product['attributes_data']->attributes;

                        $query = \panix\mod\shop\models\Attribute::find();
                        $query->where(['IN', 'name', array_keys($attributesData)]);
                        $query->displayOnCart();
                        $query->sort();
                        $result = $query->all();
                        // print_r($query);

                        foreach ($result as $q) {
                            echo $q->title . ' ';
                            echo $q->renderValue($attributesData[$q->name]) . ' <br>';
                        }

                        ?>
                        <?php
                        // Display variant options
                        if (!empty($product['variant_models'])) { ?>
                            <div class="cartProductOptions">
                                <small>
                                    <?php foreach ($product['variant_models'] as $variant) {
                                        /** @var $variant \panix\mod\shop\models\ProductVariant */
                                        echo ' &mdash; ' . $variant->productAttribute->title . ': <strong>' . $variant->option->value . '</strong>' . $variant->productAttribute->abbreviation . '<br/>';
                                    }
                                    ?>
                                </small>
                            </div>
                        <?php } ?>
                        <span class="price price-sm  text-warning">
                                <?= Yii::$app->currency->number_format($price); ?>
                                <sub><?= Yii::$app->currency->active['symbol']; ?>
                                    /<?= $product['model']->units[$product['model']->unit]; ?></sub>
                            </span>

                        <?php

                        // Display configurable options
                        if (isset($product['configurable_model'])) {
                            $attributeModels = \panix\mod\shop\models\Attribute::findAll(['id' => $product['model']->configurable_attributes]);
                            echo Html::beginTag('span', ['class' => 'cartProductOptions']);
                            foreach ($attributeModels as $attribute) {
                                $method = 'eav_' . $attribute->name;
                                echo ' - ' . $attribute->title . ': ' . $product['configurable_model']->$method->value . '<br/>';
                            }
                            echo Html::endTag('span');
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <div class="spinner" data-product="<?= $index; ?>">
                            <?= Html::button('-',['data-action'=>'minus']); ?>
                            <?= Html::textInput("quantities[$index]",$product['quantity']); ?>
                            <?= Html::button('+',['data-action'=>'plus']); ?>
                        </div>

                        <span><?= $product['model']->units[$product['model']->unit]; ?></span>
                        <?php //echo Html::textInput("quantities[$index]", $product['quantity'], array('class' => 'spinner btn-group form-control', 'product_id' => $index)) ?>

                    </td>
                    <td id="price-<?= $index ?>" class="text-center">

                            <span class="price text-warning">
                                <span class="cart-sub-total-price" id="row-total-price<?= $index ?>">
                                    <span><?= Yii::$app->currency->number_format($price * $product['quantity']); ?></span>
                                </span>
                                <sub><?= Yii::$app->currency->active['symbol']; ?></sub>
                            </span>


                        <?php


                        ?>
                    </td>
                    <td width="20px" class="remove-item">
                        <?= Html::button(Html::icon('delete'), ['data-product'=>$index,'class' => 'btn btn-sm text-danger cart-remove']) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<div class="row">
    <div class="col-sm-6">
        <?= Html::button('Продолжить покупки',['class'=>'btn btn-outline-secondary','onclick'=>'$.fancybox.close();']); ?>
    </div>
    <div class="col-sm-6 text-right">
        <span class="" id="total"><?= Yii::$app->currency->number_format($totalPrice) ?></span>
        <sub><?php echo Yii::$app->currency->active['symbol']; ?></sub>
        <?= Html::a('Оформить заказ',['/cart/default/index'],['class'=>'btn btn-primary']); ?>
    </div>
</div>


</div>

