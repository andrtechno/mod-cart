<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Delivery;
use panix\engine\bootstrap\ActiveForm;

?>
<?php
$form = ActiveForm::begin();
?>
    <div class="card-body">

        <?=
        $form->field($model, 'status_id')->dropDownList(ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'));
        ?>
        <?=
        $form->field($model, 'payment_id')->dropDownList(ArrayHelper::map(Payment::find()->all(), 'id', 'name'), [
            'prompt' => html_entity_decode($model::t('SELECT_PAYMENT'))
        ]);
        ?>
        <?=
        $form->field($model, 'delivery_id')->dropDownList(ArrayHelper::map(Delivery::find()->all(), 'id', 'name'), [
            'prompt' => html_entity_decode($model::t('SELECT_DELIVERY'))
        ]);
        <?= $form->field($model, 'ttn')->textInput()->hint('После заполнение ТТН, клиенту будет отправлено уведомление на почту.'); ?>
        <?= $form->field($model, 'paid')->checkbox(); ?>
        <?= $form->field($model, 'user_name')->textInput(); ?>
        <?= $form->field($model, 'user_address')->textInput(); ?>
        <?= $form->field($model, 'user_email')->textInput(); ?>
        <?= $form->field($model, 'user_phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
        <?= $form->field($model, 'user_comment')->textArea(); ?>
        <?= $form->field($model, 'admin_comment')->textArea(); ?>

        <?= $form->field($model, 'discount')->textInput(); ?>
        <?= $form->field($model, 'invoice')->textInput(['maxlength' => 50]); ?>
    </div>
    <div class="card-footer text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/default', 'CREATE') : Yii::t('app/default', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>