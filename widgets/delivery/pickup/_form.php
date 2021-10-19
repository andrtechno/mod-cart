<?php

use panix\engine\bootstrap\ActiveForm;
use panix\engine\Html;

//$form = ActiveForm::begin();

?>

<?php


$model->address = $model->address['name'];

//\panix\engine\CMS::dump($model->address);
echo \panix\ext\multipleinput\MultipleInput::widget([
    // 'rendererClass'=>\panix\ext\multipleinput\renderers\TableLanguageRenderer::class,
    //  'columnClass'=>\panix\ext\multipleinput\MultipleInputColumn::class,
    'id' => 'pickup-multiple',
    'model' => $model,
    'attribute' => 'address',
    //  'max' => 7,
    'min' => 1,

    'allowEmptyList' => false,

    'addButtonPosition' => \panix\ext\multipleinput\MultipleInput::POS_HEADER, // show add button in the header
    'columns' => [

        [
            'type'  => \panix\ext\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
            'name' => 'name',
            'title' => Yii::t('app', 'Address'),
            'enableError' => true,
            'options' => ['class' => 'form-control m-auto', 'autocomplete' => 'off'],
            'columnOptions' => ['class' => 'text-center'],
            'headerOptions' => [
                // 'style' => 'width: 100px;',
            ],
        ],


    ]
]);
?>
<?php //ActiveForm::end(); ?>
