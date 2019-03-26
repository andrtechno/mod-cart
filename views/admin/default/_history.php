<?php
$history = $model->getHistory();

if (empty($history)) {

    //echo Yii::app->tpl->alert('info', Yii::t('CartModule.admin', 'HISTORY_EMPTY'), false);
    echo Yii::t('cart/admin', 'HISTORY_EMPTY');
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
            echo $this->render('_' . $row->handler, array(
                'data' => $row,
            ));
        }
        ?>
    </tbody>
</table>