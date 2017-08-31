
<div class="cart pull-left">
    <a href="<?=Yii::app()->createUrl('/cart')?>" class="cart-info">
        <?php if ($count > 0) { ?>
            <span class="cart-count"><?=$count?></span> <span class=" hidden-xs">/ <span class="cart-price"><?= $total; ?></span> <sub><?= $currency->symbol; ?></sub></span>
        <?php } else { ?>
             <span class="cart-count hidden-sm hidden-md hidden-lg">0</span>
            <span class="hidden-xs"><?= Yii::t('CartModule.default', 'CART_EMPTY') ?></span>
        <?php } ?>
    </a>
</div>
