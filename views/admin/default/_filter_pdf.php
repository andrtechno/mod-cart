<?php

use panix\engine\CMS;
use panix\engine\Html;
use panix\engine\jui\DatePicker;
use panix\mod\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;

?>
<div class="card">
    <div class="card-header">
        <h5>
            <a class="" data-toggle="collapse" href="#filter-collapse" role="button" aria-expanded="false"
               aria-controls="collapseExample">
                <i class="icon-menu"></i> Фильтры
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

                        echo Html::dropDownList('render', 'delivery', ['delivery' => 'Распределить по доставке', 'manufacturer' => 'Распределить по производителю', 'supplier' => 'Распределить по поставщику'], ['class' => 'custom-select']);
                        ?>
                        <?php
                        echo Html::dropDownList('type', 1, [1 => 'PDF', 0 => 'Html'], ['class' => 'custom-select']);

                        ?>





                    </div>


                    <?php
                    echo Html::label(Yii::t('cart/Order','STATUS_ID'),'status_id',['class'=>'font-weight-bold']);
                    echo Html::checkboxList('status_id', NULL, ArrayHelper::map(OrderStatus::find()
                        ->addOrderBy(['name' => SORT_ASC])
                        ->all(), 'id', 'name'), ['class' => '', 'item' => function ($index, $label, $name, $checked, $value) {
                        $checked = $checked ? 'checked' : '';
                        return "<div class='custom-control custom-checkbox'><input id='status-id-{$index}' class='custom-control-input' type='checkbox' {$checked} name='{$name}' value='{$value}'><label class='custom-control-label' for='status-id-{$index}'>{$label}</label></div>";
                    }]);

                    ?>
                </div>
            </div>
            <div class="card-footer text-center">
                <?= Html::checkBox('image', true, ['class' => 'form-control2']); ?>
                <?= Html::label('Картинки', 'image', ['class' => 'control-label']); ?>
                <?= Html::submitButton('Показать', ['class' => 'btn btn-success', 'name' => '']); ?>
            </div>
        </div>
    </div>
</div>


