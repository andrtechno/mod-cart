<?php
use panix\engine\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this \yii\web\View
 */

if ($sended) { ?>
    <div class="alert alert-success">
        <?= Yii::t('BuyOneClickWidget/default', 'SUCCESS'); ?>
    </div>
    <?php
    return false;
}

?>
<div style="display: inline-block;width: 400px">


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
                            <span class="price price-xs price-discount"><?= $productModel->toCurrentCurrency('originalPrice') ?>
                                <sub><?= Yii::$app->currency->active['symbol'] ?></sub></span>
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
        'id' => 'buyOneClick-form',
        'options' => [],
       // 'enableAjaxValidation' => true,
    ]);

    echo $form->field($model, 'quantity')->hiddenInput(['value' => $quantity])->error(false)->label(false);
   // echo $form->field($model, 'user_phone');
    echo $form->field($model, 'user_phone', ['options' => ['class' => 'form-group form-group-auto2']])->widget(\panix\ext\telinput\PhoneInput::class);
?>


    <?php echo Html::submitButton(Yii::t('cart/default', 'BUY'), ['class' => 'btn btn-danger d-block btn-buy']); ?>
    <?php ActiveForm::end() ?>
</div>

