<?php
use panix\engine\Html;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */

$this->registerCss('.bootstrap-select .dropdown-menu{max-height:300px;}');
$model = new OrderCreateForm();


if (!Yii::$app->request->post('city')) {
    ?>
    <div class="form-group field-ordercreateform-delivery_city required">
        <?php
        echo Html::activeLabel($model, 'delivery_city',['class'=>'control-label']);
        echo Html::activeDropDownList($model,'delivery_city',$cities,['class'=>'form-control','prompt'=>'___']);
        echo Html::error($model,'delivery_city');
        ?>
    </div>
    <?php
    echo Html::activeLabel($model, 'delivery_type',['class'=>'control-label']);
    echo Html::activeDropDownList($model,'delivery_type',['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'],['class'=>'form-control','prompt'=>'___']);
    echo Html::error($model,'delivery_type');
   /* echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_city',
        'items' => $cities,
        'jsOptions' => [
            'liveSearch' => true,
            'width' => '100%',

        ]
    ]);*/



   /* echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_type',
        'items' => ['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'],
        'jsOptions' => [
            'width' => '100%',

        ]
    ]);*/
}else{
    $address = \panix\mod\novaposhta\models\Warehouses::getList(Yii::$app->request->post('city'));
    $ss = json_encode($address);
    $this->registerJs("var addressList = {$ss};");



    echo Html::activeDropDownList($model,'user_address',$address);
}
$this->registerJs("


$('#cartForm').yiiActiveForm('add', {
    id: 'ordercreateform-delivery_city',
    name: 'delivery_city',
    container: '#field-ordercreateform-delivery_city',
    input: '#ordercreateform-delivery_city',
    error: '.help-block',
    validate:  function (attribute, value, messages, deferred) {
        yii.validation.required(value, messages, {message: \"Validation Message Here\"});
    }
});

//$('#contact-form').yiiActiveForm('remove', 'address');


//$('#user-address-input').addClass('d-none');
$('#warehouse-input').addClass('d-none');
                
    $('#ordercreateform-delivery_city').on('change', function(e, clickedIndex, isSelected, previousValue) {
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
    
    $('#ordercreateform-delivery_type').on('change', function(e, clickedIndex, isSelected, previousValue) {
        if($(this).val() == 'warehouse'){
            $('#warehouse-input').removeClass('d-none');
            //$('#user-address-input').addClass('d-none');

            console.log(addressList);
            /*$('#ordercreateform-user_address').replaceWith('<select id=\"ordercreateform-user_address\" name=\"OrderCreateForm[user_address]\" class=\"form-control\">' +
                '<option value=\"1\">1</option>' +
                '<option value=\"2\">2</option>' +
                '<option value=\"3\">3</option>' +
                '<option value=\"4\">4</option>' +
                '<option value=\"5\">5</option>' +
            '</select>');

            $('#ordercreateform-user_address').selectpicker('refresh');*/

        }else{
            $('#warehouse-input').addClass('d-none');
           // $('#user-address-input').removeClass('d-none');
           

           // $('#user-address-input').replaceWith('<input id=\"ordercreateform-user_address\" name=\"OrderCreateForm[user_address]\" class=\"form-control\" />');
            //$('#ordercreateform-user_address').selectpicker('destroy');
        }
     });
");
?>
<div id="delivery-data">
    <?php
    if (Yii::$app->request->post('city')) {



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
