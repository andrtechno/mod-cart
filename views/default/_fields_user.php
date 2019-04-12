<?php

use panix\engine\Html;
use panix\engine\widgets\inputmask\InputMask;

/** @var $form \panix\mod\cart\models\forms\OrderCreateForm */
?>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_name', ['required' => true, 'class' => 'col-form-label']); ?>
    <?= Html::activeTextInput($form, 'user_name', ['class' => 'form-control']); ?>
    <?= Html::error($form, 'user_name'); ?>
</div>
<div class="form-group">
    <?= Html::activeLabel($form, 'user_phone', ['required' => true, 'class' => 'control-label']); ?>

    <?php echo InputMask::widget([
        'model' => $form,
        'attribute' => 'user_phone'
    ]);
    ?>
</div>


<div class="form-group">
    <?= Html::activeLabel($form, 'user_email', ['required' => true, 'class' => 'control-label']); ?>
    <?= Html::activeTextInput($form, 'user_email', ['class' => 'form-control']); ?>
    <?= Html::error($form, 'user_email'); ?>
</div>


<?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', ['required' => true, 'class' => 'control-label']); ?>
        <?= Html::activeCheckBox($form, 'registerGuest', ['class' => 'form-inline']); ?>
    </div>
<?php } ?>



