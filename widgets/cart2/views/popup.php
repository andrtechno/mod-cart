<?php

use panix\mod\shop\models\Product;
use panix\engine\Html;
use yii\helpers\Url;
use yii\jui\Spinner;

/**
 * @var $this \yii\web\View
 */


?>

<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="cart-modalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="cart-modalLabel"><?= Yii::t('cart/default', 'MODULE_NAME'); ?></h4>
            </div>
            <div class="modal-body">

                <?php echo $this->render(Yii::$app->getModule('cart')->modalView, ['items' => $items,'total' => $total,'isPopup'=>true]); ?>
            </div>
        </div>
    </div>
</div>


