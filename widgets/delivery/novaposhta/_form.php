<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

$form = ActiveForm::begin();

?>
<?= $form->field($model, 'api_key')->textInput(['maxlength' => 255]) ?>
<div class="form-group text-center">
    <?= Html::submitButton(Yii::t('app', 'UPDATE'), ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
