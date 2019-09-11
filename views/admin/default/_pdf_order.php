<?php

use yii\helpers\Html;
use yii\helpers\Url;

$currency = Yii::$app->currency;

?>

<table border="0" cellspacing="0" cellpadding="2" style="width:100%;" class="table2">
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('created_at'); ?>:</td>
        <td width="70%" align="left" class="text-left">
            <strong><?= Yii::$app->formatter->asDatetime($model->created_at); ?></strong></td>
    </tr>
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('user_name'); ?>:</td>
        <td width="70%" align="left" class="text-left"><strong><?= $model->user_name; ?></strong></td>
    </tr>
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('user_phone'); ?>:</td>
        <td width="70%" align="left" class="text-left"><strong><?= $model->user_phone; ?></strong></td>
    </tr>
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('user_address'); ?>:</td>
        <td width="70%" align="left" class="text-left"><strong><?= $model->user_address; ?></strong></td>
    </tr>
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('delivery_id'); ?>:</td>
        <td width="70%" align="left" class="text-left">
            <strong><?= Yii::$app->formatter->asHtml($model->deliveryMethod->name); ?></strong></td>
    </tr>
    <tr>
        <td width="30%" align="left" class="text-left"><?= $model->getAttributeLabel('payment_id'); ?>:</td>
        <td width="70%" align="left" class="text-left">
            <strong><?= Yii::$app->formatter->asHtml($model->paymentMethod->name); ?></strong></td>
    </tr>
</table>
<br/><br/>
<?php if ($model->products) { ?>
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered">
        <thead>
        <tr>
            <th width="35%" colspan="2" class="text-center">Товар</th>
            <th width="10%" class="text-center">Кол.</th>
            <th width="15%" class="text-center">Цена за шт.</th>
            <th width="20%" class="text-center">Общая стоимость</th>
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

            $newprice = $currency->convert($product->price, $product->currency_id);

            ?>
            <tr>
                <td width="10%" align="center">
                    <?php echo Html::img(Url::to($image, true), ['alt' => $product->originalProduct->name, 'width' => 50, 'height' => 50]); ?></td>
                <td width="40%"><?= $product->originalProduct->name ?></td>
                <td align="center"><?= $product->quantity ?></td>
                <td align="center"><?= $currency->number_format($newprice) ?>
                    <?= $currency->active['symbol'] ?></td>
                <td align="center"><?= $currency->number_format($newprice * $product->quantity) ?>
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

        <?php if($model->delivery_price > 0){ ?>
            <p><?= Yii::t('cart/default','COST_DELIVERY'); ?>:
                <strong><?= $currency->number_format($model->delivery_price); ?> <?= $currency->active['symbol'] ?></strong>
            </p>
        <?php } ?>
        <?= Yii::t('cart/default','TOTAL_PAY'); ?>:
        <h3><?= $currency->number_format($model->total_price); ?>
            <?= $currency->active['symbol'] ?></h3>
    </div>
<?php } ?>


