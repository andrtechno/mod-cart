<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\taginput\TagInput;
use panix\ext\tinymce\TinyMce;

$form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
]);
?>
    <div class="card bg-light">
        <div class="card-header">
            <h5><?= $this->context->pageName ?></h5>
        </div>
        <div class="card-body">
            <?=
            $form->field($model, 'order_emails')
                ->widget(TagInput::class, ['placeholder' => 'E-mail'])
                ->hint('Введите E-mail и нажмите Enter');
            ?>

            <?= $form->field($model, 'tpl_subject_user'); ?>
            <?= $form->field($model, 'tpl_subject_admin'); ?>


            <?= $form->field($model, 'tpl_body_user')->widget(TinyMce::class, [
                'options' => ['rows' => 6],

            ]);
            ?>
            <?= $form->field($model, 'tpl_body_admin')->widget(TinyMce::class, [
                'options' => ['rows' => 6],

            ]);
            ?>


        </div>
        <div class="card-footer text-center">
            <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>