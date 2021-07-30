<?php
use yii\helpers\Html;
use panix\mod\shop\models\Product;

/** @var $currency \panix\mod\shop\components\CurrencyManager */
/** @var $items[] \panix\mod\shop\models\Product */
?>
<div class="cart">


    <?php if ($count > 0) { ?>
        <div class="dropdown">
            <div class="cart-info dropdown-toggle" id="cart-items" data-toggle="dropdown" aria-haspopup="true"
                 aria-expanded="true">
                <span class="count"><strong><?= $count ?></strong> товара / </span>
                <span><strong><?= $total; ?></strong></span>
                <?= $currency['symbol']; ?>
            </div>
            <div class="dropdown-menu dropdown-menu-right2">
                <?php
                foreach ($items as $index=>$product) {

                    ?>

                    <?php
                    $price = Product::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                    ?>
                    <div class="cart-product-item">
                        <div class="cart-product-item-image">
                            <?php echo Html::img($product['model']->getMainImage('50x50')->url, array('class' => 'img-thumbnail')) ?>
                        </div>
                        <div class="cart-product-item-detail">
                            <?php echo Html::a($product['model']->name, $product['model']->getUrl()) ?>
                            <br/>
                            (<?php echo $product['quantity'] ?>)
                            <?= Yii::$app->currency->number_format(Yii::$app->currency->convert($price)) ?> <?= $currency['symbol']; ?>
                        </div>
                    </div>

                <?php } ?>
                <div class="cart-detail clearfix">
                    <span class="total-price pull-left"><span
                                class="label label-success"><?= $total ?></span> <?= $currency['symbol']; ?></span>
                    <?= Html::a(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['/cart'], ['class' => 'btn btn-sm btn-primary pull-right']) ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <a href="/cart" class="cart-info">
            <span class="hidden-xs"><?= Yii::t('cart/default', 'CART_EMPTY') ?></span>
        </a>
    <?php } ?>

</div>
