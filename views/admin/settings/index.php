<?php

use panix\engine\Html;
use yii\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
            //  'id' => 'form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-7\">{input}</div>\n<div class=\"col-sm-7 col-sm-offset-5\">{error}</div>",
                'labelOptions' => ['class' => 'col-sm-5 control-label'],
                ],
        ]);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= $this->context->pageName ?></h3>
    </div>
    <div class="panel-body panel-body-form">
        <?= $form->field($model, 'per_page') ?>
        <?= $form->field($model, 'auto_fill_short_desc')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'save_fields_on_create')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'wholesale')->checkBox(['label' => null])->label(); ?>
        
        <?= $form->field($model, 'filter_enable_brand')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'filter_enable_price')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'filter_enable_attr')->checkBox(['label' => null])->label(); ?>
        
        
        <?//= $form->field($model, 'create_btn_action')->dropDownList($model::getButtonIconSizeList(),[]) ?>
    </div>
    <div class="panel-footer text-center">
        <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>