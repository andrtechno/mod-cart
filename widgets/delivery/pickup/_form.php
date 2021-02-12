<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

$form = ActiveForm::begin();

?>

<?php


echo \panix\ext\multipleinput\MultipleInput::widget([
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
            'name' => 'volumetricWeight',
            'title' => Yii::t('app','WEIGHT'),
            'enableError' => true,
            'options' => ['class' => 'form-control m-auto', 'autocomplete' => 'off', 'placeholder' => Yii::t('app','Например: 1.5')],
            'columnOptions' => ['class' => 'text-center'],
            'headerOptions' => [
               // 'style' => 'width: 100px;',
            ],
        ],

    ]
]);


?>
<?php ActiveForm::end(); ?>
