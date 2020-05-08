<?php

use panix\engine\Html;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Delivery;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\telinput\PhoneInput;
use panix\engine\CMS;
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
        ?>
        <?= $form->field($model, 'ttn')->textInput()->hint('После заполнение ТТН, клиенту будет отправлено уведомление на почту.'); ?>
        <?= $form->field($model, 'paid')->checkbox(); ?>
        <?= $form->field($model, 'user_name')->textInput(); ?>
        <?= $form->field($model, 'user_address')->textInput(); ?>
        <?= $form->field($model, 'user_email')->textInput(); ?>
        <?php
        if (!$model->isNewRecord && $model->user_phone) { ?>
            <div class="form-group row">
                <div class="col-sm-4 col-lg-2">
                    <?= Html::activeLabel($model, 'user_phone',['class'=>'col-form-label']); ?>
                </div>
                <div class="col-sm-8 col-lg-10">
                    <?php
                    echo PhoneInput::widget(['model' => $model, 'attribute' => 'user_phone']);
                    ?>
                    <?= Html::a(Html::icon('phone').' Позвонить','tel:'.$model->user_phone,['class'=>'ml-lg-3 mt-lg-0 mt-2 d-lg-inline-block d-block text-center text-lg-center']); ?>
                </div>
            </div>
        <?php } else {
            echo $form->field($model, 'user_phone')->widget(PhoneInput::class);
        }
        ?>
        <?= $form->field($model, 'user_comment')->textArea(); ?>
        <?= $form->field($model, 'admin_comment')->textArea(); ?>

        <?= $form->field($model, 'discount')->textInput(); ?>
        <?= $form->field($model, 'invoice')->textInput(['maxlength' => 50]); ?>
    </div>
    <div class="card-footer text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/default', 'CREATE') : Yii::t('app/default', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>