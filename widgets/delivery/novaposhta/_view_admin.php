<?php

use panix\engine\Html;
use panix\engine\CMS;
use panix\ext\select2\Select2;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var \yii\web\View $this
 */

?>

    <div class="form-group row field-delivery-area required">
        <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
            <?= Html::activeLabel($model, 'area', ['class' => 'col-form-label']); ?>
        </div>
        <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
            <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'area',
                'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Area::find()->cache(86000 * 30)
                    ->orderBy(['Description' => SORT_ASC])
                    ->all(), 'Ref', function ($model) {
                    return $model->getDescription();
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
    </div>

<?php if ($model->area) { ?>
    <div class="form-group row field-delivery-city required">
        <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
            <?= Html::activeLabel($model, 'city', ['class' => 'col-form-label']); ?>
        </div>
        <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
            <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'city',
                'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Cities::find()->cache(8600 * 7)
                    ->orderBy(['Description' => SORT_ASC])
                    ->where(['warehouse' => 1])
                    ->andWhere(['Area' => $model->area])
                    ->all(), 'Ref', function ($model) {
                    return $model->getDescription();
                }),
                'options' => [
                    'prompt' => html_entity_decode('&mdash; ' . Yii::t('cart/Delivery', 'PROMPT_CITY') . ' &mdash;'),
                    'class' => ($model->getErrors('city')) ? 'is-invalid' : ''
                ],
                'clientOptions' => [
                    'width' => '100%',
                    'placeholder' => [
                        'id' => '-1',
                        'text' => '1111'
                    ],
                ],
            ]);
            echo Html::error($model, 'city', ['class' => 'invalid-feedback d-block']);
            ?>
        </div>
    </div>
<?php } ?>
<?php if ($model->area && $model->city) { ?>
    <div class="form-group row field-delivery-type required">
        <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
            <?php echo Html::activeLabel($model, 'type', ['class' => 'col-form-label']); ?>
        </div>
        <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
            <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'type',
                'items' => $model->typesList,
                'options' => [
                    'class' => ($model->getErrors('type')) ? 'is-invalid' : ''
                ],
                'clientOptions' => [
                    'width' => '100%'
                ],
            ]);
            echo Html::error($model, 'type', ['class' => 'invalid-feedback d-block']);
            ?>
        </div>
    </div>
    <?php if ($model->type == 'warehouse') { ?>
        <div class="form-group row field-order-warehouse required">
            <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
                <?= Html::activeLabel($model, 'warehouse', ['class' => 'col-form-label']); ?>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'warehouse',
                    'items' => \yii\helpers\ArrayHelper::map(\panix\mod\novaposhta\models\Warehouses::find()->cache(8600 * 7)->where(['CityRef' => $model->city])->orderBy(['number' => SORT_ASC])->all(), 'Ref', function ($model) {
                        return $model->getDescription();
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
        </div>
    <?php } else { ?>
        <div class="form-group row field-delivery-address required <?php if ($model->getErrors('address')) echo "has-error" ?>">
            <div class="col-sm-4 col-md-4 col-lg-3 col-xl-4">
                <?= Html::activeLabel($model, 'address', ['class' => 'col-form-label']); ?>
            </div>
            <div class="col-sm-8 col-md-8 col-lg-9 col-xl-8">
                <?= Html::activeTextInput($model, 'address', ['class' => 'form-control ' . (($model->getErrors('address')) ? 'is-invalid' : '')]); ?>
                <?= Html::error($model, 'address'); ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php


$this->registerJs("
    $(document).on('change', '#dynamicmodel-city, #dynamicmodel-type, #dynamicmodel-area', function(e, clickedIndex, isSelected, previousValue) {
        $.ajax({
            url: common.url('/admin/cart/delivery/process?id=" . $delivery_id . "'),
            type: 'POST',
            data: $('#order-form').serialize(),
            dataType: 'html',
            success: function (data) {
                $('#delivery-form').html(data);
                $('#delivery-form').removeClass('pjax-loader');
            },
            beforeSend:function(){
                $('#delivery-form').addClass('pjax-loader');
            }
        });
    });

",\yii\web\View::POS_END,'np');