<?php
use panix\engine\Html;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */
$model = new OrderCreateForm();
echo BootstrapSelect::widget([
    'model' => $model,
    'attribute' => 'delivery_city',
    'items' => $cities,
    'jsOptions' => [
        'liveSearch' => true
    ]
]);

$this->registerJs("
    $('#ordercreateform-delivery_city').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        $.ajax({
            url: common.url('/cart/delivery/process?id=2'),
            type: 'POST',
            data: {city: $(this).selectpicker('val')},
            dataType: 'html',
            success: function (data) {
                $('#test').html(data);
            }
        });
    });
");

if (Yii::$app->request->post('city')) {

    echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_type',
        'items' => ['warehouse' => 'Доставка на отделение', 'address' => 'Доставка на адрес'],
        'jsOptions' => [
            'liveSearch' => true
        ]
    ]);


    echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_address',
        'items' => $address,
        'jsOptions' => [
            'liveSearch' => true
        ],
        'options' => [
            'class' => 'address-input'
        ]
    ]);

    echo Html::activeTextInput($model, 'delivery_address', ['class' => 'warehouse-input']);


}
