<?php

use panix\engine\Html;
use panix\ext\fancybox\Fancybox;


/**
 * @var $this \yii\web\View
 */#ordercreateform-delivery_id
$js = <<<JS

    //$('#order-delivery_type').on('loaded.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        //ajax();
   // });
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
/*
$pattern = '#^catalog/(?P<slug>[0-9a-zA-Z_\/\-]+)/(?P<filter>\/[\w,\/]+)$#u';

$pathInfo = 'catalog/ukhod-dla-volos/kondicioner-dla-volos/filter/size/13,5/brand/1';
if (!preg_match($pattern, $pathInfo, $matches)) {
  //  return false;
}
CMS::dump($matches);die;
*/

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
            <?php
            $api = Yii::$app->novaposhta;
            $doc = \panix\mod\novaposhta\models\ExpressInvoice::findOne(['order_id' => $model->id]);
            if ($doc) {
                echo Html::a(Html::icon('novaposhta') . ' ' . Yii::t('novaposhta/default', 'UPDATE_EXPRESS_WAYBILL_CART'), ['/admin/novaposhta/express-invoice/update', 'id' => $doc->Ref], ['data-pjax' => 0, 'class' => 'btn btn-danger mb-3']);
             //   echo '<span class="text-warning"><i class="icon-warning"></i> ЭН уже создана: </span>';
            }else{
                echo Html::a(Html::icon('novaposhta') . ' ' . Yii::t('novaposhta/default', 'CREATE_EXPRESS_WAYBILL_CART'), ['/admin/novaposhta/express-invoice/create', 'order_id' => $model->primaryKey], ['data-pjax' => 0, 'class' => 'btn btn-danger mb-3']);
            }
            ?>

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
            if (!$model->apply_user_points && $model->status_id != $model::STATUS_SUBMITTED) {
                echo $this->render('_addProduct', [
                    'model' => $model,
                ]);
            }
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
