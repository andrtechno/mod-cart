<?php

use panix\mod\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;


$class = ($model->status_id || $model->delivery_city) ? 'show' : '';


?>

<div class="collapse <?= $class; ?>" id="collapse-grid-filter">
    <div class="p-3">
        <?php

        /*$form = ActiveForm::begin([
            'id' => 'form-grid-filter',
            'action' => ['index'],
            'method' => 'GET',
            'options' => [
                'data' => ['pjax' => true]
            ],

        ]);*/
        ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <?php
                    echo Html::activeLabel($model, 'status_id');
                        echo Html::activeDropDownList($model, 'status_id', ArrayHelper::map(OrderStatus::find()
                        ->addOrderBy(['name' => SORT_ASC])
                        ->all(), 'id', 'name'), [
                        'class' => 'form-control',
                        'prompt' => html_entity_decode('&mdash;'),
                        'id' => Html::getInputId($model, 'status_id')
                    ])
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo Html::activeLabel($model, 'ttn');
                    echo Html::activeTextInput($model, 'ttn', [
                        'class' => 'form-control',
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php
                    echo Html::activeLabel($model, 'delivery_city');
                    echo Html::activeTextInput($model, 'delivery_city', [
                        'class' => 'form-control',
                    ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo Html::activeLabel($model, 'delivery_address');
                    echo Html::activeTextInput($model, 'delivery_address', [
                        'class' => 'form-control',
                    ]);
                    ?>
                </div>

            </div>
            <div class="col-sm-4">
                <div class="form-check">
                    <?= Html::activeCheckbox($model, 'buyOneClick', ['class' => 'form-check-input']); ?>
                </div>
                <div class="form-check">
                    <?= Html::activeCheckbox($model, 'call_confirm', ['class' => 'form-check-input']); ?>
                </div>
                <div class="form-check">
                    <?= Html::activeCheckbox($model, 'paid', ['class' => 'form-check-input']); ?>
                </div>
                <div class="form-check">
                    <?= Html::activeCheckbox($model, 'apply_user_points', ['class' => 'form-check-input']); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton('Применить фильтр', ['class' => 'btn btn-sm btn-primary']) ?>
    </div>
    <?php
    // ActiveForm::end();
    ?>
</div>