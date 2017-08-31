<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\ShopPaymentMethod;
use panix\mod\cart\models\ShopDeliveryMethod;
?>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->context->pageName) ?></h3>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin();
                ?>
                <?=
                $form->field($model, 'status_id')->dropDownList(ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'), [
                    'prompt' => '-- статус --'
                ]);
                ?>
                <?=
                $form->field($model, 'payment_id')->dropDownList(ArrayHelper::map(ShopPaymentMethod::find()->all(), 'id', 'name'), [
                    'prompt' => '-- оплата --'
                ]);
                ?>
                <?=
                $form->field($model, 'delivery_id')->dropDownList(ArrayHelper::map(ShopDeliveryMethod::find()->all(), 'id', 'name'), [
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

            </div>
        </div>
    </div>
    <div class="col-sm-6">
        products
    </div>
</div>