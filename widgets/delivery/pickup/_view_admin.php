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
<div class="form-group row field-delivery-address required">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
        <?php echo Html::activeLabel($model, 'address', ['class' => 'col-form-label']); ?>
    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
        <?php
        echo Html::activeRadioList($model, 'address', $list, ['class' => 'custom-control-inpu2t',
            'item' => function ($index, $label, $name, $checked, $value) use ($model, $activeIndex) {

                $isChecked = '';

                //if ($model->isNewRecord) {
                    if ($index == 0) {
                        $isChecked = 'checked';
                    }
                //} else {
                    $isChecked = ($index == $activeIndex) ? 'checked' : '';
                //}
                $return = '<div class="delivery-radio mb-2"><div class=" custom-control custom-radio ">
                    <input type="radio" ' . $isChecked . ' id="radio-pickup-delivery-' . $index . '" name="' . $name . '" value="' . $value . '" class="custom-control-input">
                    <label class="custom-control-label" for="radio-pickup-delivery-' . $index . '">' . $label . '</label>
                    </div></div>';

                return $return;
            }]);
        echo Html::error($model, 'address');
        ?>
    </div>
</div>



