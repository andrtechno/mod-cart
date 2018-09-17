<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;
?>
<img src="https://corner-cms.com/logo.php?size=100x50&color=333&type=logo_cms" />
<table border="0" cellspacing="0" cellpadding="2" style="width:100%;" class="">

    <thead>
        <tr><th colspan="2" align="center"><h2>№ заказа <?= $model->id ?></h2></th></tr>
        <tr>
            <td width="70%" align="right" class="text-center"><?= $model->getAttributeLabel('user_name'); ?>:</td>
            <td width="30%" align="right" class="text-center"><?= $model->user_name; ?></td>
        </tr>
        <tr>
            <td width="70%" align="right" class="text-center"><?= $model->getAttributeLabel('user_phone'); ?>:</td>
            <td width="30%" align="right" class="text-center"><?= $model->user_phone; ?></td>
        </tr>
        <tr>
            <td width="70%" align="right" class="text-center"><?= $model->getAttributeLabel('user_address'); ?>:</td>
            <td width="30%" align="right" class="text-center"><?= $model->user_address; ?></td>
        </tr>
        <tr>
            <td width="70%" align="right" class="text-center"><?= $model->getAttributeLabel('delivery_id'); ?>:</td>
            <td width="30%" align="right" class="text-center"><?= $model->deliveryMethod->name; ?></td>
        </tr>
        <tr>
            <td width="70%" align="right" class="text-center"><?= $model->getAttributeLabel('payment_id'); ?>:</td>
            <td width="30%" align="right" class="text-center"><?= $model->paymentMethod->name; ?></td>
        </tr>
    </thead>
    <tbody>

    </tbody>
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
                $totalCountQuantity += $product->quantity;
                $totalCountPrice += $product->price;
                $totalCountPriceAll += $product->price * $product->quantity;
                //if ($product->originalProduct) {
                    $image = $product->originalProduct->getMainImageUrl('50x50');
                //} else {
                //    $image = '/uploads/no-image.png';
               // }

                $newprice = Yii::$app->currency->convert($product->price);
                ?>
                <tr>
                    <td width="10%" align="center"><?= Html::img(Url::to($image, true), ['alt' => $product->originalProduct->name, 'width' => 50, 'height' => 50]); ?></td>
                    <td width="40%"><?= $product->originalProduct->name ?></td>
                    <td align="center"><?= $product->quantity ?></td>
                    <td align="center"><?= Yii::$app->currency->number_format($newprice) ?> <sup><?= Yii::$app->currency->active->symbol ?></sup></td>
                    <td align="center"><?= Yii::$app->currency->number_format($newprice * $product->quantity) ?> <sup><?= Yii::$app->currency->active->symbol ?></sup></td>
                </tr>
            <?php } ?>

        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Всего</th>
                <th class="text-center"><?= $totalCountQuantity; ?></th>
                <th class="text-center"><?= Yii::$app->currency->number_format(Yii::$app->currency->convert($totalCountPrice)); ?> <sup><?= Yii::$app->currency->active->symbol ?></sup></th>
                <th class="text-center"><?= Yii::$app->currency->number_format(Yii::$app->currency->convert($totalCountPriceAll)); ?> <sup><?= Yii::$app->currency->active->symbol ?></sup></th>
            </tr>
        </tfoot>
    </table>
    <br/><br/>
    <div class="text-right">
        Всего к оплате: <h1><?= Yii::$app->currency->number_format($model->total_price); ?> <sup><?= Yii::$app->currency->active->symbol ?></sup></h1>
    </div>
<?php } ?>         


