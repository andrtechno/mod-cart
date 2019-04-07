<?php
if ($sended) {
    Yii::$app->tpl->alert('success', Yii::t('BuyOneClickWidget.default', 'SUCCESS'));
    ?>
    <script>$(function () {
            $('.ui-dialog-buttonpane').remove();

        });</script>
    <?php
    return false;
}
?>
<div class="help-block"><?= Yii::t('BuyOneClickWidget.default', 'TEXT'); ?></div>


<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>
                <?php
                echo Html::link(Html::image($productModel->getMainImageUrl('100x108'), $productModel->name, array('class' => 'img-responsive2')), $productModel->getUrl(), array('class' => 'thumbnail2'));
                ?>




            </td>

            <td>

                <div class="product-price">
                    <span class="price price-md">
                        <?= $productModel->priceRange() ?>
                        <sub><?= Yii::$app->currency->active->symbol ?></sub>
                    </span>
                    <?php
                    if (Yii::$app->hasModule('discounts') && isset($productModel->appliedDiscount)) {
                        ?>
                        <span class="price price-xs price-discount"><?= $productModel->toCurrentCurrency('originalPrice') ?> <sub><?= Yii::$app->currency->active->symbol ?></sub></span>
                        <?php
                    }
                    ?>

                </div>
                <br/>
                Количество: <b><?= $quantity; ?></b>
                <?php
                Yii::$app->controller->renderPartial('cart.widgets.buyOneClick.views._configurations', array('productModel' => $productModel));
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b><?= Html::encode($productModel->name) ?></b>
            </td>
        </tr>
    </table>
</div>






<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'buyOneClick-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('class' => '',
        'onsubmit' => "return false;", /* Disable normal form submit */
        'onkeypress' => " if(event.keyCode == 13){ buyOneClickSend(); } " /* Do ajax call when user presses enter key */
    ),
        ));
echo $form->hiddenField($model, 'quantity', array('value' => $quantity));

if ($model->hasErrors())
//Yii::$app->tpl->alert('danger', $form->error($model, 'phone'));
    if ($sended)
        Yii::$app->tpl->alert('success', Yii::t('BuyOneClickWidget.default', 'SUCCESS'));
?>


<?php $this->widget('ext.inputmask.InputMask', array('model' => $model, 'attribute' => 'phone')); ?>

<?= $form->error($model, 'phone'); ?>

<?php //echo Html::button(Yii::t('BuyOneClickWidget.default', 'BUTTON_SEND'), array('onclick' => 'buyOneClickSend();', 'class' => 'btn btn-danger wait'));   ?>





<?php $this->endWidget(); ?>
