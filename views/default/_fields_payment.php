<?php

use panix\engine\Html;
?>
<div class="form-group">

    <?php
    foreach ($paymenyMethods as $pay) {
        echo '<div>';
        echo Html::activeRadio($form, 'payment_id', [
            'label' => $pay->name,
            'checked' => ($form->payment_id == $pay->id),
            'uncheck' => false,
            'value' => $pay->id,
            'data-value' => Html::encode($pay->name),
            //'id' => 'payment_id_' . $pay->id,
            'class' => 'payment_checkbox'
        ]);
        echo '</div>';
    }
    ?>
</div>
