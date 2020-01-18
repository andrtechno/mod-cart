<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

$form = ActiveForm::begin();

?>
<?= $form->field($model, 'api_key')->textInput(['maxlength' => 255]) ?>
<div class="form-group row text-center">
    <div class="col">
        <?= Html::submitButton(Yii::t('app/default', 'UPDATE'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
