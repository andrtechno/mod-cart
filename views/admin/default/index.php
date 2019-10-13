<?php

use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\cart\models\Order;

?>


<?php

Pjax::begin([
    'id' => 'pjax-grid-order',
]);
?>
<?=

GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'showFooter' => true,
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    //'rowOptions' => function ($model, $index, $widget, $grid) {
    //    return ['style' => 'background-color:' . $model->status->color . ';'];
    //},
    'layoutOptions' => ['title' => $this->context->pageName],
    'columns' => [
        [
            'attribute' => 'status.name',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                /** @var $model Order */
                return $model->getGridStatus() . ' ' . $model::t('NEW_ORDER_ID', ['id' => $model->getNumberId()]);
            }
        ],
        'user_name',
        /*[
            'attribute' => 'total_price',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'footer' => \panix\mod\cart\models\Order::getTotal($dataProvider->models, 'total_price'),
            'value' => function ($model) {
                return Yii::$app->currency->number_format($model->total_price) . ' ' . Yii::$app->currency->main['symbol'];
            }
        ],*/
        [
            'attribute' => 'total_price',
            'format' => 'html',
            'class' => 'panix\engine\grid\columns\jui\SliderColumn',
            'max' => (int)Order::find()->aggregateTotalPrice('MAX'),
            'min' => (int)Order::find()->aggregateTotalPrice('MIN'),
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                /** @var $model Order */
                $priceHtml = Yii::$app->currency->number_format(Yii::$app->currency->convert($model->total_price));
                $symbol = Html::tag('sup', Yii::$app->currency->main['symbol']);
                return Html::tag('span', $priceHtml, ['class' => 'text-success font-weight-bold']) . ' ' . $symbol;
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{print} {update} {switch}',
            'buttons' => [
                'print' => function ($url, $model, $key) {
                    return Html::a(Html::icon('print'), ['print', 'id' => $model->id], [
                        'title' => Yii::t('cart/admin', 'PRINT'),
                        'class' => 'btn btn-sm btn-info linkTarget',
                        'target' => '_blank'
                    ]);
                }
            ]
        ]
    ]
]);
?>
<?php Pjax::end(); ?>
