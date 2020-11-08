<?php

use panix\engine\Html;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Delivery;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\telinput\PhoneInput;
use panix\engine\CMS;


$form = ActiveForm::begin([
    'id' => 'order-form',
    'fieldConfig' => [
        'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-4\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'offset' => 'offset-sm-4 offset-lg-3 offset-xl-4',
            'wrapper' => 'col-sm-8 col-md-8 col-lg-9 col-xl-8',
        ],
    ]
]);
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
        <div id="delivery-form"></div>
        <?= $form->field($model, 'delivery_address')->textInput(); ?>
        <?= $form->field($model, 'ttn')->textInput()->hint('После заполнение ТТН, клиенту будет отправлено уведомление на почту.'); ?>
        <?= $form->field($model, 'paid')->checkbox(); ?>
        <?= $form->field($model, 'user_name')->textInput(); ?>
        <?= $form->field($model, 'user_email')->textInput(); ?>
        <?php
        if (!$model->isNewRecord && $model->user_phone) { ?>
            <?= $form->field($model, 'user_phone', [
                'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-4\">{label}</div>\n{hint}\n{beginWrapper}{input}{call}\n{error}{endWrapper}",
                'parts' => [
                    '{call}' => Html::a(Html::icon('phone') . ' Позвонить &mdash; <strong>' . CMS::phoneOperator($model->user_phone) . '</strong>', 'tel:' . $model->user_phone, ['class' => 'mt-2 mt-lg-0 float-none float-lg-right btn btn-light'])
                ]
            ])->widget(PhoneInput::class); ?>


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