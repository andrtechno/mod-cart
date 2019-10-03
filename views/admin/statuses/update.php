<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\colorpicker\Colorpicker;

?>
<?php
$form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal']
]);
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
        <?php //echo $form->field($model, 'color')->textInput(['maxlength' => 7]) ?>
        <?= $form->field($model, 'color')->widget(Colorpicker::class)->textInput(['maxlength' => 7]); ?>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
