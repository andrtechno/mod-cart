<?php
/**
 * @var \panix\mod\cart\models\Order $model
 */
$history = $model->getHistory();

if (empty($history)) {

    echo \panix\engine\bootstrap\Alert::widget([
        'options' => ['class' => 'alert-info'],
        'body' => Yii::t('cart/admin', 'HISTORY_EMPTY')
    ]);

    return;
}
?>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th></th>
        <th><?= Yii::t('cart/admin', 'BEFORE'); ?></th>
        <th><?= Yii::t('cart/admin', 'AFTER'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($history as $row) {
        echo $this->render('_' . $row->handler, [
            'data' => $row,
        ]);
    }
    ?>
    </tbody>
</table>