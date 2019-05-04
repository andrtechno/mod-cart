<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Delivery;
use panix\engine\bootstrap\ActiveForm;

?>
<?php
$form = ActiveForm::begin([
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4 col-lg-4 col-form-label',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8 col-lg-8',
            'error' => '',
            'hint' => '',
        ]
    ]
]);
?>
<?=
$form->field($model, 'status_id')->dropDownList(ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'), [
    'prompt' => '-- статус --'
]);
?>
<?=
$form->field($model, 'payment_id')->dropDownList(ArrayHelper::map(Payment::find()->all(), 'id', 'name'), [
    'prompt' => '-- оплата --'
]);
?>
<?=
$form->field($model, 'delivery_id')->dropDownList(ArrayHelper::map(Delivery::find()->all(), 'id', 'name'), [
    'prompt' => '-- доставка --'
]);
?>
<?= $form->field($model, 'discount')->textInput(); ?>
<?= $form->field($model, 'user_name')->textInput(); ?>
<?= $form->field($model, 'user_address')->textInput(); ?>
<?= $form->field($model, 'user_phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>

<?= $form->field($model, 'user_comment')->textArea(); ?>
<?= $form->field($model, 'admin_comment')->textArea(); ?>
<?= $form->field($model, 'paid')->checkbox(); ?>
<?= $form->field($model, 'invoice')->textInput(['maxlength' => 50]); ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'CREATE') : Yii::t('app', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>