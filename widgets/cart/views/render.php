<div class="cart-info <?= (Yii::app()->cart->countItems()>0) ? 'cart-full' : 'cart-empty' ?>">
    <div class="cart-content">
        <?php if (Yii::app()->cart->countItems()) { ?>
            <a href="<?= Yii::app()->createUrl('/cart') ?>">
                <?= Yii::app()->cart->countItems() ?> <?= CMS::GetFormatWord('CartModule.default', 'WORDFORMATCART', Yii::app()->cart->countItems()); ?><br/> <span><?= ShopProduct::formatPrice(Yii::app()->currency->convert(Yii::app()->cart->getTotalPrice())) ?></span>
                <?= Yii::app()->currency->active->symbol ?>
            </a>
        <?php } else { ?>
            <?= Yii::t('CartModule.default', 'CART_EMPTY') ?>
        <?php } ?>
    </div>
</div>
<?php if(Yii::app()->cart->getDataWithModels()){ ?>
<div id="cart-products">

        <?php foreach (Yii::app()->cart->getDataWithModels() as $index => $product) { ?>
            <?php
            $price = ShopProduct::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
            ?>
            <div class="product-row">


                <?php
                // Display image
                $thumbSize = '50x50';
                if (isset($product['model']->mainImage)) {
                    $imgSource = $product['model']->mainImage->getUrl($thumbSize);
                    echo Html::link(Html::image($imgSource, ''), $product['model']->mainImage->getUrl($thumbSize), array('class' => 'thumbnail'));
                } else {
                    $imgSource = CMS::placeholderUrl(array('size'=>$thumbSize));
                    echo Html::link(Html::image($imgSource, ''), '#', array('class' => 'thumbnail'));
                }
                ?>


                <div class="info"><?php echo($product['model']['name']); ?><br>
                    <?php
                    echo Html::openTag('span', array('class' => 'price'));
                    echo Html::openTag('span', array('class' => 'pn'));
                    echo ShopProduct::formatPrice(Yii::app()->currency->convert($price*$product['quantity']));
                    echo Html::closeTag('span');
                    echo ' ' . Yii::app()->currency->active->symbol;
                    echo ' (x'.$product['quantity'].')';
                    //echo ' '.($product['currency_id']) ? Yii::app()->currency->getSymbol($product['currency_id']) : Yii::app()->currency->active->symbol;
                    echo Html::closeTag('span');
                    echo Html::link('x', 'javascript:shop.removeCart('.$index.')', array('class' => 'remove')) ?>
                </div>
                <div class="clear"></div>
            </div>

        <?php } ?>
    <div class="button-container"><a href="/cart" class="button btn-small btn-green">Оформить заказ</a></div>
</div>
    <?php } ?>