<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
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
    <div class="panel-body">

        <?= $form->field($model, 'order_emails'); ?>

        <?= $form->field($model, 'tpl_subject_user'); ?>
        <?= $form->field($model, 'tpl_subject_admin'); ?>
        <?= $form->field($model, 'tpl_body_user'); ?>
        <?= $form->field($model, 'tpl_body_admin'); ?>




        <? //= $form->field($model, 'create_btn_action')->dropDownList($model::getButtonIconSizeList(),[]) ?>
    </div>
    <div class="panel-footer text-center">
        <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>