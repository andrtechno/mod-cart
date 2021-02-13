<?php
use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

//$form = ActiveForm::begin();

?>

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
            'title' => Yii::t('app','Address'),
            'enableError' => true,
            'options' => ['class' => 'form-control m-auto', 'autocomplete' => 'off'],
            'columnOptions' => ['class' => 'text-center'],
            'headerOptions' => [
               // 'style' => 'width: 100px;',
            ],
           /* 'items' =>  [
                31 => 'item 31',
                32 => 'item 32',
                33 => 'item 33',
                34 => 'item 34',
                35 => 'item 35',
                36 => 'item 36',
            ]*/
        ],

    ]
]);
?>
<?php //ActiveForm::end(); ?>
