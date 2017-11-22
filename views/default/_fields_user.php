<?php

use panix\engine\Html;
use panix\engine\widgets\inputmask\InputMask;
?>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_name', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_name', array('class' => 'form-control')); ?>
<?= Html::error($form, 'user_name'); ?>
</div>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_phone', array('required' => true, 'class' => 'control-label')); ?>

        <?php echo InputMask::widget([
            'model'=>$form,
            'attribute'=>'user_phone'
        ]);
        ?>
</div>









<div class="form-group">
    <?= Html::activeLabel($form, 'user_email', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_email', array('class' => 'form-control')); ?>
<?= Html::error($form, 'user_email'); ?>
</div>


    <?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeCheckBox($form, 'registerGuest', array('class' => 'form-inline')); ?>
    </div>
<?php } ?>



