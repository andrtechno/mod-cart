<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\ShopProduct;
use yii\bootstrap\Alert;
?>
<div id="cart-left" class="shopping-cart">

    <div class="col-md-12 col-sm-12">
        <h1><?= $this->context->pageName; ?></h1>
        <?php if (Yii::$app->session->hasFlash('success')) { ?>


            <?php
            echo Alert::widget([
                'options' => ['class' => 'alert-success fadeOut-time'],
                'body' => Yii::$app->session->getFlash('success'),
            ]);
            ?>


        <?php } ?>
        <?php if (Yii::$app->session->hasFlash('error')) { ?>
            <div class="alert alert-danger fadeOut-time" role="alert">
                <i class="fa fa-times-circle fa-2x"></i>
                <?php
                foreach (Yii::$app->session->getFlash('error') as $flash) {
                    echo $flash;
                }
                ?>
            </div>
        <?php } ?>
        <?php
        $config = Yii::$app->settings->get('shop');
        /* if (Yii::$app->user->hasFlash('success')) {
          Yii::$app->tpl->alert('success', Yii::$app->user->getFlash('success'));
          }
          if (Yii::$app->user->hasFlash('success_register')) {
          Yii::$app->tpl->alert('success', Yii::$app->user->getFlash('success_register'));
          } */
        ?>

        <div class="table-responsive">
            <table width="100%" border="0" id="cart-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th align="center" style="width:10%"><?= Yii::t('cart/default', 'TABLE_IMG') ?></th>
                        <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_NAME') ?></th>
                        <?php if ($config['wholesale']) { ?>
                            <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_PCS') ?></th>
                        <?php } ?>
                        <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_NUM') ?></th>
                        <th align="center" style="width:30%"><?= Yii::t('cart/default', 'TABLE_SUM') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->getOrderedProducts()->getModels() as $product) { //$model->getOrderedProducts()->getData()   ?> 
                        <tr>
                            <td width="110px" align="center">
                        
                <?php

                    echo Html::img(Url::to($product->originalProduct->getMainImageUrl('100x')), ['alt' => $product->originalProduct->name]);
      
                ?>
                            </td>
                            <td>
                                <?= Html::beginTag('h3') ?>
                                <?= $product->getRenderFullName(false); ?>
                                <?= Html::endTag('h3') ?>
                                <?= Html::beginTag('span', array('class' => 'price')) ?>
                                <?= ShopProduct::formatPrice(Yii::$app->currency->convert($product->price)) ?>
                                <?= Yii::$app->currency->active->symbol; ?>
                                <?= Html::endTag('span') ?> 
                            </td>
                            <?php if ($config['wholesale']) { ?>
                                <td align="center">
                                    <?= $product->prd->pcs ?>
                                </td>
                            <?php } ?>
                            <td align="center">
                                <?= $product->quantity ?>
                            </td>
                            <td align="center">
                                <?php
                                if ($config['wholesale']) {
                                    echo ShopProduct::formatPrice(Yii::$app->currency->convert($product->price * $product->quantity * $product->prd->pcs));
                                } else {
                                    echo ShopProduct::formatPrice(Yii::$app->currency->convert($product->price * $product->quantity));
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
                <div class="row">
                    <div class="col-md-6"><?= $model->getAttributeLabel('user_name') ?></div>
                    <div class="col-md-6 text-right"><?= Html::encode($model->user_name); ?></div>
                    <div class="col-md-6"><?= $model->getAttributeLabel('user_email') ?></div>
                    <div class="col-md-6 text-right"><?= Html::encode($model->user_email); ?></div>
                    <div class="col-md-6"><?= $model->getAttributeLabel('user_phone') ?></div>
                    <div class="col-md-6 text-right"><?= Html::encode($model->user_phone); ?></div>
                    <div class="col-md-6"><?= $model->getAttributeLabel('user_address') ?></div>
                    <div class="col-md-6 text-right"><?= Html::encode($model->user_address); ?></div>
                    <?php if ($model->delivery_price > 0) { ?>
                        <div class="col-md-6"><?= Yii::t('cart/default', 'COST_DELIVERY') ?></div>
                        <div class="col-md-6 text-right">
                            <?= ShopProduct::formatPrice(Yii::$app->currency->convert($model->delivery_price)) ?>
                            <?= Yii::$app->currency->active->symbol ?>
                        </div>
                    <?php } ?>
                    <div class="col-md-6"><?= Yii::t('cart/default', 'DELIVERY') ?></div>
                    <div class="col-md-6 text-right"><?= Html::encode($model->delivery_name); ?></div>
                    <?php if (!empty($model->user_comment)) { ?>
                        <div class="col-md-6"><?= $model->getAttributeLabel('user_comment') ?></div>
                        <div class="col-md-6 text-right"><?= Html::encode($model->user_comment); ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>




    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Способ оплаты и доставки</div>
            <div class="panel-body">

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
            <div class="panel-heading"><?= Yii::t('cart/default', 'Состояние заказа') ?> <span class="label label-success pull-right" style=""><?= $model->status_name ?></span></div>
            <div class="panel-body">
                <?php if ($model->paid) { ?>
                    <?= Yii::t('cart/Order', 'PAID') ?>: <span class="label label-success"><?= Yii::t('app', 'YES') ?></span>
                <?php } else { ?>
                    <?= Yii::t('cart/Order', 'PAID') ?>: <span class="label label-default"><?= Yii::t('app', 'NO') ?></span>
                <?php } ?>
                <br/>
                Цена доставки:
                <?= ShopProduct::formatPrice(Yii::$app->currency->convert($model->delivery_price)) ?>
                <?= Yii::$app->currency->active->symbol ?>
                <br/>
                <?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
                <span class="label label-success"><?= ShopProduct::formatPrice(Yii::$app->currency->convert($model->full_price)) ?></span> 
                <?= Yii::$app->currency->active->symbol ?>
            </div>
        </div>
    </div>


</div>
