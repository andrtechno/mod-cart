<?php

use yii\helpers\Url;
use yii\jui\Spinner;
use panix\engine\Html;
use panix\mod\shop\models\Product;
use yii\widgets\ActiveForm;

/**
 * @var $deliveryMethods \panix\mod\cart\models\Delivery
 * @var $paymentMethods \panix\mod\cart\models\Payment
 */

$this->registerJs("
//cart.selectorTotal = '#total';
var orderTotalPrice = '$totalPrice';

    $(function () {

        $('.payment_checkbox').click(function () {
            $('#payment').text($(this).attr('data-value'));
        });
        $('.delivery_checkbox').click(function () {
            $('#delivery').text($(this).attr('data-value'));

        });
        // if($('#cart-check').length > 0){
        //     $('#cart-check').stickyfloat({ duration: 800 });
        // }
        hasChecked('.payment_checkbox', '#payment');
        hasChecked('.delivery_checkbox', '#delivery');
    });

    function hasChecked(selector, div) {
        $(selector).each(function (k, i) {
            var inp = $(i).attr('checked');
            if (inp == 'checked') {
                $(div).text($(this).attr('data-value'))
            }
        });
    }
    
/*



$('#contact-form').yiiActiveForm('add', {
    id: 'address',
    name: 'address',
    container: '.field-address',
    input: '#address',
    error: '.help-block',
    validate:  function (attribute, value, messages, deferred, form) {
        yii.validation.required(value, messages, {message: \"Validation Message Here\"});
    }
});



function submitform() {
    if (document.cartForm.onsubmit && !document.cartForm.onsubmit()) {
        return;
    }
    document.cartForm.submit();
}
$(document).on('click','#cartForm button[type=\"submit\"]', function(e){
e.preventDefault();
console.log($('#cartForm').yiiActiveForm('validate'));


return false;
});*/

", yii\web\View::POS_END);

$formOrder = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'action' => ['/cart'],
    'id' => 'cartForm',
    'options' => ['class' => 'form-horizontal'],
]) ?>

<div class="row">
    <?php
    if (empty($items)) { ?>
        <div id="empty-cart-page" class="text-center col">
            <i class="icon-shopcart" style="font-size:130px"></i>
            <h2><?= Yii::t('cart/default', 'CART_EMPTY_HINT') ?></h2>

            <?= Html::a(Yii::t('cart/default', 'CART_EMPTY_BTN'), ['/'], array('class' => 'btn btn-lg btn-outline-secondary')); ?>
        </div>
        <?php return;
    }


    ?>


    <div class="col-lg-12 col-md-12 col-sm-12 shopping-cart-table">

        <div class="table-responsive">
            <table id="cart-table" class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th style="width:30%"><?= Yii::t('cart/default', 'TABLE_PRODUCT') ?></th>
                    <th style="width:30%"><?= Yii::t('cart/default', 'QUANTITY') ?></th>
                    <th style="width:30%">Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $index => $product) { ?>
                    <?php
                    $price = Product::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                    ?>
                    <tr id="product-<?= $index ?>">
                        <td width="110px" align="center">

                            <?php

                            echo Html::img(Url::to($product['model']->getMainImage('100x')->url), ['alt' => $product['model']->name]);

                            ?>

                        </td>
                        <td>
                            <h5><?= Html::a(Html::encode($product['model']->name), $product['model']->getUrl()); ?></h5>

                            <?php
                            // Display variant options
                            if (!empty($product['variant_models'])) {
                                echo Html::beginTag('small', array('class' => 'cartProductOptions'));
                                foreach ($product['variant_models'] as $variant)
                                    echo ' - ' . $variant->productAttribute->title . ': <strong>' . $variant->option->value . '</strong><br/>';
                                echo Html::endTag('small');
                            }
                            ?>
                            <span class="price price-sm  text-warning">
                                <?= Yii::$app->currency->number_format($price); ?>
                                <sub><?= Yii::$app->currency->active['symbol']; ?>
                                    /<?= $product['model']->units[$product['model']->unit]; ?></sub>
                            </span>

                            <?php

                            // Display configurable options
                            if (isset($product['configurable_model'])) {
                                $attributeModels = \panix\mod\shop\models\Attribute::model()->findAllByPk($product['model']->configurable_attributes);
                                echo Html::beginTag('span', array('class' => 'cartProductOptions'));
                                foreach ($attributeModels as $attribute) {
                                    $method = 'eav_' . $attribute->name;
                                    echo ' - ' . $attribute->title . ': ' . $product['configurable_model']->$method . '<br/>';
                                }
                                echo Html::endTag('span');
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            echo Spinner::widget([
                                'name' => "quantities[$index]",
                                'value' => $product['quantity'],
                                'clientOptions' => ['max' => 999],
                                'options' => ['product_id' => $index, 'class' => 'cart-spinner']
                            ]);
                            ?>
                            <?= $product['model']->units[$product['model']->unit]; ?>
                            <?php //echo Html::textInput("quantities[$index]", $product['quantity'], array('class' => 'spinner btn-group form-control', 'product_id' => $index)) ?>

                        </td>
                        <td id="price-<?= $index ?>" class="text-center">

                            <span class="price text-warning">
                                <span class="cart-sub-total-price" id="row-total-price<?= $index ?>">
                                    <?= Yii::$app->currency->number_format($price * $product['quantity']); ?>
                                </span>
                                <sub><?= Yii::$app->currency->active['symbol']; ?></sub>
                            </span>


                            <?php


                            ?>
                        </td>
                        <td width="20px" class="remove-item">
                            <?= Html::a(Html::icon('delete'), ['/cart/default/remove', 'id' => $index], ['class' => 'btn btn-sm text-danger remove']) ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <td colspan="2" class="text-right">
                    <label class="control-label h5" for="ordercreateform-promocode_id" style="margin-bottom: 0">
                        Введите промо-код
                    </label>
                </td>
                <td colspan="3">
                    <?php
                    echo panix\mod\cart\widgets\promocode\PromoCodeWidget::widget([
                        'model' => $this->context->form,
                        'attribute' => 'promocode_id'
                    ]);
                    ?>
                </td>
                </tfoot>
            </table>


        </div>
        <?php
        // Yii::$app->tpl->alert('info', Yii::t('cart/default', 'ALERT_CART'))
        ?>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <?= Html::errorSummary($this->context->form, ['class' => 'alert alert-danger']) ?>
    </div>


    <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4><?= Yii::t('cart/default', 'USER_DATA'); ?></h4>
            </div>
            <div class="card-body">
                <div class="text-muted mb-3">
                    <small>Поля отмеченные <span class="required">*</span> обязательны для заполнения</small>
                </div>

                <?php echo $this->render('_fields_user', [
                    'model' => $this->context->form,
                    'form' => $formOrder,
                ]); ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

        <div class="card">
            <div class="card-header"><h4><?= Yii::t('cart/default', 'PAYMENT'); ?>
                    / <?= Yii::t('cart/default', 'DELIVERY'); ?></h4></div>
            <div class="card-body">
                <div class="text-muted mb-3">
                    <small>Поля отмеченные <span class="required">*</span> обязательны для заполнения</small>
                </div>
                <h4 class="text-center2"><?= Yii::t('cart/default', 'DELIVERY'); ?></h4>
                <?php
                echo $this->render('_fields_delivery', array(
                        'model' => $this->context->form,
                        'form' => $formOrder,
                        'deliveryMethods' => $deliveryMethods)
                );
                ?>

                <h4 class="text-center2"><?= Yii::t('cart/default', 'PAYMENT'); ?></h4>
                <?php
                echo $this->render('_fields_payment', array(
                        'model' => $this->context->form,
                        'form' => $formOrder,
                        'paymentMethods' => $paymentMethods)
                );
                ?>
            </div>
        </div>

    </div>


    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

        <div class="card">
            <div class="card-header"><h4>dsadsadsa</h4></div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <h5><?= Yii::t('cart/default', 'ORDER_PRICE'); ?>:</h5>

                    <span class="price price-lg text-warning">
                        <span class="" id="total"><?= Yii::$app->currency->number_format($totalPrice) ?></span>
                        <sub><?php echo Yii::$app->currency->active['symbol']; ?></sub>
                </span>
                </div>

                <h4><?= Yii::t('cart/default', 'PAYMENT'); ?>:</h4>

                <div>
                    <h6 id="payment">---</h6>
                </div>
                <h4><?= Yii::t('cart/default', 'DELIVERY'); ?>:</h4>
                <div>
                    <h6 id="delivery">---</h6>
                </div>

                <?= Html::submitButton(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['class' => 'btn btn-warning btn-lg']) ?>
                <input type="hidden" name="create" value="1">
            </div>
        </div>

    </div>

</div>
<?php ActiveForm::end() ?>
<?php //echo Html::endForm() ?>



