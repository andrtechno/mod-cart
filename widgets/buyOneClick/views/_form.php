<?php

use panix\engine\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var $this \yii\web\View
 * @var $productModel \panix\mod\shop\models\Product
 * @var $quantity integer
 */

$this->registerJs("
$(document).on('beforeValidate', 'form', function(event, messages, deferreds) {
    $(this).find(':submit').attr('disabled', true);
}).on('afterValidate', 'form', function(event, messages, errorAttributes) {
    if (errorAttributes.length > 0) {
        $(this).find(':submit').attr('disabled', false);
    }
});
");

?>

<?php
$form = ActiveForm::begin([
    'id' => 'buyOneClick-form',
    'options' => [],
    // 'enableAjaxValidation' => true,
]);
?>
<div class="modal-body">

    <table class="table">
        <tr>
            <td class="text-left" style="width: 100px">
                <?php
                echo Html::a(Html::img($productModel->getMainImage('100x100')->url, array('class' => 'img-responsive2')), $productModel->getUrl(), array('class' => 'thumbnail2'));
                ?>
            </td>
            <td>
                <strong><?= Html::encode($productModel->name) ?></strong>
                <div class="product-price">
                    <div class="mb-1">
                        <?= Yii::t('default', (in_array($productModel->type_id, [2, 3])) ? 'PER_PACK' : 'За пару'); ?>:


                        <?php if ($productModel->currency_id != 1) { ?>
                            <span class="price_range"><?= Yii::$app->currency->number_format($productModel->hasDiscount ? $productModel->discountPrice : $productModel->price, $productModel->currency_id) ?> <?= Yii::$app->currency->getById($productModel->currency_id)->symbol ?></span>
                        <?php } else { ?>
                            <span class="price_range"><?= Yii::$app->currency->number_format($productModel->getFrontPrice()) ?> <?= Yii::$app->currency->active['symbol'] ?></span>
                        <?php } ?>

                        <?php if ($productModel->hasDiscount) { ?>
                            <?php if ($productModel->currency_id != 1) { ?>
                                <span class="price_range"><del><?= Yii::$app->currency->number_format($productModel->price, $productModel->currency_id) ?> <?= Yii::$app->currency->getById($productModel->currency_id)->symbol ?></del></span>
                            <?php } else { ?>
                                <span class="price_range"><del><?= Yii::$app->currency->number_format(Yii::$app->currency->convert($productModel->price, $productModel->currency_id)) ?> <?= Yii::$app->currency->active['symbol'] ?></del></span>
                            <?php } ?>

                        <?php } ?>

                    </div>


                    <div class="mb-1">
                        <?= Yii::t('default', 'PER_BOX'); ?>:


                        <span class="price_range">
<span id="productPrice"><?= Yii::$app->currency->number_format($productModel->getFrontPrice() * $productModel->in_box) ?></span> <?= Yii::$app->currency->active['symbol'] ?>
    </span>

                        <?php if ($productModel->hasDiscount) { ?>
                            <span class="compare price-box">
                                            <span class="price_range"><del><?= Yii::$app->currency->number_format(Yii::$app->currency->convert($productModel->price * $productModel->in_box, $productModel->currency_id)) ?>  <?= Yii::$app->currency->active['symbol'] ?></del></span>
                                        </span>
                        <?php } ?>

                    </div>


                    <?php
                    if (Yii::$app->hasModule('discounts') && isset($productModel->hasDiscount)) {
                        ?>
                        <span class="price price-xs price-discount"><?= $productModel->toCurrentCurrency('originalPrice') ?>
                                <sub><?= Yii::$app->currency->active['symbol'] ?></sub></span>
                        <?php
                    }
                    ?>

                </div>

                <?= Yii::t('cart/OrderProduct', 'QUANTITY'); ?>: <strong><?= $quantity; ?></strong>
                <?php
                //Yii::$app->controller->renderPartial('cart.widgets.buyOneClick.views._configurations', array('productModel' => $productModel));
                ?>
            </td>
        </tr>
    </table>


    <?php


    echo $form->field($model, 'quantity')->hiddenInput(['value' => $quantity])->error(false)->label(false);
    echo $form->field($model, 'user_phone')->widget(\panix\ext\telinput\PhoneInput::class, [
        'jsOptions' => [
            'autoPlaceholder' => 'off',
            'onlyCountries' => ['ua', 'md'],
            //'separateDialCode'=>false,
        ],
        //'options'=>['class'=>'form-control']
    ]);
    ?>


</div>

<div class="modal-footer">
    <div>
        <?= Yii::$app->currency->number_format($productModel->priceRange() * $productModel->in_box * $quantity, $productModel->currency_id) ?> <?= Yii::$app->currency->active['symbol'] ?>
    </div>
    <?php echo Html::submitButton(Yii::t('cart/default', 'BUY'), ['class' => 'btn btn-danger btn-buy']); ?>
</div>
<?php ActiveForm::end() ?>
