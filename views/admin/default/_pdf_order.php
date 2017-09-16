<?php

use yii\helpers\Html;
use yii\helpers\Url;
use panix\mod\shop\models\Product;
 
 ?>
<table border="0" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
    
    <thead>
                        <tr>
                <th colspan="2" align="center" class="text-center"><?= Yii::$app->settings->get('app', 'site_name') ?><br/><?= Yii::$app->user->getDisplayName()?><br/><?php //echo $date;  ?> </th>
            </tr> 
        <tr><th colspan="2" align="center"><h1>№ заказа <?= $model->id ?></h1></th></tr>
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
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="50%" colspan="2" align="center" class="text-center">Товар</th>

                <th width="10%" align="center" class="text-center">Кол.</th>

                <th width="10%" align="center" class="text-center">Цена за шт.</th>
                <th width="10%" align="center" class="text-center">Общая цена</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($model->products as $product) {
                if ($product->originalProduct->image) {
                    $image = $product->originalProduct->getMainImageUrl('50x50');
                } else {
                    $image = '/uploads/no-image.png';
                }

                $newprice = $product->price;
                ?>
                <tr>
                    <td width="10%" align="center"><?= Html::img(Url::to($image,true), ['alt'=>$product->originalProduct->name,'width' => 50, 'height' => 50]); ?></td>
                    <td width="40%"><?= $product->originalProduct->name ?></td>

                    <td width="10%" align="center"><?= $product->quantity ?></td>

                    <td width="10%" align="center"><?= Product::formatPrice($newprice) ?> <?= Yii::$app->currency->active->symbol ?></td>
                    <td width="10%" align="center"><?= Product::formatPrice($newprice * $product->quantity) ?> <?= Yii::$app->currency->active->symbol ?></td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
<br/><br/>
Всего к оплате: <h1><?= $model->total_price; ?> <?= Yii::$app->currency->active->symbol ?></h1>
<?php } ?>         


<div class="alert alert-info">dsadsadsa</div>