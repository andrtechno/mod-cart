<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\colorpicker\ColorPicker;

$form = ActiveForm::begin();

/**
 * @var $this \yii\web\View
 * @var $model \panix\mod\cart\models\OrderStatus
 */
?>

<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?php if($model->id == \panix\mod\cart\models\Order::STATUS_SUBMITTED){ ?>
            <div class="alert alert-warning">На этот статус производиться <strong>начисление</strong> бонусов</div>
        <?php }elseif($model->id == \panix\mod\cart\models\Order::STATUS_RETURN){ ?>
            <div class="alert alert-warning">На этот статус производиться <strong>снятие</strong> бонусов</div>
        <?php } ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
        <?= $form->field($model, 'color')->widget(ColorPicker::class)->textInput(['maxlength' => 7]); ?>
        <?= $form->field($model, 'use_in_stats')->checkbox(); ?>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
