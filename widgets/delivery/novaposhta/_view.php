<?php
use panix\engine\Html;
use panix\engine\CMS;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */
\yii\widgets\ActiveFormAsset::register($this);
$js2 = <<<JS

$('#cartForm').yiiActiveForm('add', {
    id: 'ordercreateform-delivery_type',
    name: 'delivery_type',
    container: '.field-order-delivery_type',
    input: '#ordercreateform-delivery_type',
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



$('#cartForm').yiiActiveForm('add', {
    id: 'ordercreateform-delivery_warehouse_ref',
    name: 'delivery_warehouse_ref',
    container: '.field-order-delivery_warehouse_ref',
    input: '#ordercreateform-delivery_warehouse_ref',
    error: '.invalid-feedback',
    validate:  function (attribute, value, messages, deferred, form) {
        console.log(yii.validation);
        yii.validation.required(value, messages, {message: 'errr'});
        console.log('validate',attribute,value);
        return false;
        if(value){
            $(attribute.container).removeClass('field-is-invalid');
        }else{
            $(attribute.container).addClass('field-is-invalid');
        }
    }
});
//$('#ordercreateform-delivery_city_ref').select2("destroy").select2();

$('#ordercreateform-delivery_city_ref').on('change.select2',function(e){
    cart.delivery({$model->delivery_id});
    console.log('de',e,$(this).val());
});
$( "#cartForm" ).trigger( "cart:delivery",function(e) {
  console.log('dsa',e);
});

$('#ordercreateform-delivery_type').on('change.select2',function(e){

    console.log('delivery_type',e,$(this).val());
});



JS;
$this->registerJs($js2, \yii\web\View::POS_END, 'rrrr');


?>

<div class="form-group row field-order-delivery_type">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
        <?= Html::activeLabel($model, 'delivery_type', ['class' => 'col-form-label']); ?>
    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
        <?php
        if($model->delivery_type == 'warehouse' &&$model->delivery_warehouse_ref){
            $model->delivery_type = 'warehouse';
        }

        echo Html::activeDropDownList($model,'delivery_type',['address' => 'Доставка на адрес','warehouse' => 'Доставка на отделение'])
        /*echo \panix\ext\select2\Select2::widget([
            'model' => $model,
            'hideSearch'=>true,
            'attribute' => 'delivery_type',
            'items' => ['address' => 'Доставка на адрес','warehouse' => 'Доставка на отделение'],
            'clientOptions' => [

            ],
            'options' => [
                'class' => ''
            ]
        ]);*/
        ?>
    </div>
</div>

<?php if ($model->delivery_type == 'warehouse') { ?>
<div class="form-group row field-order-delivery_city_ref">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
        <?= Html::activeLabel($model, 'delivery_city_ref', ['class' => 'col-form-label']); ?>
    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
        <?php
        echo Html::activeDropDownList($model,'delivery_city_ref',\yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Cities::find()->where(['IsBranch'=>1])->orderBy(['Description' => SORT_ASC])->all(), 'Ref', function ($model) {
            return $model->getDescription();
        }))
        /*echo \panix\ext\select2\Select2::widget([
            'model' => $model,
            'attribute' => 'delivery_city_ref',
            'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Cities::find()->where(['IsBranch'=>1])->orderBy(['Description' => SORT_ASC])->all(), 'Ref', function ($model) {
                return $model->getDescription();
            }),

            'options' => [
                'class' => ''
            ]
        ]);*/
        ?>

    </div>
</div>
<?php } ?>
<?php


if ($model->delivery_city_ref && $model->delivery_type == 'warehouse') { ?>




<div class="form-group row field-order-delivery_warehouse_ref">
    <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
        <?= Html::activeLabel($model, 'delivery_warehouse_ref', ['class' => 'col-form-label']); ?>
    </div>
    <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
        <?php

        echo \panix\ext\select2\Select2::widget([
            'model' => $model,
            'attribute' => 'delivery_warehouse_ref',
            //'items' => \panix\mod\novaposhta\models\Warehouses::getList($model->delivery_city_ref),
            'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Warehouses::find()->cache(8600*7)->where(['CityRef' => $model->delivery_city_ref,'CategoryOfWarehouse'=>'Branch'])->orderBy(['number' => SORT_ASC])->all(), 'Ref', function ($model) {
                return $model->getDescription();
            }),
            /*'jsOptions' => [
                'liveSearch' => true,
                'width' => '100%',
                'liveSearchPlaceholder' => 'Найти отделение',
                'dropdownAlignRight' => 'auto',
                'size' => '300px',

            ],*/
            'options' => [
                'class' => '',
                'prompt'=>'111111111'
            ]
        ]);
        ?>

    </div>
</div>
<?php } ?>


