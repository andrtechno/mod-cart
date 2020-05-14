<?php
use panix\engine\Html;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */

$this->registerCss('
.bootstrap-select .dropdown-menu{max-height:300px;}
');
$model = new OrderCreateForm();
if (!Yii::$app->request->post('city')) {
    echo Html::activeLabel($model, 'delivery_city');
    echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_city',
        'items' => $cities,
        'jsOptions' => [
            'liveSearch' => true,
            'width' => '100%',

        ]
    ]);
}
$this->registerJs("

$('#user-address-input').addClass('d-none');
$('#warehouse-input').addClass('d-none');
                
    $('#ordercreateform-delivery_city').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
        $.ajax({
            url: common.url('/cart/delivery/process?id={$method->id}'),
            type: 'POST',
            data: {city: $(this).val()},
            dataType: 'html',
            success: function (data) {
                $('#delivery-data').html(data);
                $('#user-address-input').removeClass('d-none');
                $('#warehouse-input').addClass('d-none');
            }
        });
    });
    
    $('#ordercreateform-delivery_type').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
        if($(this).val() == 'warehouse'){
            $('#warehouse-input').removeClass('d-none');
            $('#user-address-input').addClass('d-none');
        }else{
            $('#warehouse-input').addClass('d-none');
            $('#user-address-input').removeClass('d-none');
        }
     });
");
?>
<div id="delivery-data">
    <?php
    if (Yii::$app->request->post('city')) {

        echo Html::activeLabel($model, 'delivery_type');
        echo BootstrapSelect::widget([
            'model' => $model,
            'attribute' => 'delivery_type',
            'items' => ['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'],
            'jsOptions' => [
                'width' => '100%',

            ]
        ]);
        $address = \panix\mod\novaposhta\models\Warehouses::getList(Yii::$app->request->post('city'));

        ?>
        <div id="warehouse-input" class="d-none">
            <?php
            echo Html::activeLabel($model, 'delivery_warehouse');
            echo BootstrapSelect::widget([
                'model' => $model,
                'attribute' => 'delivery_warehouse',
                'items' => $address,
                'jsOptions' => [
                    'liveSearch' => true,
                    'width' => '100%',

                ],
                'options' => [
                    'class' => ''
                ]
            ]);
            ?>
        </div>
        <?php
        // echo Html::activeTextInput($model, 'delivery_warehouse', ['class' => 'form-control address-input d-none','placeholder'=>'Адрес']);

    }
    ?>
</div>
