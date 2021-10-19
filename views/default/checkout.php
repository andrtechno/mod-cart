<?php

use panix\mod\cart\models\Delivery;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var $this \yii\web\View
 */
$this->registerCss('

.panel.complete{
background:green;
}
.panel .edit-step{
display:none;
}
.panel.complete .panel-body{
display:none;
}
.checkout__step.disabled .panel-body{
display:none;
}
.panel .panel-footer{
display:none;
}
.panel.complete .edit-step{
display:inline-block
}
.panel.complete .label{
text-indent:-99999px;
position:relative;
}
.panel.complete .label:before{
font-family:Pixelion;
content:"\f055";
}

');
$this->registerJs("

$(document).on('click','#button-contact',function(){
    console.log('click sumb');
    $('form#cartForm').submit();
});

$(document).on('click','.edit-step',function(){
    console.log('.edit-step',$(this).closest('.panel').parents());
    $(this).closest('.panel').removeClass('complete');
    $(this).closest('.panel').find('.panel-footer').hide().html('');
});

$(document).on('change','.custom-control-input',function(){
    console.log('click sumb');
    $('form#cartForm').submit();
});

$(document).on(\"beforeValidate\", \"form#cartForm\", function(event, messages, deferreds) {
    $(this).find('#button-checkout').attr('disabled', true);
    console.log('BEFORE VALIDATE TEST',messages);
}).on(\"afterValidate\", \"form#cartForm\", function(event, response, errorAttributes) {
    console.log('AFTER VALIDATE TEST',response.errors,errorAttributes);
    
    var form = $(this);
    console.log($(this).data('yiiActiveForm'));
    
    $.each(response.errors,function(attribute,errors){
    
    
    /*form.yiiActiveForm('add', {
        id: 'ordercreateform-delivery_id',
        name: 'delivery_id',
        container: '.field-ordercreateform-delivery_id',
        input: '#ordercreateform-delivery_id',
        error: '.invalid-feedback',
        validate:  function (attribute, value, messages, deferred, form) {
            yii.validation.required(value, messages, {message: \"Validation Message Here\"});
        }
    });*/


    
        var attr = $('.field-'+attribute+' .invalid-feedback');
        $.each(errors,function(key,error){
                attr.html(error);
            console.log(error);
        });

    });
    
    if (errorAttributes.length > 0) {
    
  //  ordercreateform-delivery_id
        $(this).find('#button-checkout').attr('disabled', false);
    }
    return false;
});
$(document).on(\"beforeSubmit\", \"form#cartForm\", function (event, messages) {



    var form = $(this);

    var formData = form.serialize();

    $.ajax({
        url: form.attr(\"action\"),
        type: form.attr(\"method\"),
        data: formData,
        success: function (data) {

            if(data.step === 1){
                $('.checkout__contacts').addClass('complete');
                $('.checkout__contacts .panel-footer').show().html(data.complete.join(', '));
                $('.checkout__delivery').removeClass('disabled');
                    
            } else if (data.step === 2) {
console.log('aaaaaaaaaa');
                $('.checkout__contacts').addClass('complete').find('.label').removeClass('label-default').addClass('label-success').html(' ');
                $('.checkout__delivery').removeClass('disabled');
            }
        $.each(data.field,function(k,field){

            console.log(field);
        });

             console.log(data);
        },
        error: function () {
            alert(\"Something went wrong\");
        }
    });

//window.dataLayer = window.dataLayer || [];
//dataLayer.push({event:'account_register'});
    return false;
}).on('submit', function(e){
   // e.preventDefault();
});

", \yii\web\View::POS_END);

?>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'action' => ['/cart/default/checkout'],
    'id' => 'cartForm',
    //'validationUrl'=>['/cart/default/checkout-validate'],
    'options' => ['class' => 'form-horizontal'],
]);


\panix\engine\CMS::dump($_SESSION);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="checkout__step checkout__contacts panel panel-default">
            <div class="panel-heading">
                <span class="label label-default">1</span> Ваши данные
                <a href="#" class="edit-step">edit</a>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#home" aria-controls="home" role="tab"
                                                              data-toggle="tab">Я НОВЫЙ ПОКУПАТЕЛЬ</a>
                    </li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Я
                            ПОСТОЯННЫЙ ПОКУПАТЕЛЬ</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
<?= Html::hiddenInput('step','contact'); ?>

                        <?= $form->field($model, 'user_name'); ?>
                        <?= $form->field($model, 'user_lastname'); ?>
                        <?= $form->field($model, 'user_phone')->widget(\panix\ext\telinput\PhoneInput::class,[
                            'jsOptions'=>[
                                'dropdownContainer'=>new \yii\web\JsExpression('document.querySelector(".iti")')
                            ]
                        ]); ?>
                        <?= $form->field($model, 'user_email'); ?>

                        <?= $form->field($model, 'user_comment')->textarea(['style' => 'resize:none;']); ?>
                        <?php if (Yii::$app->user->isGuest && $model->register) { ?>

                            <?= $form->field($model, 'register')->checkbox(); ?>

                        <?php } ?>

                        <?= Html::button(Yii::t('cart/default', 'Далее'), ['class' => 'btn btn-warning btn-lg','id'=>'button-contact','name'=>'dsadsa','value'=>111111]) ?>



                    </div>
                    <div role="tabpanel" class="tab-pane" id="profile">asdasd</div>
                </div>
            </div>
            <div class="panel-footer">

            </div>
        </div>
        <div class="checkout__step checkout__delivery panel panel-default disabled">
            <div class="panel-heading">
                <span class="label label-success">2</span> Доставка
            </div>
            <div class="panel-body">
                <?= Html::hiddenInput('step','delivery'); ?>
                <?php

                echo $form->field($model, 'delivery_id')->radioList(\yii\helpers\ArrayHelper::map($deliveryMethods, function ($model) {
                    //return ($model->system) ? $model->system : $model->id;
                    return $model->id;
                }, function ($model) {
                    $html = '';
                    if ($model->free_from) {
                        $html .= ' <small class="d-block text-muted">Бесплатно от &mdash; ' . Yii::$app->currency->number_format($model->free_from) . ' ' . Yii::$app->currency->active['symbol'] . '</small>';
                    }
                    if ($model->description) {
                        $html .= ' <small class="d-block text-muted">&mdash; ' . strip_tags($model->description) . '</small>';
                    }
                    return $model->name . $html;
                }), [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $isChecked = ($index == 0) ? 'checked' : '';
                        $isChecked = '';
                        $return = '<div class="mb-2 delivery-container-'.$value.' custom-control custom-radio">
                    <input type="radio" ' . $isChecked . ' id="radio-delivery-' . $index . '" name="' . $name . '" value="' . $value . '" class="custom-control-input" onClic2k="cart.delivery('.$value.');">
                    <label class="custom-control-label delivery_checkbox" for="radio-delivery-' . $index . '"><strong>' . $label . '</strong></label>
                    </div>';

                        return $return;
                    }
                ]);
                ?>
                <div id="delivery-form"></div>
                <?= Html::submitButton(Yii::t('cart/default', 'Далее'), ['disa2bled'=>'disa2bled','class' => 'btn btn-warning btn-lg','id'=>'button-delivery']) ?>

            </div>
        </div>




        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Collapsible Group Item #1
                        </a>


                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                           edit
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Collapsible Group Item #2
                        </a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                    <div class="panel-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingThree">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Collapsible Group Item #3
                        </a>
                    </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                    <div class="panel-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                </div>
            </div>
        </div>


    </div>
    <div class="col-sm-6">
        Items
     <?= Html::submitButton(Yii::t('cart/default', 'BUTTON_CHECKOUT'), ['disabled'=>'disabled','class' => 'btn btn-warning btn-lg ','id'=>'button-checkout']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
