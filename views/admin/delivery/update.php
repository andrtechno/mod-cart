<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\Payment;
use panix\ext\tinymce\TinyMce;

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
        <?= $form->field($model, 'price')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'free_from')->textInput(['maxlength' => 255]) ?>
        <?=
        $form->field($model, 'payment_methods')->dropDownList(ArrayHelper::map(Payment::find()->all(), 'id', 'name'), [
            'prompt' => '-- payment --'
        ]);
        ?>
        <div id="payment_configuration"></div>
        <?= $form->field($model, 'description')->widget(TinyMce::class, [
            'options' => ['rows' => 6]
        ]);
        ?>


        <div class="form-group text-center">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'CREATE') : Yii::t('app', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

