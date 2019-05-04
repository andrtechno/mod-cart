<?php

use panix\engine\Html;

/**
 * @var $form \yii\widgets\ActiveForm
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
    <?= $form->field($model, 'user_comment')->textarea(); ?>
</div>


<?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', ['required' => true, 'class' => 'col-form-label']); ?>
        <?= Html::activeCheckBox($form, 'registerGuest', ['class' => 'form-inline']); ?>
    </div>
<?php } ?>



