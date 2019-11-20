<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

use yii\helpers\ArrayHelper;
use panix\ext\tinymce\TinyMce;
use panix\mod\shop\models\Currency;

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
        <?=
        $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(Currency::find()->all(), 'id', 'name'), [
            'prompt' => Yii::t('shop/Product','SELECT_CURRENCY', [
                'currency' => Yii::$app->currency->main['iso']
            ])
        ]);
        ?>
        <?=
        $form->field($model, 'payment_system')->dropDownList($model->getPaymentSystemsArray(), [
            'prompt' => html_entity_decode(Yii::t('cart/default','SELECT_SYSTEM_PAYMENT')),
            'rel' => $model->id
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
