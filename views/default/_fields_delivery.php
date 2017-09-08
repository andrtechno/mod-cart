<?php

use panix\engine\Html;
?>
<?php if ($deliveryMethods) { ?>
    <div class="form-group">

        <?php
        //  echo Html::activeRadioList($form, 'delivery_id', \yii\helpers\ArrayHelper::map($deliveryMethods, 'id', 'name'));
        foreach ($deliveryMethods as $delivery) {
            echo '<div>';

            echo Html::activeRadio($form, 'delivery_id', [
                'label' => $delivery->name,
                'uncheck' => false,
                'checked' => ($form->delivery_id == $delivery->id),
                'value' => $delivery->id,
                'data-price' => Yii::$app->currency->convert($delivery->price),
                'data-free-from' => Yii::$app->currency->convert($delivery->free_from),
                'onClick' => 'cart.recountTotalPrice(this); ',
                'data-value' => Html::encode($delivery->name),
                //'id' => 'delivery_id_' . $delivery->id,
                'class' => 'delivery_checkbox'
            ]);
            ?>

            <?php
            if (!empty($delivery->description)) {
                ?><p><?= $delivery->description ?></p>
                <?php
            }
            ?>
            <?php
            echo '</div>';
        }
        ?>
        <?= Html::error($form, 'delivery_id', ['class' => 'error']); ?>
    </div>
        <?php
    } else {
        echo 'Необходимо добавить способ доставки!';
    }
    ?>
<div class="form-group">
<?= Html::activeLabel($form, 'user_address', array('required' => true, 'class' => 'control-label')); ?>
    <?= Html::activeTextInput($form, 'user_address', array('class' => 'form-control')); ?>
</div>


