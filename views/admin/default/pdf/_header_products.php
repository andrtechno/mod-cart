<?php
use panix\engine\Html;

?>
<table width="100%">
    <tr>
        <td width="70%">
            <h2><?= Yii::$app->settings->get('app','sitename'); ?></h2>
        </td>
        <td width="30%" style="text-align: right"><strong><?= Yii::t('cart/admin','OVER_PERIOD'); ?>:</strong><br/>
            <p>c <strong><?= $start_date; ?></strong></p>
            <p>по <strong><?= $end_date; ?></strong></p>
        </td>
    </tr>
</table>
