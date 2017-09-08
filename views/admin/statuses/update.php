<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\colorpicker\Colorpicker;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->context->pageName) ?></h3>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'form-horizontal']
        ]);
        ?>
        <?php
        ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
        <?php //echo $form->field($model, 'color')->textInput(['maxlength' => 7]) ?>
        <?php
        echo $form->field($model, 'color')->widget(Colorpicker::className(), [
        ])->textInput(['maxlength' => 7]);
        ?>

        <div class="form-group text-center">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'CREATE') : Yii::t('app', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

