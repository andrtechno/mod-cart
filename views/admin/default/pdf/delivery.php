<?php if ($model) { ?>

    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <thead>

        <tr>
            <th width="5%" align="center" class="text-center">№</th>
            <th width="20%" align="center" class="text-center">ФИО<br></th>
            <th width="45%" align="center" class="text-center">Адрес доставки<br></th>
            <th width="20%" align="center" class="text-center"><?= Yii::t('cart/Order','USER_PHONE'); ?><br></th>
            <th width="10%" align="center" class="text-center">Товаров<br></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($model as $order) {
            $array[$order->deliveryMethod->name][] = array(
                'payment' => $order->paymentMethod->name,
                'order' => $order,
                'productsCount' => count($order->products),
            );
        }
        ?>
        <?php foreach ($array as $delivery_name => $items) { ?>
            <tr>
                <th colspan="5" align="center" class="text-center" style="background-color:#9b9b9b;color:#fff">
                    <?= $delivery_name ?>
                </th>
            </tr>
            <?php
            $i = 1;
            foreach ($items as $row) {
                ?>
                <tr>
                    <td align="center" style="vertical-align:middle"><?= $i ?></td>
                    <td>
                        <?= $row['order']->user_name; ?>
                        <p><strong><?= Yii::t('cart/default', 'PAYMENT'); ?>:</strong> <?= $row['payment'] ?></p>
                    </td>
                    <td style="vertical-align:middle">
                        <?php foreach ($row['order']->getDeliveryEach() as $delivery) { ?>
                            <p><?= $delivery['key']; ?>: <strong><?= $delivery['value']; ?></strong></p>
                        <?php } ?>
                    </td>
                    <td align="center"
                        style="vertical-align:middle"><?= \panix\engine\CMS::phone_format($row['order']->user_phone) ?></td>
                    <td align="center" style="vertical-align:middle"><?= $row['productsCount'] ?></td>
                </tr>
                <?php
                $i++;
            }
        }
        ?>
        </tbody>
    </table>
<?php } else { ?>
    <center><?php echo Yii::t('app/default', 'NO_INFO'); ?></center>
<?php } ?>
