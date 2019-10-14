<?php
use panix\engine\Html;

?>
<table width="100%">
    <tr>
        <td width="10%">
            <?= Html::img('/uploads/pres-logo.png', ['width' => 50]); ?>
        </td>
        <td width="60%">
            <h1><?= Yii::$app->name; ?></h1>
        </td>
        <td width="30%" style="text-align: right"><strong>Доставка за период:</strong><br/>
            <p>c <strong><?= $start_date; ?></strong></p>
            <p>по <strong><?= $end_date; ?></strong></p>
        </td>
    </tr>
</table>
