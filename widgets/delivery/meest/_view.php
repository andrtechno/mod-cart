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
            $cities = $api->getGeoDistricts(['region_id' => $model->area]);

            echo Html::activeLabel($model, 'city', ['class' => 'col-form-label']);
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'city',
                'items' => \yii\helpers\ArrayHelper::map($cities, 'ua', function ($model) {
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
                $opts['viewdata'] = 'full';

                if (isset($settings->type_warehouse) && !empty($settings->type_warehouse)) {
                    $opts['type'] = implode(',', $settings->type_warehouse);
                }

                $value2 = substr($model->city, 0, -strlen($model->city) + 8);

                $opts['city'] = $value2;
                $branches = $api->getBranches($opts);

                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'warehouse',
                    'items' => \yii\helpers\ArrayHelper::map($branches, 'br_id', function ($model) {
                        $value = '№' . $model['num'] . ' ' . $model['type_public']['ua'] . ' ' . $model['street']['ua'] . ' ' . $model['street_number'];
                        if ($model['limits']['parcel_max_kg']) {
                            $value .= ' (до ' . floor($model['limits']['parcel_max_kg']) . 'кг)';
                        }
                        return $value;
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

