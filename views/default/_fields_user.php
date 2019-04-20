<?php

use panix\engine\Html;
use panix\engine\widgets\inputmask\InputMask;
/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \panix\mod\cart\models\forms\OrderCreateForm
 */
?>
<div class="form-group">
    <?= $form->field($model, 'user_name'); ?>
</div>
<div class="form-group">

    <?= $form->field($model, 'user_phone'); ?>

</div>


<div class="form-group">
    <?= $form->field($model, 'user_email'); ?>
</div>


<div class="form-group">
    <?= $form->field($model, 'user_address')->textarea(); ?>
</div>


<?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', ['required' => true, 'class' => 'col-form-label']); ?>
        <?= Html::activeCheckBox($form, 'registerGuest', ['class' => 'form-inline']); ?>
    </div>
<?php } ?>



