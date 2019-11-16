<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \panix\mod\cart\models\Order $model
 */
$currency = Yii::$app->currency;

?>

<table border="0" cellspacing="0" cellpadding="0" style="width:100%;" class="table2">
    <tr>
        <td width="50%" valign="top">
            <table border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="table2">
                <?php if ($model->user_name) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('user_name'); ?>:
                            <strong><?= $model->user_name; ?></strong></td>
                    </tr>
                <?php } ?>
                <?php if ($model->user_phone) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('user_phone'); ?>:
                            <strong><?= $model->user_phone; ?></strong>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($model->user_email) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('user_email'); ?>:
                            <strong><?= $model->user_email; ?></strong>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
        <td width="50%" valign="top">
            <table border="0" cellspacing="0" cellpadding="5" style="width:100%;">
                <?php if ($model->user_address) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('user_address'); ?>:
                            <strong><?= $model->user_address; ?></strong>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($model->deliveryMethod) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('delivery_id'); ?>:
                            <strong><?= Yii::$app->formatter->asHtml($model->deliveryMethod->name); ?></strong>
                    </tr>
                <?php } ?>
                <?php if ($model->paymentMethod) { ?>
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px dotted #777;">
                            <?= $model->getAttributeLabel('payment_id'); ?>:
                            <strong><?= Yii::$app->formatter->asHtml($model->paymentMethod->name); ?></strong>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>

<br/><br/>
<?php if ($model->products) { ?>
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered">
        <thead>
        <tr>
            <th width="35%" colspan="2" class="text-center"><?= Yii::t('cart/default', 'TABLE_PRODUCT'); ?></th>
            <th width="10%" class="text-center"><?= Yii::t('cart/default', 'TABLE_NUM'); ?></th>
            <th width="15%" class="text-center"><?= Yii::t('cart/default', 'PRICE_PER_UNIT'); ?></th>
            <th width="20%" class="text-center"><?= Yii::t('cart/default', 'TOTAL_COST'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalCountQuantity = 0;
        $totalCountPrice = 0;
        $totalCountPriceAll = 0;
        foreach ($model->products as $product) {
            /**
             * @var \panix\mod\cart\models\OrderProduct $product
             */
            $totalCountQuantity += $product->quantity;
            $totalCountPrice += $product->price;
            $totalCountPriceAll += $product->price * $product->quantity;
            if ($product->originalProduct) {
                $image = $product->originalProduct->getMainImage('50x50')->url;
            } else {
                $image = '/uploads/no-image.png';
            }

            $price = $currency->convert($product->price, $product->currency_id);

            ?>
            <tr>
                <td width="10%"
                    align="center"><?= Html::img(Url::to($image, true), ['width' => 50, 'height' => 50]); ?></td>
                <td width="40%"><?= $product->name; ?></td>
                <td align="center"><?= $product->quantity; ?></td>
                <td align="center"><?= $currency->number_format($price) ?>
                    <?= $currency->active['symbol'] ?></td>
                <td align="center"><?= $currency->number_format($price * $product->quantity) ?>
                    <?= $currency->active['symbol'] ?></td>
            </tr>
        <?php } ?>

        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">Всего</th>
            <th class="text-center"><?= $totalCountQuantity; ?></th>
            <th class="text-center"><?= $currency->number_format($currency->convert($totalCountPrice)); ?>
                <?= $currency->active['symbol'] ?></th>
            <th class="text-center"><?= $currency->number_format($currency->convert($totalCountPriceAll)); ?>
                <?= $currency->active['symbol'] ?></th>
        </tr>
        </tfoot>
    </table>
    <br/>
    <hr/>
    <div class="text-right">

        <?php if ($model->delivery_price > 0) { ?>
            <p><?= Yii::t('cart/default', 'COST_DELIVERY'); ?>:
                <strong><?= $currency->number_format($model->delivery_price); ?> <?= $currency->active['symbol'] ?></strong>
            </p>
        <?php } ?>
        <?= Yii::t('cart/default', 'TOTAL_PAY'); ?>:
        <h3><?= $currency->number_format($model->total_price); ?>
            <?= $currency->active['symbol'] ?></h3>
    </div>
<?php } ?>


