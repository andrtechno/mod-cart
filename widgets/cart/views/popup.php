<?php

/**
 * @var $this \yii\web\View
 */

?>

<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="cart-modalLabel">
    <div class="modal-dialog modal-dialog-centered2" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h3 class="modal-title" id="cart-modalLabel"><?= Yii::t('cart/default', 'MODULE_NAME'); ?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="icon-delete"></i>
                </button>
            </div>
            <div class="modal-ajax">
                <?php if ($items) { ?>
                    <?= $this->render(Yii::$app->getModule('cart')->modalBodyView, ['items' => $items, 'popup' => true, 'total' => $total]); ?>
                <?php } else { ?>
                    <?= $this->render(Yii::$app->getModule('cart')->emptyView); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
