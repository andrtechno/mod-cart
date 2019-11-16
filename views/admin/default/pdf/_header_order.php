<?php
use panix\engine\Html;
use panix\engine\CMS;

?>
<table width="100%">
    <tr>
        <td width="10%">
            <?= Html::img('/uploads/pres-logo.png', ['width' => 50]); ?>
        </td>
        <td width="50%">
            <h1>№<?= CMS::idToNumber($model->id); ?></h1>
        </td>
        <td width="40%" style="text-align: right"><?= $model->getAttributeLabel('created_at'); ?>:
            <strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong>
        </td>
    </tr>
</table>
