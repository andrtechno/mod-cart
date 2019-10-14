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
        <?= \panix\engine\Html::button('Применить!', ['id' => 'submit-promocode', 'class' => 'btn btn-outline-success']); ?>
    </div>
</div>
<div class="help-block" id="promocode-result"></div>

