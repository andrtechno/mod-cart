<?php

use panix\engine\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;

$currency = Yii::$app->currency->active->symbol;
$thStyle = 'border-color:#D8D8D8; border-width:1px; border-style:solid;';
?>


<?php if ($order->user_name) { ?>
    <p><strong><?= $order->getAttributeLabel('user_name') ?>:</strong> <?= $order->user_name; ?></p>
<?php } ?>

<?php if ($order->user_phone) { ?>
    <p><strong><?= $order->getAttributeLabel('user_phone') ?>:</strong> <?= Html::tel($order->user_phone); ?></p>
<?php } ?>

<?php if ($order->user_email) { ?>
    <p><strong><?= $order->getAttributeLabel('user_email') ?>:</strong> <?= $order->user_email; ?></p>
<?php } ?>
<?php if ($order->deliveryMethod->name) { ?>
    <p><strong><?= $order->getAttributeLabel('delivery_id') ?>:</strong> <?= $order->deliveryMethod->name; ?></p>
<?php } ?>
<?php if ($order->paymentMethod->name) { ?>
    <p><strong><?= $order->getAttributeLabel('payment_id') ?>:</strong> <?= $order->paymentMethod->name; ?></p>
<?php } ?>
<?php if ($order->user_address) { ?>
    <p><strong><?= $order->getAttributeLabel('user_address') ?>:</strong> <?= $order->user_address; ?></p>
<?php } ?>




<table border="0" width="100%" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
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

                    echo Html::img(Url::to($row->originalProduct->getMainImage('100x')->url,true), ['alt' => $row->originalProduct->name]);

                ?>
            </td>
            <td style="<?= $thStyle; ?>">
                <?= Html::a($row->name, Url::toRoute($row->originalProduct->getUrl(), true), ['target' => '_blank']); ?>

                <?php

                // Display variant options
                if (!empty($row->variants)) {
                    $variants = unserialize($row->variants);
                    echo '<br/>'.Html::beginTag('small');
                    foreach ($variants as $name=>$variant)
                        echo ' - ' . $name . ': <strong>' . $variant . '</strong><br/>';
                    echo Html::endTag('small');
                }
                ?>

            </td>
            <td style="<?= $thStyle; ?>" align="center"><?= $row->quantity ?></td>
            <td style="<?= $thStyle; ?>" align="center"><strong><?= Yii::$app->currency->convert($row->price) ?></strong> <sup><?= $currency ?></sup></td>
            <td style="<?= $thStyle; ?>" align="center"><strong><?= Yii::$app->currency->convert($row->price * $row->quantity) ?></strong><sup> <?= $currency ?></sup></td>
        </tr>
    <?php } ?>

</table>


<?php if ($order->user_comment) { ?>
    <br/>
    <p><strong><?= $order->getAttributeLabel('user_comment') ?>:</strong><br/><?= $order->user_comment; ?></p>
    <hr/>
<?php } ?>

<a href="#" class="btn">dasd</a>
    <p><strong>Детали заказа вы можете просмотреть на странице:</strong><br/> <?= Html::a(Url::to($order->getUrl(),true),Url::to($order->getUrl(),true),['target'=>'_blank']);?></p>
<br/><br/>



<?= Yii::t('cart/default', 'TOTAL_PAY') ?>:
<h1 style="display:inline"><?= Yii::$app->currency->number_format($order->total_price + $order->delivery_price); ?> <sup><?= $currency; ?></sup></h1>
