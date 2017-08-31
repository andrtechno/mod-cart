<?php
use panix\engine\Html;
?>

<div class="form-group">
    <?= Html::activeLabel($form, 'delivery_id', array('required' => true, 'class' => 'info-title control-label')); ?>
    <?php
    foreach ($deliveryMethods as $delivery) {
        echo '<div style="margin-left:15px;">';
        echo Html::activeRadio($form, 'delivery_id', array(
            'checked' => ($form->delivery_id == $delivery->id),
            'uncheckValue' => null,
            'value' => $delivery->id,
            'data-price' => Yii::$app->currency->convert($delivery->price),
            'data-free-from' => Yii::$app->currency->convert($delivery->free_from),
            'onClick' => 'cart.recountTotalPrice(this); ',
            'data-value' => Html::encode($delivery->name),
            'id' => 'delivery_id_' . $delivery->id,
            'class' => 'delivery_checkbox'
        ));
        if (!empty($delivery->description)) {
            ?><p><?= $delivery->description ?></p>
            <?php
        } ?>
        <label for="delivery_id_<?= $delivery->id ?>"><?= Html::encode($delivery->name) ?></label>
        <?php
        echo '</div>';
    }
    ?>
</div>




