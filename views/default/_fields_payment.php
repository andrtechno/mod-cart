<?php
use panix\engine\Html;
?>
<div class="form-group">
    <?= Html::activeLabel($form, 'payment_id', array('required' => true, 'class' => 'info-title control-label')); ?>

    <?php
    foreach ($paymenyMethods as $pay) {
        echo '<div style="margin-left:15px;">';
        echo Html::activeRadio($form, 'payment_id', array(
            'checked' => ($form->payment_id == $pay->id),
            'uncheckValue' => null,
            'value' => $pay->id,
            'data-value' => Html::encode($pay->name),
            'id' => 'payment_id_' . $pay->id,
            'class' => 'payment_checkbox'
        ));
        ?>
        <label for="payment_id_<?= $pay->id ?>"><?= Html::encode($pay->name) ?></label>
        <?php
        echo '</div>';
    }
    ?>
</div>
