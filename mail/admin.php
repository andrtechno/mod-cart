<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;

$currency = Yii::$app->currency->active->symbol;
$thStyle = 'border-color:#D8D8D8; border-width:1px; border-style:solid;';
?>


<?php if ($order->user_name) { ?>
    <p><b><?= $order->getAttributeLabel('user_name') ?>:</b> <?= $order->user_name; ?></p>
<?php } ?>

<?php if ($order->user_phone) { ?>
    <p><b><?= $order->getAttributeLabel('user_phone') ?>:</b> <?= $order->user_phone; ?></p>
<?php } ?>

<?php if ($order->user_email) { ?>
    <p><b><?= $order->getAttributeLabel('user_email') ?>:</b> <?= $order->user_email; ?></p>
<?php } ?>
<?php if ($order->deliveryMethod->name) { ?>
    <p><b><?= $order->getAttributeLabel('delivery_id') ?>:</b> <?= $order->deliveryMethod->name; ?></p>
<?php } ?>
<?php if ($order->paymentMethod->name) { ?>
    <p><b><?= $order->getAttributeLabel('payment_id') ?>:</b> <?= $order->paymentMethod->name; ?></p>
<?php } ?>
<?php if ($order->user_address) { ?>
    <p><b><?= $order->getAttributeLabel('user_address') ?>:</b> <?= $order->user_address; ?></p>
<?php } ?>

<?php if ($order->user_comment) { ?>
    <p><b><?= $order->getAttributeLabel('user_comment') ?>:</b><br/><?= $order->user_comment; ?></p>
<?php } ?>


<table border="0" width="600px" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
    <tr>
        <th colspan="2" style="<?= $thStyle; ?>"><?= Yii::t('cart/default', 'MAIL_TABLE_TH_PRODUCT') ?></th>
        <th style="<?= $thStyle; ?>"><?= Yii::t('cart/default', 'MAIL_TABLE_TH_QUANTITY') ?></th>
        <th style="<?= $thStyle; ?>"><?= Yii::t('cart/default', 'MAIL_TABLE_TH_PRICE_FOR') ?></th>
        <th style="<?= $thStyle; ?>"><?= Yii::t('cart/default', 'MAIL_TABLE_TH_TOTALPRICE') ?></th>
    </tr>
    <?php foreach ($order->products as $row) { ?>

        <tr>
            <td style="<?= $thStyle; ?>" align="center">
                <?php
                    echo Html::img($message->embed(Url::to($row->originalProduct->getMainImageUrl('100x'), true)), ['alt' => $row->originalProduct->name]);

                ?>
            </td>
            <td style="<?= $thStyle; ?>"><?= Html::a($row->originalProduct->name, Url::toRoute($row->originalProduct->getUrl(), true), ['target' => '_blank']); ?></td>
            <td style="<?= $thStyle; ?>" align="center"><?= $row->quantity ?></td>
            <td style="<?= $thStyle; ?>" align="center"><?= Yii::$app->currency->convert($row->originalProduct->price) ?> <?= $currency ?></td>
            <td style="<?= $thStyle; ?>" align="center"><?= Yii::$app->currency->convert($row->originalProduct->price * $row->quantity) ?> <?= $currency ?></td>
        </tr>
    <?php } ?>

</table>
    <p><b>Детали заказа вы можете просмотреть на странице:</b><br/> <?= Html::a(Url::to($order->getUrl(),true),Url::to($order->getUrl(),true),['target'=>'_blank']);?></p>
<br/><br/><br/>
<?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
<h1 style="display:inline"><?= Yii::$app->currency->number_format($order->total_price + $order->delivery_price); ?></h1>
<?= $currency; ?>