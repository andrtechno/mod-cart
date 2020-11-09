<?php

use panix\engine\Html;
use panix\ext\fancybox\Fancybox;

/**
 * @var $this \yii\web\View
 */#ordercreateform-delivery_id
$js = <<<JS


function ajax() {
    var form = $('#order-form');
        $.ajax({
            type:'POST',
            url:form.attr('action'),
            data:form.serialize()+'&onChangeDelivery=true',
            dataType:'html',
            beforeSend:function(){
                $('#delivery-form').addClass('pjax-loading');
            },
            success:function(data){
                $('#delivery-form').removeClass('pjax-loading').html(data);
                if($('#order-delivery_type').val() === 'warehouse'){
                    $('.field-order-delivery_address').hide();
                    $('.field-order-delivery_address').find('input').val('');
                }else{
                    $('.field-order-delivery_address').show();
                }
            }
        });
}
ajax();

$(document).on('change','#order-delivery_type',function(e, clickedIndex, isSelected, previousValue){

    //if($(this).val() == 'warehouse'){
        ajax();
   // }else{
     //   ajax();
        //$('.field-order-delivery_address').show();
    //}
});


$(document).on('change','#order-delivery_city_ref',function(e, clickedIndex, isSelected, previousValue){
        ajax();
});


$(document).on('change','#order-delivery_id',function(e, clickedIndex, isSelected, previousValue){
    delivery_id = $(this).val();
    var checkSystem  = Number.parseInt(delivery_id);
    if($(this).val() == 2){
        ajax();
    }else{
        $('.field-order-delivery_address').show();
        $('#delivery-form').html('');
    }

});

JS;
$this->registerJs($js);

?>
<?php if ($model->call_confirm) { ?>
    <div class="alert alert-info">Мне можно не звонить!</div>
<?php } ?>

<?php if ($model->buyOneClick) { ?>
    <div class="alert alert-info"><?= Yii::t('cart/admin', 'MSG_BUYONECLICK'); ?></div>
<?php } ?>


<?php if ($model->points > 0) { ?>
    <div class="alert alert-info"><?= Yii::t('default', 'BONUS_ACTIVE', $model->points); ?></div>
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

