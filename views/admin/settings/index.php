<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\taginput\TagInput;
use panix\ext\tinymce\TinyMce;
$form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal'],
        ]);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= $this->context->pageName ?></h3>
    </div>
    <div class="panel-body">
        <?=
                $form->field($model, 'order_emails')
                ->widget(TagInput::className(), ['placeholder' => 'E-mail'])
                ->hint('Введите E-mail и нажмите Enter');
        ?>

        <?= $form->field($model, 'tpl_subject_user'); ?>
        <?= $form->field($model, 'tpl_subject_admin'); ?>


<?= $form->field($model, 'tpl_body_user')->widget(TinyMce::className(), [
    'options' => ['rows' => 6],

]);
?>
<?= $form->field($model, 'tpl_body_admin')->widget(TinyMce::className(), [
    'options' => ['rows' => 6],

]);
?>


    </div>
    <div class="panel-footer text-center">
        <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>