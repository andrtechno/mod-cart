<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\PaymentMethod;
use panix\mod\cart\models\DeliveryMethod;
?>
                <?php
                $form = ActiveForm::begin();
                ?>
                <?=
                $form->field($model, 'status_id')->dropDownList(ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'), [
                    'prompt' => '-- статус --'
                ]);
                ?>
                <?=
                $form->field($model, 'payment_id')->dropDownList(ArrayHelper::map(PaymentMethod::find()->all(), 'id', 'name'), [
                    'prompt' => '-- оплата --'
                ]);
                ?>
                <?=
                $form->field($model, 'delivery_id')->dropDownList(ArrayHelper::map(DeliveryMethod::find()->all(), 'id', 'name'), [
                    'prompt' => '-- доставка --'
                ]);
                ?>
                <?= $form->field($model, 'discount')->textInput() ?>
                <?= $form->field($model, 'user_name')->textInput() ?>
                <?= $form->field($model, 'user_address')->textInput() ?>
                <?= $form->field($model, 'user_phone')->textInput() ?>
                <?= $form->field($model, 'user_comment')->textArea() ?>
                <?= $form->field($model, 'admin_comment')->textArea() ?>
                <?= $form->field($model, 'paid')->checkbox() ?>


                <div class="form-group text-center">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'CREATE') : Yii::t('app', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>