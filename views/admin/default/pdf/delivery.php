<?php if ($model) { ?>

    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <thead>

            <tr>
                <th width="5%" align="center" class="text-center">№</th>
                <th width="20%" align="center" class="text-center">ФИО<br></th>
                <th width="45%" align="center" class="text-center">Адрес доставки<br></th>
                <th width="20%" align="center" class="text-center">Телефон<br></th>
                <th width="10%" align="center" class="text-center">товаров<br></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($model as $order) {
                $array[$order->deliveryMethod->name][] = array(
                    'payment' => $order->paymentMethod->name,
                    'city' => $order->user_address,
                    'user_phone' => $order->user_phone,
                    'user_name' => $order->user_name,
                    'user_address'=>$order->user_address,
                    'productsCount' => count($order->products),
                );
            }
            ?>
            <?php foreach ($array as $delivery_name => $items) { ?>
                <tr>
                    <th colspan="5" align="center" class="text-center" style="background-color:#9b9b9b;color:#fff"><b><?= $delivery_name ?></b><br/></th>
                </tr>
                <?php
                $i = 1;
                foreach ($items as $row) {
                    ?>
                    <tr>
                        <td align="center" style="vertical-align:middle"><?= $i ?></td>
                        <td>
                            <?= $row['user_name'] ?>
                            <p>Оплата: <?= $row['payment'] ?></p>
                        </td>
                        <td align="center" style="vertical-align:middle"><?= $row['city'] ?><?= $row['user_address'] ?></td>
                        <td align="center" style="vertical-align:middle"><?= \panix\engine\CMS::phone_number_format($row['user_phone']) ?></td>
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
