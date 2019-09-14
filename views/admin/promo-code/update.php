<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

$form = ActiveForm::begin();
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?= $form->field($model, 'code')->textInput(['maxlength' => 50]); ?>
        <?= $form->field($model, 'discount')->textInput(['maxlength' => 50]); ?>
        <?= $form->field($model, 'max_use'); ?>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
