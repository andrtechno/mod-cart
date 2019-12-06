<?php
use panix\engine\Html;
use panix\engine\widgets\Pjax;

?>


<h1><?= $this->context->pageName; ?></h1>


<?php
Pjax::begin();
echo \panix\engine\grid\GridView::widget([
    //'id'=>'list-product',
    'dataProvider' => $dataProvider,
    // 'filterModel' => $searchModel,
    'layout' => '{items}{pager}',
    //'emptyText' => 'Empty',
    // 'options' => ['class' => 'list-view'],
    'tableOptions' => ['class' => 'table table-striped'],
    'sorter' => [
        //'class' => \yii\widgets\LinkSorter::class,
        'attributes' => ['price', 'sku']
    ],
    'emptyTextOptions' => ['class' => 'alert alert-info'],
    'columns' => [
        [
            'header' => Yii::t('cart/Order', 'ID'),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-center'],
            'attribute' => 'id'
        ],
        [
            'header' => Yii::t('cart/Order', 'PAID'),
            'attribute' => 'paid',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'html',
            'value' => function ($model) {
                return Html::tag('span', Yii::$app->formatter->asBoolean($model->paid), ['class' => 'badge badge-' . ($model->paid ? 'success' : 'secondary')]);
            }
        ],
        [
            'header' => Yii::t('cart/Order', 'STATUS_ID'),
            'attribute' => 'status_id',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->status->name;
            }
        ],
        [
            'header' => Yii::t('cart/Order', 'Доставка'),
            'attribute' => 'delivery_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return $model->deliveryMethod->name . '<br/>' . Yii::t('cart/OrderCreateForm', 'USER_ADDRESS') . ': ' . $model->user_address;
            }
        ],
        [
            'header' => Yii::t('cart/Order', 'Оплата'),
            'attribute' => 'payment_id',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->paymentMethod->name;
            }
        ],
        [
            'header' => Yii::t('cart/Order', 'USER_PHONE'),
            'attribute' => 'user_phone',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'html',
            'value' => function ($model) {
                return Html::tel($model->user_phone);
            }
        ],
        [
            'header' => Yii::t('cart/Order', 'FULL_PRICE'),
            'contentOptions' => ['class' => 'text-center'],
            'headerOptions' => ['class' => 'text-center'],
            'attribute' => 'full_price',
            'format' => 'html',
            'value' => function ($model) {
                $priceHtml = Yii::$app->currency->number_format($model->full_price);
                $symbol = Html::tag('sup', Yii::$app->currency->main['symbol']);
                return Html::tag('span', $priceHtml, ['class' => 'text-success font-weight-bold']) . ' ' . $symbol;
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',  // the default buttons + your custom button
            'buttons' => [
                'view' => function ($url, $model, $key) {     // render your custom button
                    return Html::a(Html::icon('eye'), $model->getUrl(), ['class' => 'btn btn-sm btn-secondary']);
                }
            ]
        ]
    ]
]);
Pjax::end();
?>

