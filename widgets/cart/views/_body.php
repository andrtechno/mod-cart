<?php

use yii\helpers\Html;

?>
<div class="modal-body p-0">
    <div class="ml-3 mr-3 ml-lg-0 mr-lg-0 cart-items">
        <?= $this->render('@cart/views/default/_items', ['items' => $items, 'popup' => true]); ?>
    </div>
</div>
<div class="modal-footer d-block">
    <div class="row">
        <div class="col-md-6 col-sm-12 d-flex align-items-end justify-content-center justify-content-md-start">
            <?= Html::button('<span>' . Yii::t('cart/default', 'BUTTON_CONTINUE_SHOPPING') . '</span>', ['class' => 'btn btn-outline-secondary', 'data-dismiss' => 'modal']); ?>
        </div>
        <div class="col-md-6 col-sm-12 text-right" style="">

            <div class="h3 text-center text-md-right">
                <span class="cart-total"><?= Yii::$app->currency->number_format($total) ?></span> <?php echo Yii::$app->currency->active['symbol']; ?>
            </div>
            <div class="text-center text-md-right">
                <?= Html::a(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['/cart/default/index'], ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
</div>
