<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \panix\mod\cart\models\Order $model
 * @var ActiveForm $form
 */

?>

<div class="form-group field-delivery-address required">


    <?php
    echo Html::activeLabel($deliveryModel, 'address', ['class' => 'custom-control-label2 ']);
    echo Html::activeRadioList($deliveryModel, 'address', $list, ['class' => 'custom-control-inpu2t',
        'item' => function ($index, $label, $name, $checked, $value) use ($model) {

            $isChecked = '';


            if ($index == 0) {
                $isChecked = 'checked';
            }

            $return = '<div class="delivery-radio mb-2"><div class=" custom-control custom-radio">
                    <input type="radio" ' . $isChecked . ' id="radio-pickup-delivery-' . $index . '" name="' . $name . '" value="' . $value . '" class="custom-control-input">
                    <label class="custom-control-label" for="radio-pickup-delivery-' . $index . '">' . $label . '</label>
                    </div></div>';

            return $return;
        }]);
    echo Html::error($deliveryModel, 'address');
    ?>

</div>



