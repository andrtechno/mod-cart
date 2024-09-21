<?php if ($model) { ?>

    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <thead>

        <tr>
            <th width="5%" align="center" class="text-center">№</th>
			<th width="10%" align="center" class="text-center">№ заказа</th>
            <th width="20%" align="center" class="text-center">ФИО<br></th>
            <th width="35%" align="center" class="text-center">Адрес доставки<br></th>
            <th width="20%" align="center" class="text-center"><?= Yii::t('cart/Order','USER_PHONE'); ?><br></th>
            <th width="10%" align="center" class="text-center">Ящиков<br></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($model as $order) {
			$box=0;
	
			foreach($order->products as $product){
				//print_r($product);die; // * $product['in_box']
				$box += $product['quantity'];
			}
            $array[$order->deliveryMethod->name][] = array(
                'payment' => $order->paymentMethod->name,
                'order' => $order,
                //'productsCount' => count($order->products),
				'productsCount' => $box,
            );
        }
        ?>
        <?php foreach ($array as $delivery_name => $items) { ?>
            <tr>
                <th colspan="6" align="center" class="text-center" style="background-color:#9b9b9b;color:#fff">
                    <?= $delivery_name ?>
                </th>
            </tr>
            <?php
            $i = 1;
            foreach ($items as $row) {
                ?>
                <tr>
                    <td align="center" style="vertical-align:middle"><?= $i ?></td>
					<td align="center" style="vertical-align:middle"><?= $row['order']->id; ?></td>
                    <td>
                        <?= $row['order']->user_name; ?> <?= $row['order']->user_lastname; ?>
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
