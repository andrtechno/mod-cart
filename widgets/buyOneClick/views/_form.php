<?php
use panix\engine\Html;
use yii\widgets\ActiveForm;
if ($sended) {
    Yii::$app->tpl->alert('success', Yii::t('BuyOneClickWidget.default', 'SUCCESS'));
    ?>
    <?php
    return false;
}
?>
<div style="display: inline-block;width: 400px">
    <div>
<div class="help-block">asdasdasd</div>


<div class="table-responsive">
    <table class="table">
        <tr>
            <td class="text-left">
                <?php
                echo Html::a(Html::img($productModel->getMainImage('100x108')->url, array('class' => 'img-responsive2')), $productModel->getUrl(), array('class' => 'thumbnail2'));
                ?>
            </td>
            <td>
                <strong><?= Html::encode($productModel->name) ?></strong>
                <div class="product-price">
                    <span class="price price-md">
                        <?= $productModel->priceRange() ?> <?= Yii::$app->currency->active['symbol'] ?>
                    </span>
                    <?php
                    if (Yii::$app->hasModule('discounts') && isset($productModel->hasDiscount)) {
                        ?>
                        <span class="price price-xs price-discount"><?= $productModel->toCurrentCurrency('originalPrice') ?> <sub><?= Yii::$app->currency->active['symbol'] ?></sub></span>
                        <?php
                    }
                    ?>

                </div>
                <br/>
                Количество: <b><?= $quantity; ?></b>
                <?php
                //Yii::$app->controller->renderPartial('cart.widgets.buyOneClick.views._configurations', array('productModel' => $productModel));
                ?>
            </td>
        </tr>
    </table>
</div>






<?php

$form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => [],
]);
/*
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'buyOneClick-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('class' => '',
        'onsubmit' => "return false;",
        'onkeypress' => " if(event.keyCode == 13){ buyOneClickSend(); } "
    ),
        ));*/
echo $form->field($model, 'quantity')->hiddenInput()->label(false);
echo $form->field($model, 'phone');
if ($model->hasErrors())
//Yii::$app->tpl->alert('danger', $form->error($model, 'phone'));
   // if ($sended)
   //     Yii::$app->tpl->alert('success', Yii::t('BuyOneClickWidget.default', 'SUCCESS'));
?>


<?php //$this->widget('ext.inputmask.InputMask', array('model' => $model, 'attribute' => 'phone')); ?>



<?php echo Html::button(Yii::t('cart/default','BUY'), ['onclick' => 'buyOneClickSend();', 'class' => 'btn btn-danger d-block btn-buy']);   ?>





<?php ActiveForm::end() ?>
</div></div>