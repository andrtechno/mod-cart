<?php
use panix\engine\Html;
use panix\ext\bootstrapselect\BootstrapSelect;
use panix\mod\cart\models\forms\OrderCreateForm;

/**
 * @var \yii\web\View $this
 */

$this->registerCss('.bootstrap-select .dropdown-menu{max-height:300px;}');
$model = new OrderCreateForm();
$id = ($method->system)?$method->system:$method->id;
$delivery_city_selector =  Html::getInputId($model,'user_city');
$delivery_type_selector =  Html::getInputId($model,'delivery_type');
$address_selector =  Html::getInputId($model,'delivery_address');
if (!Yii::$app->request->post('city')) {
    ?>
    <div class="form-group field-ordercreateform-delivery_city required">
        <?php


        echo BootstrapSelect::widget([
            'model' => $model,
            'attribute' => 'user_city',
            'items' => $cities,
            'jsOptions' => [
                'width' => '100%',
                'liveSearch' => true,
            ]
        ]);


       // echo Html::activeLabel($model, 'user_city',['class'=>'control-label']);
      //  echo Html::activeDropDownList($model,'user_city',$cities,['class'=>'form-control','prompt'=>'___']);
      //  echo Html::error($model,'user_city');
        ?>
    </div>
    <?php



    echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_type',
        'items' => ['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'],
        'jsOptions' => [
            //'liveSearch' => true,
            'width' => '100%',

        ]
    ]);


    //echo Html::activeLabel($model, 'delivery_type',['class'=>'control-label']);
   // echo Html::activeDropDownList($model,'delivery_type',['address' => 'Доставка на адрес', 'warehouse' => 'Доставка на отделение'],['class'=>'form-control','prompt'=>'___']);
    //echo Html::error($model,'delivery_type');
    /*echo BootstrapSelect::widget([
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
    //print_r($address);die;
   // Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
   // echo json_encode($address);Yii::$app->end();
    $ss = json_encode($address);
    $this->registerJs("var addressList = {$ss};");


    echo BootstrapSelect::widget([
        'model' => $model,
        'attribute' => 'delivery_address',
        'items' => $address,
        'jsOptions' => [
            'liveSearch' => true,
            'width' => '100%',

        ]
    ]);

  //  echo Html::activeDropDownList($model,'delivery_address',$address);
}
$this->registerJs("



$('#cartForm').yiiActiveForm('add', {
    id: 'ordercreateform-user_city',
    name: 'delivery_city',
    container: '#field-ordercreateform-user_city',
    input: '#ordercreateform-user_city',
    error: '.help-block',
    validate:  function (attribute, value, messages, deferred) {
        yii.validation.required(value, messages, {message: \"Validation Message Here\"});
    }
});

//$('#contact-form').yiiActiveForm('remove', 'address');


//$('#user-address-input').addClass('d-none');
$('#warehouse-input').addClass('d-none');
        /*        
    $('#{$delivery_city_selector}').on('change', function(e, clickedIndex, isSelected, previousValue) {
        $.ajax({
            url: common.url('/cart/delivery/process?id={$id}'),
            type: 'POST',
            data: {city: $(this).val()},
            dataType: 'json',
            success: function (data) {
                $('#delivery-data').html(data);
                $('#user-address-input').removeClass('d-none');
                $('#warehouse-input').addClass('d-none');

            }
        });
    });
    */
    
    $(document).on('change', '#{$delivery_type_selector}', function(e, clickedIndex, isSelected, previousValue) {
    
    
        if($(this).val() == 'warehouse'){
            $('#warehouse-input').removeClass('d-none');
            $('#{$address_selector}').replaceWith('<select id=\"{$address_selector}\" name=\"OrderCreateForm[delivery_address]\" class=\"form-control\">' +
                '<option value=\"1\">1</option>' +
                '<option value=\"2\">2</option>' +
                '<option value=\"3\">3</option>' +
                '<option value=\"4\">4</option>' +
                '<option value=\"5\">5</option>' +
            '</select>');

            $('#{$address_selector}').selectpicker('refresh');

        }else{
            $('#warehouse-input').addClass('d-none');
           // $('#user-address-input').removeClass('d-none');
           
            $('#{$address_selector}').selectpicker('destroy');
            $('#{$address_selector}').replaceWith('<input id=\"{$address_selector}\" name=\"OrderCreateForm[delivery_address]\" class=\"form-control\" />');

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
            echo Html::activeLabel($model, 'delivery_address');
            echo BootstrapSelect::widget([
                'model' => $model,
                'attribute' => 'delivery_address',
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
