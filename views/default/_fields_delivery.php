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


    <?php

    //echo Html::activeLabel($model, 'delivery_id');
    //  echo Html::activeRadioList($form, 'delivery_id', \yii\helpers\ArrayHelper::map($deliveryMethods, 'id', 'name'));
    foreach ($deliveryMethods as $delivery) {
        // echo '<div>';

        /*echo Html::activeRadio($model, 'delivery_id', [
            'label' => $delivery->name,
            'uncheck' => false,
            'checked' => ($model->delivery_id == $delivery->id),
            'value' => $delivery->id,
            'data-price' => Yii::$app->currency->convert($delivery->price),
            'data-free-from' => Yii::$app->currency->convert($delivery->free_from),
            'onClick' => 'cart.recountTotalPrice(this); cart.delivery(this);',
            'data-value' => Html::encode($delivery->name),
            //'id' => 'delivery_id_' . $delivery->id,
            'class' => 'delivery_checkbox'
        ]);*/
        ?>

        <?php
        if (!empty($delivery->description)) { ?>
            <?php //echo $delivery->description ?>
        <?php } ?>
        <?php
        //  echo '</div>';
    }

    echo $form->field($model, 'delivery_id')->radioList(\yii\helpers\ArrayHelper::map($deliveryMethods, function ($model) {
        //return ($model->system) ? $model->system : $model->id;
        return $model->id;
    }, function ($model) {
        $html = '';
        if ($model->free_from) {
            $html .= ' <small class="d-block text-muted">Бесплатно от &mdash; ' . Yii::$app->currency->number_format($model->free_from) . ' ' . Yii::$app->currency->active['symbol'] . '</small>';
        }
        if ($model->description) {
            $html .= ' <small class="d-block text-muted">&mdash; ' . strip_tags($model->description) . '</small>';
        }
        return $model->name . $html;
    }), [
        'item' => function ($index, $label, $name, $checked, $value) {
            $isChecked = ($index == 0) ? 'checked' : '';
            $return = '<div class="mb-2 custom-control custom-radio">
                    <input type="radio" ' . $isChecked . ' id="radio-delivery-' . $index . '" name="' . $name . '" value="' . $value . '" class="custom-control-input" onClick="cart.delivery(this);">
                    <label class="custom-control-label delivery_checkbox" for="radio-delivery-' . $index . '"><strong>' . $label . '</strong></label>
                    </div>';

            return $return;
        }
    ]);
    ?>


    <?php //echo Html::error($model, 'delivery_id', ['class' => 'help-block']); ?>
    <div id="delivery-form"></div>
    <div id="user-address-input"><?= $form->field($model, 'delivery_address') ?></div>

    <?php // Html::activeLabel($model, 'delivery_address', array('required' => true, 'class' => 'col-form-label')); ?>
    <?php // Html::activeTextInput($model, 'delivery_address', array('class' => 'form-control')); ?>


    <?php
} else {
    echo 'Необходимо добавить способ доставки!';
}
?>

