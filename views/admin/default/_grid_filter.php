<?php

use panix\mod\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="collapse" id="collapse-grid-filter">

    <?php
    $form = ActiveForm::begin([
        'id' => 'form-grid-filter',
        'action' => ['index'],
        'method' => 'GET',
        'options' => [
            'data' => ['pjax' => true]
        ],

    ]);

    echo Html::textInput(Html::getInputName($model, 'status_id'), NULL, ['class' => 'form-control']);
    echo Html::dropDownList(Html::getInputName($model, 'status_id'), NULL, ArrayHelper::map(OrderStatus::find()
        ->addOrderBy(['name' => SORT_ASC])
        ->all(), 'id', 'name'), ['class' => 'form-control']);
    ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('OK', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php
    ActiveForm::end();
    ?>
</div>