<?php

use panix\mod\shop\models\Product;
use panix\engine\Html;
use yii\helpers\Url;
use yii\jui\Spinner;

/**
 * @var $this \yii\web\View
 */

//Yii::$app->assetManager->bundles['yii\jui\JuiAsset'] = false;
//Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
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
                    <div class="modal-body p-0">
                        <div class="ml-3 mr-3 cart-items">
                            <?php //echo $this->render('@cart/views/default/_items', ['items' => $items, 'popup' => true]); ?>
                        </div>
                        <?php //echo $this->render(Yii::$app->getModule('cart')->modalView, ['items' => $items,'total' => $total,'popup'=>true]); ?>
                    </div>
                    <div class="modal-footer">
                        <div class="">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 d-flex align-items-end justify-content-center justify-content-md-start">
                                    <?= Html::button('<span>' . Yii::t('cart/default', 'BUTTON_CONTINUE_SHOPPING') . '</span>', ['class' => 'btn btn-outline-success', 'data-dismiss' => 'modal']); ?>
                                </div>
                                <div class="col-md-6 col-sm-12 text-right" style="">
                                    <div class="price price-xl text-center text-md-right mt-3 mb-3">
                                        <span class="cart-total"><?= Yii::$app->currency->number_format($total) ?></span>
                                        <span class="currency"><?php echo Yii::$app->currency->active['symbol']; ?></span>
                                    </div>
                                    <div class="text-center text-md-right">
                                        <?= Html::a(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['/cart/default/index'], ['class' => 'btn btn-success btn-buy']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="modal-body p-0">
                        <?php echo $this->render(Yii::$app->getModule('cart')->emptyView); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


