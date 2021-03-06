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


$this->registerCss('.bootstrap-select .inner{max-height: 300px;}');
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
        echo BootstrapSelect::widget([
            'model' => $model,
            'attribute' => 'delivery_type',
            'items' => ['address' => 'Доставка на адрес','warehouse' => 'Доставка на отделение'],
            'jsOptions' => [

            ],
            'options' => [
                'class' => ''
            ]
        ]);
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

        echo BootstrapSelect::widget([
            'model' => $model,
            'attribute' => 'delivery_city_ref',
            'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Cities::find()->cache(8600*7)->orderBy(['Description' => SORT_ASC])->all(), 'Ref', function ($model) {
                return $model->getDescription();
            }),
            'jsOptions' => [
                'liveSearch' => true,
                'width' => '100%',
                'liveSearchPlaceholder' => 'Найти город',
                'dropdownAlignRight' => 'auto',
                'size' => '300px',
            ],
            'options' => [
                'class' => ''
            ]
        ]);
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

        echo BootstrapSelect::widget([
            'model' => $model,
            'attribute' => 'delivery_warehouse_ref',
            //'items' => \panix\mod\novaposhta\models\Warehouses::getList($model->delivery_city_ref),
            'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Warehouses::find()->cache(8600*7)->where(['CityRef' => $model->delivery_city_ref])->orderBy(['number' => SORT_ASC])->all(), 'Ref', function ($model) {
                return $model->getDescription();
            }),
            'jsOptions' => [
                'liveSearch' => true,
                'width' => '100%',
                'liveSearchPlaceholder' => 'Найти отделение',
                'dropdownAlignRight' => 'auto',
                'size' => '300px',

            ],
            'options' => [
                'class' => ''
            ]
        ]);
        ?>

    </div>
</div>
<?php } ?>


