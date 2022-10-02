<?php

use panix\mod\shop\models\Product;
use panix\engine\Html;
use yii\helpers\Url;
use yii\jui\Spinner;

/**
 * @var $this \yii\web\View
 */


?>
<?php if ($items) { ?>

    <div class="table-responsive">
        <table id="cart-table" class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th style="width:30%"><?= Yii::t('cart/default', 'TABLE_PRODUCT') ?></th>
                <th style="width:30%"><?= Yii::t('cart/default', 'QUANTITY') ?> ящиков</th>
                <th style="width:30%">Сумма</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $index => $product) { ?>
                <?php

                $price = Product::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                ?>
                <tr id="product-<?= $index ?>">
                    <td width="110px" align="center">

                        <?= Html::img(Url::to($product['model']->getMainImage('100x100', ['watermark' => false])->url), ['width' => 100, 'alt' => $product['model']->name]); ?>

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
                        <span class="price price-sm">
                                <?= Yii::$app->currency->number_format($price); ?>
                            <?= Yii::$app->currency->active['symbol']; ?>
                                    /<?= $product['model']->units[$product['model']->unit]; ?>
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
                            <?= Html::button('-', ['data-action' => 'minus']); ?>
                            <?= Html::textInput("quantities[$index]", $product['quantity']); ?>
                            <?= Html::button('+', ['data-action' => 'plus']); ?>
                        </div>


                        <?php //echo Html::textInput("quantities[$index]", $product['quantity'], array('class' => 'spinner btn-group form-control', 'product_id' => $index)) ?>

                    </td>
                    <td id="price-<?= $index ?>" class="text-center">

                            <span class="price">
                                <span class="cart-sub-total-price row-total-price<?= $index ?>">
                                    <span><?= Yii::$app->currency->number_format($price * $product['quantity']); ?></span>
                                </span>
                                <?= Yii::$app->currency->active['symbol']; ?>
                            </span>


                        <?php


                        ?>
                    </td>
                    <td width="20px" class="remove-item">
                        <?= Html::button(Html::icon('delete'), ['data-product' => $index, 'class' => 'btn btn-sm text-danger cart-remove','data-ispopup'=> $isPopup ? 1 : 0]) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if ($isPopup) { ?>
        <div class="row">
            <div class="col-md-6 col-sm-12 hidden-xs">

                <?= Html::button(Yii::t('cart/default', 'BUTTON_CONTINUE_SHOPPING'), ['class' => 'btn btn-outline-secondary', 'data-dismiss' => 'modal']); ?>
            </div>
            <div class="col-md-6 col-sm-12 text-right container-checkout" style="">
                <div style="margin-right: 1rem" class="container-checkout-price">
                    <span class="h1 cart-totalPrice"><?= Yii::$app->currency->number_format($total) ?></span>
                    <span class="h3"><?php echo Yii::$app->currency->active['symbol']; ?></span>
                </div>
                <?= Html::a(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['/cart/default/index'], ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    <?php } ?>

<?php } else { ?>
    <?php echo $this->render(Yii::$app->getModule('cart')->emptyView); ?>
<?php } ?>
