<?php

use panix\engine\Html;
use yii\helpers\Url;

if (Yii::$app->request->get('image')) {
    $small = false;
    $rowsCount = 5;
    $nums = 2;
    $footnum = 3;
} else {
    $rowsCount = 4;
    $nums = 1;
    $footnum = 2;
    $small = true;
}


foreach ($model as $order) {
    if (isset($order->products)) {
        foreach ($order->products as $item) {
            /** @var $item \panix\mod\cart\models\OrderProduct */
            $original = $item->originalProduct;

            if ($original) {
                $supplier = $original->supplier;
                if ($supplier) {
                    $supplierData = [
                        'id' => ($supplier) ? $supplier->id : null,
                        'name' => ($supplier) ? $supplier->name : null,
                        'address' => ($supplier) ? $supplier->address : null,
                        'phone' => ($supplier) ? $supplier->phone : null
                    ];
                    if ($original->mainImage) {
                        $image = $original->getMainImage('50x50')->url;
                    } else {
                        $image = '/uploads/no-image.png';
                    }
                    //$newprice = ($original->hasDiscount) ? $original->discountPrice : $item->price;

                    $newprice = $item->price;

                    if (Yii::$app->request->get('price') == 1) {
                        if ($item->price_purchase) {
                            //$box = $original->eav_kolicestvo_v_asike;
                            //if (isset($box)) {
                            $newprice = $item->price_purchase;// * $box->value;

                            // }
                        } else {
                            $newprice = 0;
                        }
                    }
                    $box = $original->eav_par_v_asiku;
                    $in_box = 0;
                    if (isset($box)) {
                        $in_box = $newprice / $box->value;
                    }


                    ///$total_price = (Yii::app()->currency->convert($item->price, $item->currency_id) * $in * $item->quantity);
                    $total_price = ($newprice * $item->quantity);

                    $array[$supplierData['id']][] = [
                        'item' => $item,
                        'supplier' => $supplierData,
                        'order_date' => $order->created_at,
                        'order_url' => Url::to($order->getUpdateUrl(), true),
                        'image' => Url::to($image, true),
                        'username' => $order->user_name,
                        // 'price' => $item->prd->price,
                        'price' => $in_box,
                        // 'price' => Yii::app()->currency->convert($item->price,$item->currency_id),
                        'model' => $original,
                        'url' => Url::to($original->getUrl()),
                        // 'title' => $title,
                        //'address'=>$address,
                        'price_total' => $total_price
                    ];

                }
            }
        }
    } else {
        echo 'no find products';
    }
}
?>

<?php
$total_count = 0;
$total_price = 0;
$contact = Yii::$app->settings->get('contacts');
$phones = [];
foreach ($contact->phone as $phone) {
    $phones[] = $phone['number'];
}
//$phones = explode(',', $contact->phone);

foreach ($array as $key => $items) {
    $brand = explode('|', $key);
    ?>

    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th colspan="<?= $rowsCount; ?>" align="center" class="text-center">
                <strong><?= Yii::$app->settings->get('app', 'sitename') ?> (<?= implode(', ', $phones); ?>)</strong>
            </th>
        </tr>
        <tr>
            <th colspan="<?= $rowsCount; ?>" align="center" class="text-center"
                style="background-color:#9b9b9b;color:#fff">
                <strong><?= $items[0]['supplier']['name'] ?> - <?= $items[0]['supplier']['address'] ?>
                    (<?= $items[0]['supplier']['phone'] ?>)</strong>
            </th>
        </tr>
        <tr>
            <th width="5%" align="center" class="text-center">№</th>
            <th width="50%" <?php if (!$small) { ?> colspan="<?= $nums; ?>" <?php } ?> align="center"
                class="text-center"><?= Yii::t('cart/default', 'TABLE_PRODUCT'); ?></th>
            <th width="10%" align="center" class="text-center"><?= Yii::t('cart/default', 'QUANTITY'); ?></th>
            <th width="25%" align="center" class="text-center">Сумма</th>
        </tr>
        <?php
        // \panix\engine\CMS::dump($items[$key]);die;
        // usort($items, [$this->context, "titleSort"]);
        $brand_count = 0;
        $brand_price = 0;
        $num = 0;
        $i = 1;
        foreach ($items as $row) {
            $brand_count += $row['item']->quantity;
            $brand_price += $row['price_total'];
            $num += $row['item']->quantity;
            ?>
            <tr>
                <td align="center"><?= $i ?></td>
                <?php if (!$small) { ?>
                    <td width="10%" align="center">
                        <?= Html::img($row['image'], ['width' => 50, 'height' => 50]); ?>
                    </td>
                <?php } ?>
                <td>
                    <?= $row['item']->name ?><br/>
                    <strong><?= Yii::$app->currency->number_format($row['price']) ?></strong> <?= Yii::$app->currency->active['symbol'] ?>
                    <?php //echo $row['model']->units[$row['model']->unit]; ?>
                    <br/>
                    <?php
                    if ($row['model']->sku) {
                        echo $row['item']->getAttributeLabel('sku') . ': <strong>' . $row['model']->sku . '</strong>; ';
                    }
                    $query = \panix\mod\shop\models\Attribute::find();
                    $query->where(['IN', 'name', array_keys($row['model']->eavAttributes)]);
                    $query->displayOnPdf();
                    $query->sort();
                    $result = $query->all();
                    // print_r($query);
                    $attributes = $row['model']->eavAttributes;
                    foreach ($result as $q) {
                        echo $q->title . ': ';
                        echo '<strong>' . $q->renderValue($attributes[$q->name]) . '</strong>; ';
                    }
                    ?>


                </td>
                <td align="center">
                    <strong><?= $row['item']->quantity ?></strong> <?= $row['model']->units[$row['model']->unit]; ?>
                </td>
                <td align="center">
                    <?php if ($row['price_total']) { ?>
                        <strong><?= Yii::$app->currency->number_format($row['price_total']) ?></strong> <?= Yii::$app->currency->active['symbol'] ?>
                    <?php } else { ?>
                        ---
                    <?php } ?>

                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <td align="left" colspan="<?= $footnum; ?>">

                <strong><?= Yii::$app->request->get('start'); ?></strong>
                по <strong><?= Yii::$app->request->get('end'); ?></strong>
            </td>
            <td align="center">
                <?= Yii::t('cart/default', 'QUANTITY'); ?>: <strong><?= $num ?></strong>
            </td>

            <td align="center">
                И того:
                <strong><?= Yii::$app->currency->number_format($brand_price) ?></strong> <?= Yii::$app->currency->active['symbol'] ?>
            </td>
        </tr>

        </tbody>
    </table>

    <!--  <pagebreak /> добавляем разрыв страницы -->

    <?php
    $total_count += $brand_count;
    $total_price += $brand_price;
}
?>
<br/>

<h3 style="text-align: center">
    <small><?= Yii::t('shop/default', 'PRODUCTS_COUNTER', $total_count); ?> на сумму:
    </small> <?= Yii::$app->currency->number_format($total_price) ?>
    <small><?= Yii::$app->currency->active['symbol'] ?></small>
</h3>
