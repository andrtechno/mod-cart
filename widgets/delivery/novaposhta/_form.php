<?php
use panix\engine\bootstrap\ActiveForm;


$form = ActiveForm::begin();

?>
<?php echo $form->field($model, 'api_key')->textInput(['maxlength' => 255]) ?>
<?php //ActiveForm::end(); ?>
