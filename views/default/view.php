<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;
use panix\engine\bootstrap\Alert;
$config = Yii::$app->settings->get('shop');
$currency = Yii::$app->currency;
?>
<div class="shopping-cart row">

    <div class="col-md-12 col-sm-12">
        <h1><?= $this->context->pageName; ?></h1>
        <?php


       /* $liqpay = new \panix\mod\cart\widgets\payment\liqpay\LiqPay('i61699065543', 'ztgu6RktfUWoSCxEDuoBsOlbm762LQScuQ01c0BI');
        $html = $liqpay->cnb_form(array(
            'action' => 'pay',
            'amount' => '1',
            'currency' => 'USD',
            'description' => 'description text',
            'order_id' => \panix\engine\CMS::gen(5),
            'version' => '3',
            'sandbox' => '1', //1 test mode
            'server_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => 4, 'result' => true], true),
            'result_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => 4], true),
        ));
        echo $html;
*/
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

                            <span class="price price-sm">
                            <?= Yii::$app->currency->number_format($currency->convert($product->price)) ?>
                            <sub><?= $currency->active->symbol; ?></sub>
                            </span>

                        </td>
                        <td align="center">
                            <?= $product->quantity ?>
                        </td>
                        <td align="center">
                            <?php
                            if ($config->wholesale) {
                                echo $currency->number_format($currency->convert($product->price * $product->quantity * $product->prd->pcs));
                            } else {
                                echo $currency->number_format($currency->convert($product->price * $product->quantity));
                            }
                            ?>
                            <?= $currency->active->symbol; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4><?= Yii::t('cart/default', 'USER_DATA') ?></h4></div>
            <div class="card-body">

                <div class="form-group"><?= $model->getAttributeLabel('user_name') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->user_name); ?></div>
                </div>

                <div class="form-group"><?= $model->getAttributeLabel('user_email') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->user_email); ?></div>
                </div>

                <div class="form-group"><?= $model->getAttributeLabel('user_phone') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->user_phone); ?></div>
                </div>


                <?php if (!empty($model->user_comment)) { ?>
                    <div><?= $model->getAttributeLabel('user_comment') ?>:<br/>
                        <?= Html::encode($model->user_comment); ?></div>
                <?php } ?>

            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4>Доставки и оплаты</h4></div>
            <div class="card-body">
                <h4>Доставка</h4>
                <?php if ($model->delivery_price > 0) { ?>
                    <div class="form-group">
                        <?= Yii::t('cart/default', 'COST_DELIVERY') ?>:
                        <div class="float-right font-weight-bold">
                            <span class="price">
                                <?= $currency->number_format($currency->convert($model->delivery_price)) ?>
                                <sub><?= $currency->active->symbol ?></sub>
                            </span>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group"><?= $model->getAttributeLabel('delivery_id') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->delivery_name); ?></div>
                </div>
                <div class="form-group"><?= $model->getAttributeLabel('user_address') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->user_address); ?></div>
                </div>
                <h4 class="mt-5">Оплата</h4>
                <div class="form-group"><?= $model->getAttributeLabel('payment_id') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->payment_name); ?></div>
                </div>
                <?php


                if ($model->deliveryMethod) {

                    foreach ($model->deliveryMethod->paymentMethods as $payment) {
                        ?>
                        <?php
                        $activePay = ($payment->id == $model->payment_id) ? '<span class="icon-checkmark " style="font-size:20px;color:green"></span>' : '';
                        ?>
                        <h5><?= $activePay; ?> <?= $payment->name ?></h5>
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
        <div class="card">
            <div class="card-header">
                <h4><?= Yii::t('cart/default', 'Состояние заказа') ?>
                    <span class="badge badge-success float-right"><?= $model->statusName ?></span>
                </h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?php if ($model->paid) { ?>
                        <?= Yii::t('cart/Order', 'PAID') ?>:
                        <div class="float-right">
                            <span class="badge badge-success"><?= Yii::t('app', 'YES') ?></span>
                        </div>
                    <?php } else { ?>
                        <?= Yii::t('cart/Order', 'PAID') ?>:
                        <div class="float-right">
                            <span class="badge badge-danger"><?= Yii::t('app', 'NO') ?></span>
                        </div>
                    <?php } ?>

                </div>

                <div class="form-group"><?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
                    <div class="float-right font-weight-bold">
                        <span class="price price-lg">
                            <?= $currency->number_format($model->full_price) ?>
                            <sub><?= $currency->active->symbol ?></sub>
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
