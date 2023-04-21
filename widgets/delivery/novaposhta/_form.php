<?php
use panix\engine\bootstrap\ActiveForm;


$form = ActiveForm::begin();

?>
<?php echo $form->field($model, 'api_key')->textInput(['maxlength' => 255]) ?>
<?php echo $form->field($model, 'type_warehouse')->checkboxList(\panix\mod\novaposhta\models\WarehouseTypes::getList2()) ?>
<?php //ActiveForm::end(); ?>
