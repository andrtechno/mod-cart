<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

//$form = ActiveForm::begin();

?>
<div class="form-group row field-delivery-address required">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-2">

    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-10">
        <?php

        echo \panix\ext\multipleinput\MultipleInput::widget([
            // 'rendererClass'=>\panix\ext\multipleinput\renderers\TableLanguageRenderer::class,
            //  'columnClass'=>\panix\ext\multipleinput\MultipleInputColumn::class,
            'id'=>'pickup-multiple',
            'model' => $model,
            'attribute' => 'address',
            //  'max' => 7,
            'min' => 1,
            'cloneButton' => false,
            'allowEmptyList' => false,
            'enableGuessTitle' => true,
            'addButtonPosition' => \panix\ext\multipleinput\MultipleInput::POS_HEADER, // show add button in the header
            'columns' => [

                [
                    //'type'  => \panix\ext\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                    'name' => 'name',
                    'title' => Yii::t('cart/Delivery','ADDRESS'),
                    'enableError' => true,
                    'options' => ['class' => 'form-control m-auto', 'autocomplete' => 'off'],
                    'columnOptions' => ['class' => 'text-center'],
                    'headerOptions' => [
                        // 'style' => 'width: 100px;',
                    ],
                ],
                [
                    'type'  => \panix\engine\jui\DatetimePicker::class,
                    'name' => 'from',
                    'title' => Yii::t('app','с'),
                    'enableError' => true,
                    'options' => [
                        'mode'=>'time',
                        'timeFormat' => 'HH:mm'
                    ],
                    'columnOptions' => ['class' => 'text-center'],
                    'headerOptions' => [
                        'style' => 'width: 100px;',
                    ],
                ],
                [
                    'type'  => \panix\engine\jui\DatetimePicker::class,
                    'name' => 'to',
                    'title' => Yii::t('app','по'),
                    'enableError' => true,
                    'options' => [
                        'mode'=>'time',
                        'timeFormat' => 'HH:mm'
                    ],
                    'columnOptions' => ['class' => 'text-center'],
                    'headerOptions' => [
                        'style' => 'width: 100px;',
                    ],
                ],
            ]
        ]);
        ?>
    </div>
</div>


<?php //ActiveForm::end(); ?>
