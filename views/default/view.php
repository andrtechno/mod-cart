<?php

use panix\engine\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;
use panix\engine\bootstrap\Alert;

/**
 * @var \panix\mod\cart\models\Order $model
 */
$config = Yii::$app->settings->get('shop');
$currency = Yii::$app->currency;
?>


<?php
if (Yii::$app->session->hasFlash('success-promocode')) {

    echo \panix\engine\bootstrap\Alert::widget([
        'options' => ['class' => 'alert alert-success'],
        'closeButton' => false,
        'body' => Yii::$app->session->getFlash('success-promocode')
    ]);

}
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
                    <th align="center" style="width:30%"><?= Yii::t('cart/default', 'QUANTITY') ?></th>
                    <th align="center"
                        style="width:30%"><?= Yii::t('cart/default', 'TABLE_SUM', ['currency' => $currency->active['symbol']]) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->getOrderedProducts()->getModels() as $product) {
                    /** @var \panix\mod\cart\models\OrderProduct $product */

                    //$model->getOrderedProducts()->getData()    ?>
                    <tr>
                        <td align="center" style="width:10%">

                            <?php
                            if ($product->originalProduct) {
                                echo Html::img(Url::to($product->originalProduct->getMainImage('100x')->url), ['alt' => $product->originalProduct->name]);
                            } else {
                                echo Html::tag('span', 'удален', ['class' => 'badge badge-danger']);
                            }


                            ?>
                        </td>
                        <td>

                            <h5><?= $product->getRenderFullName(false); ?> </h5>
                            <?php
                            if($product->sku){
                                echo $product->getAttributeLabel('sku').': <strong>'.$product->sku.'</strong>; ';
                            }
                            $attributesData = (array) $product->productAttributes->attributes;
                            // \panix\engine\CMS::dump($attributesData);die;
                            $query = \panix\mod\shop\models\Attribute::find();
                            $query->where(['IN', 'name', array_keys($attributesData)]);
                            $query->displayOnCart();
                            $query->sort();
                            $result = $query->all();
                            foreach ($result as $q) {
                                echo $q->title . ': ';
                                echo '<strong>'.$q->renderValue($attributesData[$q->name]) . '</strong>; ';
                            }
                            ?>
                            <br/>
                            <span class="price price-sm text-warning">
                                <?= $currency->number_format($currency->convert($product->price)) ?>
                                <sub><?= $currency->active['symbol']; ?>/шт.</sub>
                            </span>

                        </td>
                        <td align="center">
                            <strong><?= $product->quantity ?></strong>
                        </td>
                        <td align="center">
                             <span class="price text-warning">
                                <?= $currency->number_format($currency->convert($product->price * $product->quantity)); ?>
                                 <sub><?= $currency->active['symbol']; ?></sub>
                             </span>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <?php if (!$model->promoCode) { ?>
                    <tfoot>
                    <td colspan="2" class="text-right">
                        <label class="control-label h5" for="ordercreateform-promocode_id" style="margin-bottom: 0">
                            Введите промо-код
                        </label>
                    </td>
                    <td colspan="3">
                        <?php
                        echo Html::beginForm();
                        echo panix\mod\cart\widgets\promocode\PromoCodeWidget::widget([
                            'model' => $model,
                            'attribute' => 'promocode_id'
                        ]);
                        echo Html::error($model, 'promocode_id');
                        echo Html::endForm();

                        /*echo \panix\mod\cart\widgets\promocode\PromoCodeInput::widget([
                            'model' => $model,
                            'attribute' => 'promocode_id',
                            'options' => [
                                'placeholder' => 'Введите промо-код'
                            ]
                        ]);*/

                        ?>
                    </td>
                    </tfoot>
                <?php } ?>
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
                    <div class="float-right font-weight-bold"><?= Html::tel($model->user_phone); ?></div>
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
            <div class="card-header"><h4><?= Yii::t('cart/default', 'DELIVERY_PAYMENT') ?></h4></div>
            <div class="card-body">
                <h4>Доставка</h4>
                <?php if ($model->delivery_price > 0) { ?>
                    <div class="form-group">
                        <?= Yii::t('cart/default', 'COST_DELIVERY') ?>:
                        <div class="float-right font-weight-bold">
                            <span class="price">
                                <?= $currency->number_format($currency->convert($model->delivery_price)) ?>
                                <sub><?= $currency->active['symbol'] ?></sub>
                            </span>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group"><?= $model->getAttributeLabel('delivery_id') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->delivery_name); ?></div>
                </div>
                <div class="form-group"><?= $model->getAttributeLabel('delivery_address') ?>:
                    <div class="float-right font-weight-bold"><?= Html::encode($model->delivery_address); ?></div>
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
                <h4><?= Yii::t('cart/default', 'ORDER_STATUS') ?>
                    <span class="badge badge-success float-right"><?= $model->statusName ?></span>
                </h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?php if ($model->paid) { ?>
                        <?= Yii::t('cart/Order', 'PAID') ?>:
                        <div class="float-right">
                            <span class="badge badge-success"><?= Yii::t('app/default', 'YES') ?></span>
                        </div>
                    <?php } else { ?>
                        <?= Yii::t('cart/Order', 'PAID') ?>:
                        <div class="float-right">
                            <span class="badge badge-danger"><?= Yii::t('app/default', 'NO') ?></span>
                        </div>
                    <?php } ?>

                </div>
                <?php if ($model->promoCode) { ?>
                    <div class="form-group"><?= Yii::t('cart/default', 'PROMO_CODE_ACTIVATED') ?>:
                        <div class="float-right font-weight-bold">
                            <span class="badge2 badge-success2 text-success"><strong>-<?= $model->promoCode->discount; ?></strong></span>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group"><?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
                    <div class="float-right font-weight-bold">
                        <span class="price price-lg">
                            <?= $currency->number_format($model->full_price) ?>
                            <sub><?= $currency->active['symbol'] ?></sub>
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>
