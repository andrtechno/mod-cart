<?php
use panix\engine\Html;
use panix\engine\widgets\Pjax;
use panix\mod\novaposhta\models\Area;
use panix\mod\novaposhta\models\Cities;
use panix\mod\novaposhta\models\Warehouses;
use yii\helpers\Json;

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
            'header' => Yii::t('cart/default', 'DELIVERY'),
            'attribute' => 'delivery_id',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                if ($model->deliveryMethod) {
                    if ($model->deliveryMethod->system) {
                        $manager = new DeliverySystemManager();
                        $system = $manager->getSystemClass($model->deliveryMethod->system);
                        //$model->deliveryModel = $system->getModel();
                    }
                    $data = Json::decode($model->delivery_data);
                    if ($model->deliveryMethod->system == 'novaposhta') {
                        $html = '';
                        if (isset($data['type'])) {
                            if ($data['type'] == 'warehouse') {
                                if (isset($data['area'])) {
                                    $area = Area::findOne($data['area']);
                                    if ($area) {
                                        $html .= $area->getDescription() . ', ';
                                    }
                                }
                                if (isset($data['city'])) {
                                    $city = Cities::findOne($data['city']);
                                    if ($city) {
                                        $html .= Yii::t('cart/Delivery', 'CITY') . ' ' . $city->getDescription() . '';
                                    }
                                }
                                if (isset($data['warehouse'])) {
                                    $warehouse = Warehouses::findOne($data['warehouse']);
                                    if ($warehouse) {
                                        $html .= '<br/>' . $warehouse->getDescription();
                                    }
                                }

                            } else {
                                $html .= $data['address'];
                            }
                        }
                        return '<span class="badge badge-light">' . $model->deliveryMethod->name . '</span><br/>' . $html;
                    } elseif ($model->deliveryMethod->system == 'address') {
                        if (isset($data['address'])) {
                            return '<span class="badge badge-light">' . $model->deliveryMethod->name . '</span><br/>' . $data['address'];
                        }
                    } elseif ($model->deliveryMethod->system == 'pickup') {
                        if (isset($data['address'])) {
                            $settings = $system->getSettings($model->deliveryMethod->id);
                            if (isset($settings->address[$data['address']]['name'])) {
                                return '<span class="badge badge-light">' . $model->deliveryMethod->name . '</span><br/>' . $settings->address[$data['address']]['name'];
                            }

                        }
                    }
                    //return $model->deliveryMethod->name;
                }
                //return $model->deliveryMethod->name . '<br/>' . Yii::t('cart/OrderCreateForm', 'DELIVERY_ADDRESS') . ': ' . $model->delivery_address;
            }
        ],
        [
            'header' => Yii::t('cart/default', 'PAYMENT'),
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

