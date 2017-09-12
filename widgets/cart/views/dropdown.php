<?php
use yii\helpers\Html;
use panix\mod\shop\models\ShopProduct;
?>
<div class="cart">

    

        <?php if ($count > 0) { ?>
        <div class="dropdown">
        <div class="cart-info dropdown-toggle" id="cart-items" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span class="count"><?= $count ?></span>
            <span><?= $total; ?></span> <small><?= $currency->symbol; ?></small>
        </div>
        <div class="dropdown-menu dropdown-menu-right">
            
            <ul class="list-group">
  <li class="list-group-item">Cras justo odio</li>
  <li class="list-group-item">Dapibus ac facilisis in</li>
  <li class="list-group-item">Morbi leo risus</li>
  <li class="list-group-item">Porta ac consectetur ac</li>
  <li class="list-group-item">Vestibulum at eros</li>
</ul>
            <?php
            foreach ($items as $product) {

                ?>
            
                <?php
                $price = ShopProduct::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                ?>
                <div class="cart-product-item">
                    <div class="cart-product-item-image">
                        <?php echo Html::img($product['model']->getMainImageUrl('50x50'), array('class' => 'img-thumbnail')) ?>
                    </div>
                    <div class="cart-product-item-detail">
                        <?php echo Html::a($product['model']->name, $product['model']->getUrl()) ?>
                        <br/>
                        (<?php echo $product['quantity'] ?>)
                        <?= ShopProduct::formatPrice(Yii::$app->currency->convert($price)) ?> <?= $currency->symbol; ?>
                    </div>
                </div>

            <?php } ?>
            <div class="cart-detail clearfix">
                <span class="total-price pull-left"><span class="label label-success"><?= $total ?></span> <?= $currency->symbol; ?></span>
                <?= Html::a(Yii::t('cart/default', 'BUTTON_CHECKOUT'), array('/cart'), array('class' => 'btn btn-sm btn-primary pull-right')) ?>
            </div>
        </div>
    </div>
        <?php } else { ?>
        <a href="/cart" class="cart-info">
            <span class="hidden-xs"><?= Yii::t('cart/default', 'CART_EMPTY') ?></span>
              </a>
        <?php } ?>
  
</div>
