<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\ext\select2\Select2;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var \yii\web\View $this
 */

?>

<div class="form-group row field-delivery-type required">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
        <?php echo Html::activeLabel($model, 'address', ['class' => 'col-form-label']); ?>
    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
        <?= Html::activeTextInput($model, 'address', ['class' => 'form-control ' . (($model->getErrors('address')) ? 'is-invalid' : '')]); ?>
        <?= Html::error($model, 'address'); ?>
    </div>
</div>