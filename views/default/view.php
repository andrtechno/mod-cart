<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;
use panix\engine\bootstrap\Alert;
?>
<div id="cart-left" class="shopping-cart row">

    <div class="col-md-12 col-sm-12">
        <h1><?= $this->context->pageName; ?></h1>
        <?php
        $config = Yii::$app->settings->get('shop');


        $liqpay = new \panix\mod\cart\widgets\payment\liqpay\LiqPay('i61699065543', 'ztgu6RktfUWoSCxEDuoBsOlbm762LQScuQ01c0BI');
        $html = $liqpay->cnb_form(array(
            'action'         => 'pay',
            'amount'         => '1',
            'currency'       => 'USD',
            'description'    => 'description text',
            'order_id'       => \panix\engine\CMS::gen(5),
            'version'        => '3',
            'sandbox' => '1', //1 test mode
            'server_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => 4, 'result' => true], true),
            'result_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => 4], true),
        ));
echo $html;

        ?>

        <div class="table-responsive">
            <table id="cart-table" class="table table-striped">
                <thead>
                    <tr>
                        <th align="center" style="width:40%" colspan="2"><?= Yii::t('cart/default', 'TABLE_PRODUCT') ?></th>
                        <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_NUM') ?></th>
                        <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_SUM') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->getOrderedProducts()->getModels() as $product) { //$model->getOrderedProducts()->getData()    ?> 
                        <tr>
                            <td align="center" style="width:10%">

                                <?php
                                echo Html::img(Url::to($product->originalProduct->getMainImage('100x')->url), ['alt' => $product->originalProduct->name]);
                                ?>
                            </td>
                            <td>
                                <?= Html::beginTag('h3') ?>
                                <?= $product->getRenderFullName(false); ?>
                                <?= Html::endTag('h3') ?>
                                <?= Html::beginTag('span', array('class' => 'price')) ?>
                                <?= Yii::$app->currency->number_format(Yii::$app->currency->convert($product->price)) ?>
                                <?= Yii::$app->currency->active->symbol; ?>
                                <?= Html::endTag('span') ?> 
                            </td>
                            <td align="center">
                                <?= $product->quantity ?>
                            </td>
                            <td align="center">
                                <?php
                                if ($config->wholesale) {
                                    echo Yii::$app->currency->number_format(Yii::$app->currency->convert($product->price * $product->quantity * $product->prd->pcs));
                                } else {
                                    echo Yii::$app->currency->number_format(Yii::$app->currency->convert($product->price * $product->quantity));
                                }
                                ?>
                                <?= Yii::$app->currency->active->symbol; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>





    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?= Yii::t('cart/default', 'USER_DATA') ?></div>
            <div class="panel-body">

                
          
                    <div><?= $model->getAttributeLabel('user_name') ?>: <b><?= Html::encode($model->user_name); ?></b></div>
                    <div><?= $model->getAttributeLabel('user_email') ?>: <b><?= Html::encode($model->user_email); ?></b></div>
                    <div><?= $model->getAttributeLabel('user_phone') ?>: <b><?= Html::encode($model->user_phone); ?></b></div>
                    


                    <?php if (!empty($model->user_comment)) { ?>
                    <div><?= $model->getAttributeLabel('user_comment') ?>:<br/>
                            <?= Html::encode($model->user_comment); ?></div>
                    <?php } ?>
            
            </div>
        </div>
    </div>




    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Доставки и оплаты</div>
            <div class="panel-body">

                    <?php if ($model->delivery_price > 0) { ?>
                        <div><?= Yii::t('cart/default', 'COST_DELIVERY') ?>:
                        <b>
                            <?= Yii::$app->currency->number_format(Yii::$app->currency->convert($model->delivery_price)) ?>
                            <?= Yii::$app->currency->active->symbol ?>
                        </b>
                    <?php } ?>
                    <div><?= $model->getAttributeLabel('delivery_id') ?> <b><?= Html::encode($model->delivery_name); ?></b></div>
                    <div><?= $model->getAttributeLabel('user_address') ?>: <b><?= Html::encode($model->user_address); ?></b></div>
                    <div><?= $model->getAttributeLabel('payment_id') ?> <b><?= Html::encode($model->payment_name); ?></b></div>
                <?php
                if ($model->deliveryMethod) {
                    foreach ($model->deliveryMethod->paymentMethods as $payment) {
                        ?>
                        <?php
                        $activePay = ($payment->id == $model->payment_id) ? '<span class="icon-checkmark " style="font-size:20px;color:green"></span>' : '';
                        ?>
                        <h3><?= $activePay; ?> <?= $payment->name ?></h3>
                        <p><?= $payment->description ?></p>
                        <p><?= $payment->renderPaymentForm($model) ?></p>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading"><?= Yii::t('cart/default', 'Состояние заказа') ?> <span class="label label-success pull-right" style=""><?= $model->statusName ?></span></div>
            <div class="panel-body">
                <?php if ($model->paid) { ?>
                    <?= Yii::t('cart/Order', 'PAID') ?>: <span class="label label-success"><?= Yii::t('app', 'YES') ?></span>
                <?php } else { ?>
                    <?= Yii::t('cart/Order', 'PAID') ?>: <span class="label label-danger"><?= Yii::t('app', 'NO') ?></span>
                <?php } ?>

                <div>
                    <?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
                    <?= Yii::$app->currency->number_format($model->full_price) ?>
                    <?= Yii::$app->currency->active->symbol ?>
                </div>
            </div>
        </div>
    </div>


</div>
