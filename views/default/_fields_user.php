<?php

use panix\engine\Html;

/**
 * @var $form \panix\engine\bootstrap\ActiveForm
 * @var $model \panix\mod\cart\models\forms\OrderCreateForm
 */
?>
<div class="form-group">
    <?= $form->field($model, 'user_name'); ?>
</div>
<div class="form-group">
    <?= $form->field($model, 'user_phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'user_email'); ?>
</div>

<div class="form-group">
    <?= $form->field($model, 'user_comment')->textarea(['style' => 'resize:none;']); ?>
</div>

<?php if (Yii::$app->user->isGuest && $model->registerGuest) { ?>
    <div class="form-group">
        <?= $form->field($model, 'registerGuest')->checkbox(); ?>
    </div>
<?php } ?>



