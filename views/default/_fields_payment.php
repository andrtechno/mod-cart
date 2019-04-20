<?php

use panix\engine\Html;

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \panix\mod\cart\models\forms\OrderCreateForm
 * @var array $paymenyMethods \panix\mod\cart\models\Payment
 */
?>
<div class="form-group">

    <?php

  //  echo Html::activeLabel($model, 'payment_id');
   // foreach ($paymenyMethods as $pay) {
       /* echo '<div>';
        echo Html::activeRadio($model, 'payment_id', [
            'label' => $pay->name,
            'checked' => ($model->payment_id == $pay->id),
            'uncheck' => false,
            'value' => $pay->id,
            'data-value' => Html::encode($pay->name),
            //'id' => 'payment_id_' . $pay->id,
            'class' => 'payment_checkbox'
        ]);
        echo Html::error($model, 'payment_id');
        echo '</div>';*/
  //  }

    echo $form->field($model,'payment_id')->radioList(\yii\helpers\ArrayHelper::map($paymenyMethods,'id','name'),[
        'item' => function($index, $label, $name, $checked, $value) {
            $return = '<div><label class="payment_checkbox" data-value="'.Html::encode($label).'">';
            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"> ';
            $return .= $label;
            $return .= '</label></div>';
            return $return;
        }
    ]);
    ?>
</div>
