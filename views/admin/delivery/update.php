<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\Payment;
use panix\ext\tinymce\TinyMce;

?>

<?php
$form = ActiveForm::begin();
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'price')->textInput(['maxlength' => 255]) ?>
        <?= $form->field($model, 'free_from')->textInput(['maxlength' => 255]) ?>
        <?=
        $form->field($model, 'system')->dropDownList($model->getDeliverySystemsArray(), [
            'data-id'=>$model->id
        ]);
        ?>
        <div id="delivery_configuration"></div>
        <?=
        $form->field($model, 'payment_methods')->dropDownList(ArrayHelper::map(Payment::find()->all(), 'id', 'name'), [
            'prompt' => html_entity_decode(Yii::t('cart/default','SELECT_SYSTEM_PAYMENT')),
            'multiple' => true
        ]);
        ?>
        <div id="payment_configuration"></div>
        <?= $form->field($model, 'description')->widget(TinyMce::class, [
            'options' => ['rows' => 6]
        ]);
        ?>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
