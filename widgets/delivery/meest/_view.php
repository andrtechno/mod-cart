<?php

use panix\engine\Html;
use panix\ext\select2\Select2;


/**
 * @var \yii\web\View $this
 */

?>
    <div class="mb-4">
        <div class="form-group field-delivery-area required <?php if ($model->getErrors('area')) echo "has-error" ?>">
            <?= Html::activeLabel($model, 'area', ['class' => 'col-form-label']); ?>

            <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'area',
                'items' => \yii\helpers\ArrayHelper::map($areas, 'region_id', function ($model) {
                    return (Yii::$app->language == 'ru') ? $model['ru'] : $model['ua'];
                }),
                'options' => [
                    'prompt' => html_entity_decode('&mdash; ' . Yii::t('cart/Delivery', 'PROMPT_AREA') . ' &mdash;'),
                    'class' => ($model->getErrors('area')) ? 'is-invalid' : ''
                ],
                'clientOptions' => [
                    'width' => '100%',
                    'placeholder' => [
                        'id' => '-1',
                        'text' => '1111'
                    ],
                    //'initSelection' => new \yii\web\JsExpression('function(element, callback) {}')
                ],
            ]);
            echo Html::error($model, 'area', ['class' => 'invalid-feedback d-block']);
            ?>
        </div>
        <?php if ($model->area) { ?>
            <div class="form-group field-delivery-city required <?php if ($model->getErrors('city')) echo "has-error" ?>">
                <?php
                $test = $api->getGeoDistricts(['region_id' => $model->area]);

                echo Html::activeLabel($model, 'city', ['class' => 'col-form-label']);
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'city',
                    'items' => \yii\helpers\ArrayHelper::map($test, 'ua', function ($model) {
                        return $model['ua'];
                    }),
                    'options' => [
                        'prompt' => html_entity_decode('&mdash; ' . Yii::t('cart/Delivery', 'PROMPT_CITY') . ' &mdash;'),
                        'class' => ($model->getErrors('city')) ? 'is-invalid' : ''
                    ],
                    'clientOptions' => [
                        'width' => '100%',
                        'placeholder' => [
                            'id' => '-1',
                            'text' => ''
                        ],
                        //'initSelection' => new \yii\web\JsExpression('function(element, callback) {}')
                    ],
                ]);
                echo Html::error($model, 'city', ['class' => 'invalid-feedback d-block']);
                ?>
            </div>
        <?php } ?>
        <?php if ($model->area && $model->city) { ?>
            <div class="form-group field-delivery-type required">

                <?php echo Html::activeLabel($model, 'type', ['class' => 'col-form-label']); ?>

                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'type',
                    'items' => $model->typesList,
                    'clientOptions' => [
                        'width' => '100%'
                    ],
                ]);
                //echo Html::activeDropDownList($model,'delivery_type',['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'])

                ?>
            </div>

            <?php

            if ($model->type == 'warehouse') { ?>
                <div class="form-group field-order-warehouse required <?php if ($model->getErrors('warehouse')) echo "has-error" ?>">
                    <?= Html::activeLabel($model, 'warehouse', ['class' => 'col-form-label']); ?>
                    <?php

                    $value2= substr($model->city, 0, -strlen($model->city)+8);
                    $test2 = $api->getBranches(['city' => $value2, 'viewdata' => 'full', 'type2' => 'cargobranch,minibranch,poshtomat,mainbranch']);

                    echo Select2::widget([
                        'model' => $model,
                        'attribute' => 'warehouse',
                        'items' => \yii\helpers\ArrayHelper::map($test2, 'br_id', function ($model) {
                            return '№' . $model['num'] . ' ' . $model['type_public']['ua'] . ' ' . $model['street']['ua'] . ' ' . $model['street_number'] . ' (до ' . $model['limits']['place_max_kg'] . 'кг)';
                        }),
                        'options' => [
                            'prompt' => html_entity_decode('&mdash; ' . Yii::t('cart/Delivery', 'PROMPT_WAREHOUSE') . ' &mdash;'),
                            'class' => ($model->getErrors('warehouse')) ? 'is-invalid' : ''
                        ],
                        'clientOptions' => [
                            'width' => '100%',
                            'placeholder' => [
                                'id' => '-1',
                                'text' => '1111'
                            ],
                        ],
                    ]);
                    echo Html::error($model, 'warehouse', ['class' => 'invalid-feedback d-block']);
                    ?>
                </div>
            <?php } else { ?>

                <div class="form-group field-delivery-address required <?php if ($model->getErrors('address')) echo "has-error" ?>">
                    <?= Html::activeLabel($model, 'address', ['class' => 'col-form-label']); ?>
                    <?= Html::activeTextInput($model, 'address', ['class' => 'form-control ' . (($model->getErrors('address')) ? 'is-invalid' : '')]); ?>
                    <?= Html::error($model, 'address'); ?>
                    <?php //echo $form->field($model, 'delivery_address')->textInput(['maxlength' => 255])
                    ?>
                </div>
            <?php } ?>
        <?php } ?>


    </div>
<?php
//if(!Yii::$app->request->isAjax){
//$this->registerJs("var delivery_id = " . $delivery_id . ";", \yii\web\View::POS_END, 'delivery-novaposhta');
/*
$this->registerJs("
    $(document).on('change', '#dynamicmodel-city, #dynamicmodel-type, #dynamicmodel-area', function(e) {
        $.ajax({
            url: common.url('/cart/delivery/process?id=" . $delivery_id . "'),
            type: 'POST',
            data: $('#cartForm').serialize(),
            dataType: 'html',
            beforeSend: function(){
                $('#order-delivery_id').addClass('loading');
            },
            complete: function(){
                $('#order-delivery_id').removeClass('loading');
            },
            error: function(){
                $('#order-delivery_id').removeClass('loading');
            },
            success: function (data) {
                $('.delivery-form-" . $delivery_id . "').html(data);
                $('#order-delivery_id').removeClass('loading');
            }
        });
    });
    $(document).on('change', '#dynamicmodel-type', function(e) {
        $('#delivery-1').html($('option:selected',this).text());
    });
    $(document).on('change', '#dynamicmodel-city', function(e) {
        $('#delivery-2').html($('option:selected',this).text());
    });
    $(document).on('change', '#dynamicmodel-warehouse', function(e) {
        $('#delivery-3').html($('option:selected',this).text());
    });

    if($('#dynamicmodel-area option:selected').val()){
        $('#delivery-1').html($('#dynamicmodel-area option:selected').text());
    }

    if($('#dynamicmodel-city option:selected').val()){
        $('#delivery-2').html($('#dynamicmodel-city option:selected').text());
    }

    if($('#dynamicmodel-warehouse option:selected').val()){
        $('#delivery-3').html($('#dynamicmodel-warehouse option:selected').text());
    }

    var deliveryCheck = $('#order-delivery_id input[type=\"radio\"]:checked');
    $('#delivery').html($(\"label[for='\"+deliveryCheck.attr('id')+\"']\").text());

", \yii\web\View::POS_END, 'delivery-novaposhta');*/
//}
