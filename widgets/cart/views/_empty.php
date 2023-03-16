<?php
use yii\helpers\Html;
?>
<div id="empty-cart-page" class="text-center col">
    <i class="icon-shopcart" style="font-size:130px"></i>
    <h2><?= Yii::t('cart/default', 'CART_EMPTY_HINT') ?></h2>

    <?= Html::button(Yii::t('cart/default', 'CART_EMPTY_BTN'), ['class' => 'btn btn-outline-secondary', 'data-dismiss' => 'modal']); ?>

</div>
