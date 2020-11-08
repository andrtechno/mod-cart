<?php

use panix\engine\Html;
use panix\ext\fancybox\Fancybox;

/**
 * @var $this \yii\web\View
 */#ordercreateform-delivery_id
$js2 = <<<JS

$(document).on('change','#order-delivery_city_ref',function(e, clickedIndex, isSelected, previousValue){
    console.log('delivery_city_ref');
        $.ajax({
            type:'POST',
            url:$('#order-form').attr('action'),
            data:$('#order-form').serialize()+'&onChangeDelivery=true',
            dataType:'html',
            beforeSend:function(){
                $('#delivery-form').addClass('pjax-loading');
            },
            success:function(data){
                $('#delivery-form').removeClass('pjax-loading').html(data);
            }
        });
});

JS;
$this->registerJs($js2,\yii\web\View::POS_END,'delivery_city_ref');
$js = <<<JS


function buildFields(data){
    $('#delivery-form').html('');
    $.each(data.field,function(key,dat){
        var group = $('<div class="form-group field-'+dat.id+'"><div class="invalid-feedback-cart"></div></div>');

        if(dat.type === 'dropdownlist'){
            var field = $('<select name="'+dat.name+'" id="'+dat.id+'" class="form-control" /></select>');
            
            group.prepend(field);
            $.each(dat.items, function(key2, value) {
                var option = $('<option></option>');
                option.attr('value', key2);
                if(dat.value !== undefined){
                    if(dat.value == key2){
                        option.attr('selected', true);
                    }
                }

                option.text(value);
                field.append(option);
            });

            if(dat.error){
                //$(attribute.container).removeClass('field-is-invalid');
                $('#cartForm').yiiActiveForm('add', {
                    id: dat.id,
                    name: dat.name,
                    container: '.field-'+dat.id+'',
                    input: '#'+dat.id,
                    error: '.invalid-feedback-cart',
                    validate:  function (attribute, value, messages, deferred, form) {
                        yii.validation.required(value, messages, {message: dat.error});
                        console.log('validate',attribute,value);
                        if(value){
                        $(attribute.container).removeClass('field-is-invalid');
                        }else{
                        $(attribute.container).addClass('field-is-invalid');
                        }

                    }
                });
            }
            $('#delivery-form').append(group);
           // $('#'+dat.id).selectpicker(dat.jsOptions);
         
        }
    });

}


$(document).on('change','#order-delivery_id',function(e, clickedIndex, isSelected, previousValue){
    //delivery_id = $(this,'option:selected').val();
    delivery_id = $(this).val();
    console.log($(this).val());
    var checkSystem  = Number.parseInt(delivery_id);
    if($(this).val() == 2){
        console.log('system', delivery_id);

        $.ajax({
            type:'POST',
            url:$('#order-form').attr('action'),
            data:$('#order-form').serialize()+'&onChangeDelivery=true',
            dataType:'html',
            success:function(data){
                        $('#delivery-form').html(data);
                //buildFields(data);
                //if(data.show_address){
                //    $('#user-address-input').show();
                //}else{
                //    $('#user-address-input').hide();
                //}
            }
        });
    }else{
        $('#user-address-input').show();
        $('#delivery-form').html('');
    }
//.selectpicker('destroy')
        $('#order-delivery_address').replaceWith('<input id="order-delivery_address" name="Order[delivery_address]" class="form-control" />');
        $('#order-form').yiiActiveForm('remove', 'delivery_city_ref');
});

JS;
$this->registerJs($js);

?>
<?php if ($model->call_confirm) { ?>
    <div class="alert alert-info">Мне можно не звонить!</div>
<?php } ?>

<?php if ($model->buyOneClick) { ?>
    <div class="alert alert-info"><?= Yii::t('cart/admin','MSG_BUYONECLICK');?></div>
<?php } ?>


<?php if ($model->points > 0) { ?>
    <div class="alert alert-info"><?= Yii::t('default','BONUS_ACTIVE',$model->points);?></div>
<?php } ?>
<?php if (Yii::$app->hasModule('novaposhta') && $model->deliveryMethod) { ?>
    <?php if ($model->deliveryMethod->system == 'novaposhta') { ?>
        <div class="text-right">
            <?= Html::a(Html::icon('novaposhta') . ' ' . Yii::t('novaposhta/default', 'CREATE_EXPRESS_WAYBILL_CART'), ['/admin/novaposhta/express-invoice/create', 'order_id' => $model->primaryKey], ['data-pjax' => 0, 'class' => 'btn btn-danger mb-3']); ?>
        </div>
    <?php }
} ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
    <div class="col-md-6">
        <?= Fancybox::widget(['target' => '.image a']); ?>
        <?php

        //echo Html::a('add', 'javascript:openAddProductDialog(' . $model->id . ');', ['class' => 'btn btn-success']);
        if (!$model->isNewRecord) {
            ?>


                <?php
                echo $this->render('_addProduct', [
                    'model' => $model,
                ]);
                ?>

            <div id="orderedProducts">
                <?php
                if (!$model->isNewRecord) {
                    echo $this->render('_order-products', ['model' => $model]);
                }
                ?>
            </div>

        <?php } else { ?>
            <div class="alert alert-info">Товары можно будет добавить после создание заказа</div>
        <?php } ?>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5><?= Yii::t('cart/admin', 'ORDER_HISTORY'); ?></h5>
    </div>
    <div class="card-body">
        <?php
        echo $this->render('_history', ['model' => $model]);
        ?>
    </div>
</div>

