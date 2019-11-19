<?php
use panix\engine\Html;
use yii\helpers\Url;

if ($_GET['image']) {
    $small = false;
    $nums = 6;
    $footnum = 3;
} else {
    $nums = 5;
    $footnum = 2;
    $small = true;
}


foreach ($model as $order) {
    if (isset($order->products)) {
        foreach ($order->products as $item) {

            if (isset($item->originalProduct)) {
                if (isset($item->originalProduct->manufacturer)) {
                    $manufacturer = (isset($item->originalProduct->manufacturer)) ? $item->originalProduct->manufacturer->name : null;
                    // $attrs = $attributes->getData($item->prd);
                    if ($item->originalProduct->mainImage) {
                        $image = $item->originalProduct->getMainImage('50x50')->url;
                    } else {
                        $image = '/uploads/no-image.png';

                    }

                    $newprice = ($item->originalProduct->appliedDiscount) ? $item->originalProduct->discountPrice : $item->price;


                    ///$total_price = (Yii::app()->currency->convert($item->price, $item->currency_id) * $in * $item->quantity);
                    $total_price = ($newprice * $item->quantity);
                    $array[$manufacturer][] = array(

                        'order_id' => $item->order_id,
                        'order_date' => $order->created_at,
                        'order_url' => Url::to($order->getUpdateUrl(), true),
                        'image' => Url::to($image, true),
                        'username' => $order->user_name,
                        // 'price' => $item->prd->price,
                        'price' => $newprice,
                        // 'price' => Yii::app()->currency->convert($item->price,$item->currency_id),
                        'name' => $item->originalProduct->name,
                        'product_id' => $item->product_id,
                        //'size' => $attrs->size->value,
                        'size' => $item->originalProduct->eav_size,
                        'url' => Url::to($item->originalProduct->getUrl()),
                        'manufacturer' => $manufacturer,
                        'quantity' => $item->quantity,


                        'price_total' => $total_price
                    );
                } else {

                }
            } else {
                //  Yii::log('productID '.$item->id,'info','application');
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
//$contact = Yii::$app->settings->get('contacts');
//$phones = explode(',', $contact['phone']);

foreach ($array as $key => $items) {
    $brand = explode('|', $key);
    ?>

    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th colspan="<?= $nums; ?>" align="center" class="text-center" style="background-color:#9b9b9b;color:#fff">
                <strong><?= $key ?></strong>
            </th>
        </tr>
        <tr>
            <th width="5%" align="center" class="text-center">№</th>
            <?php if ($small) { ?>
                <th width="25%" align="center" class="text-center">Товара</th>
            <?php } else { ?>
                <th colspan="2" width="35%" align="center" class="text-center">Товара</th>
            <?php } ?>
            <th width="10%" align="center" class="text-center">Кол.</th>
            <th width="10%" align="center" class="text-center">Цена за пару</th>
            <th width="10%" align="center" class="text-center">Сумма</th>
        </tr>
        <?php
        usort($items, array($this->context, "manufacturerSort"));
        $brend_count = 0;
        $brend_price = 0;
        $num = 0;
        $i = 1;
        foreach ($items as $row) {
            $brend_count += $row['quantity'];
            $brend_price += $row['price_total'];
            $num += $row['quantity'];
            ?>
            <tr>
                <td align="center"><?= $i ?></td>
                <?php if ($small) { ?>
                    <td><?= $row['name'] ?> (<?= $row['size'] ?>)</td>
                <?php } else { ?>
                    <td width="10%" align="center">
                        <?= Html::img($row['image'], ['width' => 50, 'height' => 50]); ?>
                    </td>
                <?php } ?>
                <td align="center"><?= $row['quantity'] ?></td>
                <td align="center"><?= Yii::$app->currency->number_format($row['price']) ?> <?= Yii::$app->currency->active['symbol'] ?></td>
                <td align="center"><?= Yii::$app->currency->number_format($row['price_total']) ?> <?= Yii::$app->currency->active['symbol'] ?></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <td align="center" colspan="<?= $footnum ?>">
                Всего товаров: <strong><?= $brend_count ?></strong></td>
            <td width="10%" align="center">
                кол. товаров: <strong><?= $num ?></strong>
            </td>
            <td width="10%" align="center"></td>
            <td width="10%" align="center">
                И того:
                <strong><?= Yii::$app->currency->number_format($brend_price) ?></strong> <?= Yii::$app->currency->active['symbol'] ?>
            </td>
        </tr>
        </tbody>
    </table>
    <!--  <pagebreak /> добавляем разрыв страницы -->

    <?php
    $total_count += $brend_count;
    $total_price += $brend_price;
}
?>
<br/>

<h3 style="text-align: center">
    <small><?= Yii::t('shop/default', 'PRODUCTS_COUNTER', $total_count); ?> на сумму:</small> <?= Yii::$app->currency->number_format($total_price) ?>
    <small><?= Yii::$app->currency->active['symbol'] ?></small>
</h3>
