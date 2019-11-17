<?php
use panix\mod\cart\widgets\promocode\PromoCodeInput;

?>


<div class="input-group">
    <?php
    echo PromoCodeInput::widget([
        'model' => $this->context->model,
        'attribute' => $this->context->attribute,
        'options' => [
            'placeholder' => 'Введите промо-код'
        ]
    ]);
    ?>

    <div class="input-group-append">
        <?php if($this->context->model instanceof \panix\mod\cart\models\forms\OrderCreateForm){ ?>
        <?= \panix\engine\Html::button('Применить!', ['id' => 'submit-promocode', 'class' => 'btn btn-outline-success']); ?>
        <?php }else{ ?>
            <?= \panix\engine\Html::submitButton('Применить!', ['id' => 'submit-promocode', 'class' => 'btn btn-outline-success']); ?>
        <?php } ?>
    </div>
</div>

<div class="help-block" id="promocode-result"></div>

