<?php

use yii\widgets\Pjax;
use panix\engine\grid\GridView;
use panix\mod\cart\models\OrderProduct;
use panix\engine\Html;
use panix\engine\CMS;

/**
 * @var \panix\mod\cart\models\Order $model
 * @var \yii\web\View $this
 */
$symbol = Yii::$app->currency->active['symbol'];

Pjax::begin([
    'id' => 'pjax-container-products',
    // 'enablePushState' => false,
    // 'linkSelector' => 'a:not(.linkTarget)'
]);
$buttons = [];
if (!$model->apply_user_points && $model->status_id != $model::STATUS_SUBMITTED) {

    $buttons[] = [
        'label' => Yii::t('shop/admin', 'CREATE_PRODUCT'),
        'url' => '#',
        'options' => ['class' => 'btn btn-success btn-sm', 'data-toggle' => "modal", 'data-target' => "#cart-add-product"]
    ];

}
echo GridView::widget([
    //  'id' => 'orderedProducts',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $model->getOrderedProducts(),
    // 'filterModel' => $searchModel,
    'showFooter' => false,
    'footerRowOptions' => ['style' => 'font-weight:bold;', 'class' => 'text-center'],
    'layoutOptions' => [
        'title' => Yii::t('cart/admin', 'ORDER_PRODUCTS'),
        'buttons' => $buttons
    ],
    'columns' => [
        'image' => [
            'class' => 'panix\engine\grid\columns\ImageColumn',
            'attribute' => 'image',
            'header' => Yii::t('cart/OrderProduct', 'IMAGE'),
            // 'filter'=>true,
            'value' => function ($model) {
                /** @var $model OrderProduct */

                if ($model->getProduct()) {
                    return Html::a(Html::img($model->getProductImage('50x50')), $model->getProductImage(), ['data-pjax' => false]);
                } else {
                    return \panix\engine\Html::tag('span', 'товар удален', ['class' => 'badge badge-danger']);
                }

            },
        ],

        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                /** @var $model OrderProduct */
                if ($model->currency_id && $model->currency_rate) {
                    $priceValue = Yii::$app->currency->convert($model->price / $model->currency_rate, $model->currency_id);
                } else {
                    $priceValue = $model->price;
                }
                $discount = '';
                if ($model->discount) {
                    $priceValue = $priceValue;
                    $discount = ' <span class="badge badge-danger">-' . $model->discount . '</span>';
                }
                $variantsConfigure = '';
                if ($model->variantsConfigure) {
                    foreach ($model->variantsConfigure as $configure) {
                        $variantsConfigure .= "<div>{$configure->name}: <strong>{$configure->value}</strong></div>";
                    }
                }
                /*$productName = $model->name;
                if ($model->configurable_name) {
                    $productName = $model->configurable_name;
                    if($model->id != $model->configurable_id){
                        $productName.= $model->configureProduct->id;
                    }

                }*/
                $price = Yii::$app->currency->number_format($priceValue) . ' ' . Yii::$app->currency->main['symbol'];
                return $model->getProductName(false, ['data-pjax' => '0']) . '<br/>' . $variantsConfigure . $price . $discount;
            },
        ],
        [
            'attribute' => 'quantity',
            'footer' => $model->productsCount,
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center quantity'],
            'value' => function ($model) {

                $value = $model->quantity;
                $dataValue = $model->quantity;
                $units = \panix\mod\shop\models\Product::unitsList();
                $unit = ' <span>' . Yii::t('shop/Product', 'UNITS_CUT', ['n' => $model->unit]) . '</span>';
                if (Yii::$app->settings->get('cart', 'quantity_convert')) {
                    $value = $model->quantity / $model->in_box . $unit;
                    $dataValue = $model->quantity / $model->in_box;
                } else {
                    //$unit = ' <span>' . Yii::t('shop/Product', 'UNITS_CUT', ['n' => $model->unit]) . '</span>';
                    $value = $model->quantity . $unit;
                    $dataValue = $model->quantity;
                }

                //return Html::textInput('quantity[' . $model->product_id . ']', $model->quantity, ['data-title'=>$model->name,'data-product'=>$model->product_id,'readonly' => 'readonly','tabindex'=>-1, 'class' => 'form-control d-inline text-center', 'style' => 'max-width:50px']);
                return Html::button($value . ' ' . Html::icon('edit'), ['data-value' => $dataValue, 'data-title' => $model->name, 'data-product' => $model->product_id, 'data-step' => 1, 'class' => 'btn2 badge badge-light', 'style' => 'border:0;']);
            }

        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center', 'style' => 'min-width:120px'],
            'footer' => Yii::$app->currency->number_format($model->total_price) . ' ' . Yii::$app->currency->main['symbol'],
            'value' => function ($model) {
                /** @var $model OrderProduct */
                //if ($model->currency_id && $model->currency_rate) {
                //    $priceValue = Yii::$app->currency->convert($model->price, $model->currency_id);
                // } else {

                //  }
                //if ($model->discount) {
                //    $priceValue = ($model->price) * $model->quantity; // - $model->discount
                //} else {
                    $priceValue = $model->price * $model->quantity;
                //}
                return Yii::$app->currency->number_format($priceValue) . ' ' . Yii::$app->currency->main['symbol'];
            }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $data, $key) use ($model) {
                    if (!$model->apply_user_points && $model->status_id != $model::STATUS_SUBMITTED) {
                        return Html::a('<i class="icon-delete"></i>', '#', [
                            'title' => Yii::t('app/default', 'DELETE'),
                            'class' => 'btn btn-sm btn-danger',
                            'onClick' => "return deleteOrderedProduct($data->id, $data->order_id);"
                        ]);
                    }
                }
            ]
        ]
    ]
]);
Pjax::end();

?>


    <div class="panel-container">
        <ul class="list-group">
            <?php if ($model->user_id && Yii::$app->settings->get('user', 'bonus_enable')) { ?>
                <li class="list-group-item">
                    Бонусы к зачаслению:
                    <h5 class="m-0 float-right"><?= floor($model->total_price * Yii::$app->settings->get('user', 'bonus_ratio')) ?>
                        <span class="text-muted"><?= $symbol ?></span></h5>
                </li>
            <?php } ?>
            <?php if ($model->delivery_price > 0) { ?>
                <li class="list-group-item">
                    <?= Yii::t('cart/Order', 'DELIVERY_PRICE') ?>: <strong
                            class="float-right"><?= Yii::$app->currency->number_format($model->delivery_price); ?> <?= $symbol; ?></strong>
                </li>
            <?php } ?>
            <li class="list-group-item">
                <?= Yii::t('cart/default', 'ORDER_PRICE') ?>: <strong
                        class="float-right"><?= Yii::$app->currency->number_format($model->total_price) ?> <span
                            class="text-muted"><?= $symbol ?></span></strong>
            </li>
            <?php if ($model->discount) { ?>
                <li class="list-group-item">
                    <?= $model::t('DISCOUNT') ?>:
                    <?php if ('%' === substr($model->discount, -1, 1)) { ?>
                        <strong class="float-right"><?= $model->discount; ?></strong>
                    <?php } else { ?>
                        <strong class="float-right"><?= Yii::$app->currency->number_format($model->discount) ?> <span
                                    class="text-muted"><?= $symbol ?></span></strong>
                    <?php } ?>
                </li>
            <?php } ?>
            <?php if ($model->diff_price) { ?>
                <li class="list-group-item">
                    <?= Yii::t('cart/admin', 'INCOME') ?>:
                    <?php if ($model->discount) { ?>
                        <?php if ('%' === substr($model->discount, -1, 1)) {
                            $sum = $model->diff_price * ((double)$model->discount) / 100;
                            ?>
                            <strong class="float-right">
                                <?= Yii::$app->currency->number_format($model->diff_price - $sum) ?>
                                <span class="text-muted"><?= $symbol ?></span>
                            </strong>
                        <?php } else { ?>
                            <strong class="float-right">
                                <?= Yii::$app->currency->number_format($model->diff_price - $model->discount) ?>
                                <span class="text-muted"><?= $symbol ?></span>
                            </strong>
                        <?php } ?>
                    <?php } else { ?>
                        <strong class="float-right">
                            <?= Yii::$app->currency->number_format($model->diff_price) ?>
                            <span class="text-muted"><?= $symbol ?></span>
                        </strong>
                    <?php } ?>
                </li>
            <?php } ?>

            <li class="list-group-item d-flex justify-content-between">
                <span class="d-flex align-items-center mr-4"><?= $model::t('FULL_PRICE') ?>:</span>
                <h4 class="m-0">
                    <span class="total-price"><?= Yii::$app->currency->number_format($model->full_price); ?></span>
                    <small class="text-muted"><?= $symbol; ?></small>
                </h4>
            </li>


        </ul>
    </div>


    <div class="card mt-4">
        <div class="card-header">
            <h5><?= Yii::t('cart/admin', 'Доп информация'); ?></h5>
        </div>
        <div class="card-body">

            <?php
            $browser = new \panix\engine\components\Browser($model->user_agent);
            ?>

            <div class="list-group-item d-flex justify-content-between">
                <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('ip_create'); ?>:</span>
                <span class="m-0"><?= CMS::ip($model->ip_create); ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between">
                <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('created_at'); ?>:</span>
                <span class="m-0"><?= CMS::date($model->created_at); ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between">
                <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('updated_at'); ?>:</span>
                <span class="m-0"><?= CMS::date($model->updated_at); ?></span>
            </div>

            <div class="list-group-item d-flex justify-content-between">
                <span class="d-flex align-items-center mr-4"><?= $model->getAttributeLabel('user_agent'); ?>:</span>
                <span class="m-0 text-right">
                    <?= $browser->getBrowser(); ?> (v <?= $browser->getVersion(); ?>)
                    <br/>
                <?= $browser->getPlatformIcon(); ?> <?= $browser->getPlatform(); ?>
                </span>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
<?php
if (!$model->isNewRecord) {
    $this->registerJs("

var locale = {
    OK: 'OK',
    CONFIRM: 'Confirm',
    CANCEL: 'Cancel'
};

bootbox.addLocale('custom', locale);


$(document).on('click','.quantity button',function(e){
    var title = $(this).data('title');
    var product_id = $(this).data('product');
    var step = $(this).data('step');

    var value = $(this).data('value');
    if($(this).prop('readonly')){
        $(this).prop('readonly',false);
    }else{
        $(this).prop('readonly',true);
    }


    bootbox.prompt({
        value:value,
        title: title, 
        message: 'Укажите количество',
        locale: 'custom',
        inputType: 'number',
        backdrop:true,
        onEscape:true,
        min:step,
        step:step,
        //centerVertical: true,
        callback: function (result) {
            if(!result){
                bootbox.hideAll();
            }
            var pattern = /^\d+$/;
            var valid = false;

            if(pattern.test(result) && result <= 999 && result >= step){
                valid=true;
            }
console.log(valid);
            if(valid){
                $(this).find('input').removeClass('error');

                if(value != result && valid){
                    $.ajax({
                        url:'/admin/cart/default/quantity?id=" . $model->id . "',
                        type:'POST',
                        data:{product_id:product_id,quantity:result},
                        dataType:'json',
                        success:function(response){
                            if(response.success){
                                common.notify(response.message,'success');
                                $.pjax.reload({container:\"#pjax-container-products\",timeout:false});
                                $('.total-price').html(response.total_formatted);
                            }
                        }
                    });
                }
                return true;
            }else{
                $(this).find('input').addClass('error');
                return false;
            }
        }
    });
});

/*
$(document).on('keyup','.quantity input',function(e){
    console.log(e,e.keyCode);
    var value = $(this).val();
    if(e.keyCode !== 8){ //backspace
    
    
        var pattern = /^\d+$/;
        if(pattern.test(value)){
            //console.log('patt');
            $(this).removeClass('error');
        }else{
            $(this).addClass('error');
            console.log('ошибка');
            return false;
        }
    }
    console.log('das');
});*/

");
}
