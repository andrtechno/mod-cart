<?php

echo Html::form(array('/cart/add'), 'post', array('class' => 'product-form2', 'id' => 'form-add-cart' . $this->data->id));
echo Html::hiddenField('product_id', $this->data->id);
echo Html::hiddenField('product_price', $this->data->price);
echo Html::hiddenField('use_configurations', $this->data->use_configurations);
echo Html::hiddenField('currency_rate', Yii::app()->currency->active->rate);
echo Html::hiddenField('currency_id', $this->data->currency_id);
echo Html::hiddenField('supplier_id', $this->data->supplier_id);
echo Html::hiddenField('pcs', $this->data->pcs);
echo Html::hiddenField('configurable_id', 0);


if ($this->spinner) {
    ?>


    <?php

    echo Html::textField('quantity', 1, array('class' => 'spinner btn-group form-control'));
}
if ($this->data->isAvailable) {
    echo Html::link('<i class="fa fa-shopping-cart inner-right-vs"></i>'.Yii::t('CartModule.default', 'BUY'), 'javascript:cart.add(' . $this->data->id . ')', array('class' => 'btn btn-primary'));
} else {
    echo Html::link(Yii::t('CartModule.default', 'NOT_AVAILABLE'), 'javascript:cart.notifier(' . $this->data->id . ');');
}
echo Html::endForm();
?>