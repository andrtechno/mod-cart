
<div class="dropdown dropdown-cart">
    <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
        <div class="items-cart-inner">
            <div class="total-price-basket">
                <?php if ($count > 0) { ?>
                    <span class="lbl">Корзина -</span>
                    <span class="total-price">
                        
                        <span class="value"><?= $total; ?></span>
                        <span class="sign"><?= $currency->symbol; ?></span>
                    </span>
                <?php } else { ?>
                    <span class="lbl"><?= Yii::t('CartModule.default', 'CART_EMPTY') ?></span>
                <?php } ?>
            </div>
            <div class="basket">
                <i class="glyphicon glyphicon-shopping-cart"></i>
            </div>
            <?php if ($items) { ?>
            <div class="basket-item-count">
                <span class="count"><?= $count; ?></span>
            </div>
            <?php } ?>
        </div>
    </a>
    <?php if ($items) { ?>
        <ul class="dropdown-menu">
            <li>
                <?php $i=0; foreach ($items as $index => $product) { $i++;
                

                ?>
                    <?php
                    $price = ShopProduct::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);

                    // Display image
                    $thumbSize = '50x50';
                    if (isset($product['model']->image)) {
                        $imgSource = $product['model']->getImageUrl($thumbSize);
                        $img= Html::link(Html::image($imgSource, ''), $product['model']->getImageUrl($thumbSize), array('class' => ''));
                    } else {
                        $imgSource = CMS::placeholderUrl(array('size'=>$thumbSize));
                        $img= Html::link(Html::image($imgSource, ''), '#', array('class' => ''));
                    }
                    ?>
                    <div class="cart-item product-summary">
                        <div class="row" style="margin: 5px 0;">
                            <div class="col-xs-4" style="padding:0;">
                                <div class="image">
                                    <?= $img?>
                                </div>
                            </div>
                            <div class="col-xs-7" style="padding-left:0;">
                                <h3 class="name">
                                    <?= Html::link($product['model']['name'], array('/shop/product/view','seo_alias'=>$product['model']['seo_alias'])) ?>

                                </h3>
                                <div class="price"><?= $price; ?></div>
                            </div>
                            <div class="col-xs-1 action">
                                <?= Html::link('<i class="fa fa-trash"></i>', 'javascript:cart.remove('.$index.')', array('class' => '')) ?>
                            </div>
                        </div>
                    </div>
                <hr>
                <?php
                
                
                                if($i==5){
                    break;
                }
                
                } ?>
                <div class="clearfix"></div>
           

                <div class="clearfix cart-total">
                    <div class="pull-right">
                        <span class="text"><?= Yii::t('CartModule.default','TOTAL_PAY'); ?>:</span><span class='price'><?=$total?></span>
                    </div>
                    <div class="clearfix"></div>
                     <?= Html::link(Yii::t('CartModule.default','BUTTON_CHECKOUT'), '/cart', array('class' => 'btn btn-upper btn-primary btn-block m-t-20')) ?>
                </div>


            </li>
        </ul>
    <?php } ?>
</div>






