<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

$form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal']
]);
?>
<?php


?>
<?= $form->field($model, 'merchant_id')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'merchant_pass')->textInput(['maxlength' => 255]) ?>




<div class="form-group text-center">
    <?= Html::submitButton(Yii::t('app', 'UPDATE'), ['class' => 'btn btn-success']) ?>
</div>


<?php ActiveForm::end(); ?>
