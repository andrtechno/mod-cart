<?php

use panix\mod\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;


$class = ($model->status_id || $model->delivery_city) ? 'show' : '';


?>

<div class="collapse <?= $class; ?>" id="collapse-grid-filter">
    <div class="pr-3 pl-3 pt-3">
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
                    /* echo Html::dropDownList(Html::getInputName($model, 'status_id'), $model->status_id, ArrayHelper::map(OrderStatus::find()
                         ->addOrderBy(['name' => SORT_ASC])
                         ->all(), 'id', 'name'), [
                         'class' => 'form-control',
                         'prompt' => html_entity_decode('&mdash;'),
                         'id' => Html::getInputId($model, 'status_id')
                     ]);*/

                    echo Html::activeDropDownList($model, 'status_id', ArrayHelper::map(OrderStatus::find()
                        ->addOrderBy(['name' => SORT_ASC])
                        ->all(), 'id', 'name'), [
                        'class' => 'form-control',
                        'prompt' => html_entity_decode('&mdash;'),
                        'id' => Html::getInputId($model, 'status_id')
                    ])
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
            </div>
            <div class="col-sm-4">

                <div class="form-check">
                    <?php

                    echo Html::activeCheckbox($model, 'buyOneClick', [
                        'class' => 'form-check-input',
                    ]);
                 //   echo Html::activeLabel($model, 'buyOneClick', ['class' => 'form-check-label']);
                    ?>
                </div>

                <div class="form-check">
                    <?php

                    echo Html::activeCheckbox($model, 'call_confirm', [
                        'class' => 'form-check-input',
                    ]);
                   // echo Html::activeLabel($model, 'call_confirm', ['class' => 'form-check-label']);
                    ?>
                </div>

                <div class="form-check">
                    <?php

                    echo Html::activeCheckbox($model, 'paid', [
                        'class' => 'form-check-input',
                    ]);
                 //   echo Html::activeLabel($model, 'paid', ['class' => 'form-check-label']);
                    ?>

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