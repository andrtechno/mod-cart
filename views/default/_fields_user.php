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
    <?php // $form->field($model, 'user_email'); ?>
</div>
<?php
/*echo $form->field($model, 'user_email')->widget(\panix\ext\inputmask\InputMask::class, [
    'pluginOptions' => [
        'mask' => '999-99-99'
    ]
]);*/

echo $form->field($model, 'user_email')->widget(\panix\ext\inputmask\InputMask::class, [
    'extensions' => ['date'],
    'pluginOptions' => [
        'mask' => 'dd/mm/yyyy'
    ]
]);
?>

<div class="form-group">
    <?= $form->field($model, 'user_comment')->textarea(); ?>
</div>


<?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
        <?= Html::activeLabel($form, 'registerGuest', ['required' => true, 'class' => 'col-form-label']); ?>
        <?= Html::activeCheckBox($form, 'registerGuest', ['class' => 'form-inline']); ?>
    </div>
<?php } ?>



