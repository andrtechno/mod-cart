<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var \yii\web\View $this
 */

?>

<div class="form-group field-delivery-address required <?php if ($model->getErrors('address')) echo "has-error" ?>">
    <?= Html::activeLabel($model, 'address'); ?>
    <?= Html::activeTextInput($model, 'address', ['class' => 'form-control ' . (($model->getErrors('address')) ? 'is-invalid' : '')]); ?>
    <?= Html::error($model, 'address'); ?>
</div>





