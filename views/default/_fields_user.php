<?php
use panix\engine\Html;
?>
<div class="form-group">
    <?= Html::activeLabel($form, 'name', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'name', array('class' => 'form-control')); ?>
    <?= Html::error($form, 'name'); ?>
</div>
<div class="form-group">
    <?= Html::activeLabel($form, 'phone', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'phone', array('class' => 'form-control')); ?>



    <?php
    /*$this->widget('ext.telinput.IntlPhoneInput', array(
        'model' => $form,
        'attribute' => 'phone',
        'htmlOptions' => array('class' => 'form-control'),
        'options' => array(// optional
            // Additional settings.
            'allowExtensions' => true,
            'defaultCountry' => "UA",
        ),
            //'localisedCountryNames'=>false, // other public properties
    ));*/
    ?>






<?= Html::error($form, 'phone'); ?>
</div>

<div class="form-group">
    <?= Html::activeLabel($form, 'email', array('required' => true, 'class' => 'control-label')); ?>
<?= Html::activeTextInput($form, 'email', array('class' => 'form-control')); ?>
    <?= Html::error($form, 'email'); ?>
</div>
<div class="form-group">
<?= Html::activeLabel($form, 'address', array('required' => true, 'class' => 'control-label')); ?>
<?= Html::activeTextInput($form, 'address', array('class' => 'form-control')); ?>
</div>

    <?php if (Yii::$app->user->isGuest && $form->registerGuest) { ?>
    <div class="form-group">
    <?= Html::activeLabel($form, 'registerGuest', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeCheckBox($form, 'registerGuest', array('class' => 'form-inline')); ?>
    </div>
<?php } ?>



