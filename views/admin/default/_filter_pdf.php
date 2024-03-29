<?php

use panix\engine\CMS;
use panix\engine\Html;
use panix\engine\jui\DatePicker;
use panix\mod\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;

/**
 * @var $this \yii\web\View
 */

$this->registerJs("
$('#filter-collapse').on('show.bs.collapse', function () {
  $('#filter-collapse-icon').removeClass('icon-arrow-down').addClass('icon-arrow-up');
});

$('#filter-collapse').on('hide.bs.collapse', function () {
  $('#filter-collapse-icon').removeClass('icon-arrow-up').addClass('icon-arrow-down');
});

")
?>
<div class="card" id="card-filter-collapse">
    <div class="card-header">
        <h5>
            <a class="" data-toggle="collapse" href="#filter-collapse" role="button" aria-expanded="false"
               aria-controls="filter-collapse">
                <i class="icon-arrow-down" id="filter-collapse-icon"></i> Печать
            </a>
        </h5>
    </div>
    <div class="collapse" id="filter-collapse">
        <div class="card-body">

            <div class="pl-3 pr-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">с</span>
                        </div>
                        <?php
                        echo DatePicker::widget([
                            'name' => 'start',
                            'value' => (Yii::$app->request->get('start')) ? Yii::$app->request->get('start') : date('Y-m-d'),
                            //'language' => 'ru',
                            'dateFormat' => 'yyyy-MM-dd',
                        ]);
                        ?>
                        <div class="input-group-prepend">
                            <span class="input-group-text">по</span>
                        </div>
                        <?php
                        echo DatePicker::widget([
                            'name' => 'end',
                            'value' => (Yii::$app->request->get('end')) ? Yii::$app->request->get('end') : date('Y-m-d'),
                            //'language' => 'ru',
                            'dateFormat' => 'yyyy-MM-dd',
                        ]);
                        ?>
                        <?php

                        echo Html::dropDownList('render', 'delivery', ['delivery' => 'Распределить по доставке', 'brand' => 'Распределить по бренду', 'supplier' => 'Распределить по поставщику'], ['class' => 'custom-select']);
                        ?>
                        <?php
                        echo Html::dropDownList('type', 1, [1 => 'PDF', 0 => 'Html'], ['class' => 'custom-select']);

                        ?>

                    </div>

                    <div class="row">
                        <div class="col-3">
                            <?php
                            echo Html::label(Yii::t('cart/Order', 'STATUS_ID'), 'status_id', ['class' => 'font-weight-bold']);
                            echo Html::checkboxList('status_id', NULL, ArrayHelper::map(OrderStatus::find()
                                ->addOrderBy(['name' => SORT_ASC])
                                ->all(), 'id', 'name'), ['class' => '', 'item' => function ($index, $label, $name, $checked, $value) {
                                $checked = $checked ? 'checked' : '';
                                return "<div class='custom-control custom-checkbox'><input id='status-id-{$index}' class='custom-control-input' type='checkbox' {$checked} name='{$name}' value='{$value}'><label class='custom-control-label' for='status-id-{$index}'>{$label}</label></div>";
                            }]);

                            ?>
                        </div>

                    </div>


                </div>
            </div>
            <div class="card-footer text-center">
                <div class="row">
                    <div class="col text-right">
                        <div class="custom-control custom-checkbox">
                            <?php
                            echo Html::checkbox('price', true, ['id' => 'type_price', 'class' => 'custom-control-input', 'checked' => 1]);
                            echo Html::label('Цена закупки (только для поставщиков)', 'type_price', ['class' => 'font-weight-b2old custom-control-label']);
                            ?>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <?php
                            echo Html::checkbox('image', true, ['id' => 'image', 'class' => 'custom-control-input', 'checked' => 1]);
                            echo Html::label('Картинки', 'image', ['class' => 'font-weight-b2old custom-control-label']);
                            ?>
                        </div>
                    </div>
                    <div class="col text-left">
                        <?= Html::submitButton('Показать', ['class' => 'btn btn-success', 'name' => '']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


