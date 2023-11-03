<?php
use panix\engine\bootstrap\ActiveForm;
use panix\mod\cart\widgets\delivery\meest\api\MeestApi;

$form = ActiveForm::begin();

?>
<?php echo $form->field($model, 'type_warehouse')->checkboxList(MeestApi::getList()) ?>
<?php //ActiveForm::end(); ?>
