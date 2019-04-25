<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

use yii\helpers\ArrayHelper;
use panix\ext\tinymce\TinyMce;
use panix\mod\shop\models\Currency;

?>


<div class="card bg-light">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">


        <?php
        $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal']
        ]);
        ?>
        <?php


        ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        <?=
        $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(Currency::find()->all(), 'id', 'name'), [
            'prompt' => '-- статус --'
        ]);
        ?>
        <?=
        $form->field($model, 'payment_system')->dropDownList($model->getPaymentSystemsArray(), [
            'prompt' => '-- статус --',
            'rel' => $model->id
        ]);
        ?>
        <div id="payment_configuration"></div>
        <?= $form->field($model, 'description')->widget(TinyMce::class, [
            'options' => ['rows' => 6]
        ]);
        ?>


        <div class="form-group text-center">
            <?= $model->submitButton(); ?>
        </div>


        <?php ActiveForm::end(); ?>


    </div>
</div>

