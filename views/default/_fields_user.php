<?php

use panix\engine\Html;
?>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_name', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_name', array('class' => 'form-control')); ?>
<?= Html::error($form, 'user_name'); ?>
</div>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_phone', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_phone', array('class' => 'form-control')); ?>

<?= Html::error($form, 'user_phone'); ?>
</div>

<div class="form-group">
    <?= Html::activeLabel($form, 'user_email', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_email', array('class' => 'form-control')); ?>
<?= Html::error($form, 'user_email'); ?>
</div>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_address', array('required' => true, 'class' => 'control-label')); ?>
<?= Html::activeTextInput($form, 'user_address', array('class' => 'form-control')); ?>
</div>

    <?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeCheckBox($form, 'registerGuest', array('class' => 'form-inline')); ?>
    </div>
<?php } ?>



