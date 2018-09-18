<?php

use yii\helpers\Url;
use yii\jui\Spinner;
use panix\engine\Html;
use panix\mod\shop\models\Product;

?>
<?php
$this->registerJs("
//cart.selectorTotal = '#total';
var orderTotalPrice = '$totalPrice';
", yii\web\View::POS_HEAD,'cart');
?>
<script>
    $(function(){
 
        $('.payment_checkbox').click(function(){
            $('#payment').text($(this).attr('data-value'));
        });
        $('.delivery_checkbox').click(function(){
            $('#delivery').text($(this).attr('data-value'));
          
        });
        // if($('#cart-check').length > 0){
        //     $('#cart-check').stickyfloat({ duration: 800 });
        // }
        hasChecked('.payment_checkbox','#payment');
        hasChecked('.delivery_checkbox','#delivery');
    });
    
    function hasChecked(selector,div){
        $(selector).each(function(k,i){
            var inp = $(i).attr('checked');
            if(inp=='checked'){
                $(div).text($(this).attr('data-value'))
            }
        });
    }
    function submitform(){
        if(document.cartform.onsubmit &&
            !document.cartform.onsubmit())
        {
            return;
        }
        document.cartform.submit();
    }
</script>

<div class="row">
<?php
if (empty($items)) {
    echo Html::beginTag('div', array('id' => 'container-cart', 'class' => 'indent'));
    echo Html::beginTag('h1');
    echo Yii::t('cart/default', 'CART_EMPTY');
    echo Html::endTag('h1');
    echo Html::endTag('div');
    return;
}


?>





<?php echo Html::beginForm(['/cart'], 'post', array('id' => 'cart-form', 'name' => 'cartform')) ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 shopping-cart-table">

    <div class="table-responsive">
        <table id="cart-table" class="table table-striped" width="100%" border="0" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th></th>
                    <th style="width:30%"><?= Yii::t('cart/default', 'TABLE_NAME') ?></th>
                    <th style="width:30%"><?= Yii::t('cart/default', 'TABLE_NUM') ?></th>
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

                    echo Html::img(Url::to($product['model']->getMainImageUrl('100x')), ['alt' => $product['model']->name]);
      
                ?>
                    
                        </td>
                        <td>
                            <?php
                            // Display product name with its variants and configurations
                            echo Html::a(Html::encode($product['model']->name), $product['model']->getUrl());
                            ?>
                            <br/>
                            <?php
                            // Price

                            echo Html::beginTag('span', array('class' => 'price'));
                            echo Yii::$app->currency->number_format(Yii::$app->currency->convert($price));
                            echo ' ' . Yii::$app->currency->active->symbol;
                            //echo ' '.($product['currency_id']) ? Yii::$app->currency->getSymbol($product['currency_id']) : Yii::$app->currency->active->symbol;
                            echo Html::endTag('span');

                            // Display variant options
                            if (!empty($product['variant_models'])) {
                                echo Html::beginTag('span', array('class' => 'cartProductOptions'));
                                foreach ($product['variant_models'] as $variant)
                                    echo ' - ' . $variant->attribute->title . ': ' . $variant->option->value . '<br/>';
                                echo Html::endTag('span');
                            }

                            // Display configurable options
                            if (isset($product['configurable_model'])) {
                                $attributeModels = ShopAttribute::model()->findAllByPk($product['model']->configurable_attributes);
                                echo Html::beginTag('span', array('class' => 'cartProductOptions'));
                                foreach ($attributeModels as $attribute) {
                                    $method = 'eav_' . $attribute->name;
                                    echo ' - ' . $attribute->title . ': ' . $product['configurable_model']->$method . '<br/>';
                                }
                                echo Html::endTag('span');
                            }
                            ?>
                        </td>
                        <td>
<?php
echo Spinner::widget([
    'name'  => "quantities[$index]",
    'value'=>$product['quantity'],
    'clientOptions' => ['max' => 999],
    'options'=>['product_id' => $index,'class'=>'cart-spinner']
]);
?>
                            <?php //echo Html::textInput("quantities[$index]", $product['quantity'], array('class' => 'spinner btn-group form-control', 'product_id' => $index)) ?>

                        </td>
                        <td id="price-<?= $index ?>" class="cart-product-sub-total">
                            <?php
                            echo Html::beginTag('span', array('class' => 'cart-sub-total-price', 'id' => 'row-total-price' . $index));
                            echo (Yii::$app->settings->get('shop', 'wholesale')) ? Yii::$app->currency->number_format(Yii::$app->currency->convert($price * $product['model']->pcs * $product['quantity'])) : Yii::$app->currency->number_format(Yii::$app->currency->convert($price * $product['quantity']));
                            echo Html::endTag('span');
                            //echo $convertTotalPrice;// echo Yii::$app->currency->number_format(Yii::$app->currency->convert($convertPrice, $product['currency_id']));
                            echo ' ' . Yii::$app->currency->active->symbol;
                            //echo ' '.($product['currency_id'])? Yii::$app->currency->getSymbol($product['currency_id']): Yii::$app->currency->active->symbol;
                            ?>
                        </td>
                        <td width="20px" class="remove-item">
                            <?= Html::a('<i class="icon-delete"></i>', ['/cart/default/remove', 'id' => $index], array('class' => 'remove icon')) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


    </div>
    <?php
    // Yii::$app->tpl->alert('info', Yii::t('cart/default', 'ALERT_CART'))
    ?>
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <?php
    if($this->context->form->hasErrors()){
      echo  yii\bootstrap4\Alert::widget([
          'closeButton'=>false,
            'options' => ['class' => 'alert-danger'],
            'body'=>Html::errorSummary($this->context->form)
            ]);
    }



    // Yii::$app->tpl->alert('info', Yii::t('cart/default', 'ALERT_CART'))
  //  echo Html::errorSummary($this->form, '', null, array('class' => 'errorSummary alert alert-danger'));
    ?>
</div>


<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="panel panel-default">
        <div class="panel-heading"><?= Yii::t('cart/default', 'USER_DATA'); ?></div>
        <div class="panel-body">
            <p class="hint">Поля отмеченные <span class="required">*</span> обязательны для заполнения</p>
            <?php echo $this->render('_fields_user', array('form' => $this->context->form)); ?>
        </div>
    </div>
</div>

<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

    <div class="panel panel-default">
        <div class="panel-heading">Оплата / доставка</div>
        <div class="panel-body">
            <p class="hint">Поля отмеченные <span class="required">*</span> обязательны для заполнения</p>
            <?php
            echo $this->render('_fields_delivery', array(
                'form' => $this->context->form,
                'deliveryMethods' => $deliveryMethods)
            );
            echo $this->render('_fields_payment', array(
                'form' => $this->context->form,
                'paymenyMethods' => $paymenyMethods)
            );
            ?>
        </div>
    </div>

</div>



<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

    <div class="panel panel-default">
        <div class="panel-heading">dsadsadsa</div>
        <div class="panel-body">
            <div class="cart-grand-total">
                Сумма заказа <span id="total"><?= Yii::$app->currency->number_format($totalPrice) ?></span> <?php echo Yii::$app->currency->active->symbol; ?>
            </div>
            <div id="cart-check" class="text-center padding-tb">
                <div style="font-size:14px"><?= Yii::t('cart/default', 'PAYMENT'); ?>:</div>
                <div id="payment" style="font-size:14px;margin-bottom:20px;font-weight:bold">---</div>
                <div style="font-size:14px"><?= Yii::t('cart/default', 'DELIVERY'); ?>:</div>
                <div id="delivery" style="font-size:14px;margin-bottom:20px;font-weight:bold">---</div>
                <a href="javascript:submitform();" class="btn btn-primary btn-lg"><?= Yii::t('cart/default', 'BUTTON_CHECKOUT'); ?></a>
            </div>
            <input type="hidden" name="create" value="1">
        </div>
    </div>

</div>



<?php echo Html::endForm() ?>

</div>