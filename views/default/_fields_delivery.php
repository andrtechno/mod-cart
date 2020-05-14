<?php

use panix\engine\Html;

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \panix\mod\cart\models\forms\OrderCreateForm
 * @var $deliveryMethods \panix\mod\cart\models\Delivery
 * @var $this \yii\web\View
 */

?>
<?php if ($deliveryMethods) { ?>
    <div class="form-group required ">

        <?php

        echo Html::activeLabel($model, 'delivery_id');
        //  echo Html::activeRadioList($form, 'delivery_id', \yii\helpers\ArrayHelper::map($deliveryMethods, 'id', 'name'));
        foreach ($deliveryMethods as $delivery) {
            echo '<div>';

            echo Html::activeRadio($model, 'delivery_id', [
                'label' => $delivery->name,
                'uncheck' => false,
                'checked' => ($model->delivery_id == $delivery->id),
                'value' => $delivery->id,
                'data-price' => Yii::$app->currency->convert($delivery->price),
                'data-free-from' => Yii::$app->currency->convert($delivery->free_from),
                'onClick' => 'cart.recountTotalPrice(this); cart.delivery();',
                'data-value' => Html::encode($delivery->name),
                //'id' => 'delivery_id_' . $delivery->id,
                'class' => 'delivery_checkbox'
            ]);
            ?>

            <?php
            if (!empty($delivery->description)) { ?>
                <?= $delivery->description ?>
            <?php } ?>
            <?php
            echo '</div>';
        }

        /* echo $form->field($model,'payment_id')->radioList(\yii\helpers\ArrayHelper::map($deliveryMethods,'id','name'),[
             'item' => function($index, $label, $name, $checked, $value) {
                 $return = '<div><label class="payment_checkbox" data-value="'.Html::encode($label).'">';
                 $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"> ';
                 $return .= $label;
                 $return .= '</label></div>';
                 return $return;
             }
         ]);*/
        ?>


        <?= Html::error($model, 'delivery_id', ['class' => 'help-block']); ?>
        <div id="delivery-form"></div>
        <div id="user-address-input"><?= $form->field($model, 'user_address') ?></div>

        <?php // Html::activeLabel($model, 'user_address', array('required' => true, 'class' => 'col-form-label')); ?>
        <?php // Html::activeTextInput($model, 'user_address', array('class' => 'form-control')); ?>

    </div>
    <?php
} else {
    echo 'Необходимо добавить способ доставки!';
}
?>

