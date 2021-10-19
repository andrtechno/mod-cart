<?php
use panix\engine\Html;
use panix\engine\CMS;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */
//CMS::dump($model);

$js2 = <<<JS

$('#order-form').yiiActiveForm('add', {
    id: 'order-delivery_type',
    name: 'delivery_type',
    container: '.field-order-delivery_type',
    input: '#order-delivery_type',
    error: '.invalid-feedback',
    validate:  function (attribute, value, messages, deferred, form) {
        yii.validation.required(value, messages, {message: 'errr'});
        console.log('validate',attribute,value);
        if(value){
            $(attribute.container).removeClass('field-is-invalid');
        }else{
            $(attribute.container).addClass('field-is-invalid');
        }
    }
});

JS;
$this->registerJs($js2, \yii\web\View::POS_END, 'rrrr');



?>

adsdsadadasdsa


