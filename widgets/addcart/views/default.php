<?php

echo Html::form(array('/cart/add'), 'post', array('class' => 'product-form2', 'id' => 'form-add-cart' . $data->id));
echo Html::hiddenField('product_id', $data->id);
echo Html::hiddenField('product_price', $data->price);
echo Html::hiddenField('use_configurations', $data->use_configurations);
echo Html::hiddenField('currency_rate', Yii::app()->currency->active->rate);
echo Html::hiddenField('currency_id', $data->currency_id);
echo Html::hiddenField('supplier_id', $data->supplier_id);
echo Html::hiddenField('pcs', $data->pcs);
echo Html::hiddenField('configurable_id', 0);

if($conf){
Yii::app()->controller->renderPartial('_configurations', array('model' => $data));
}
if ($spinner) {
    ?>


    <?php
    echo Html::textField('quantity', 1, array('class' => 'spinner btn-group form-control'));
}
if ($data->isAvailable) {
    echo Html::link(Yii::t('CartModule.default', 'BUY'), 'javascript:cart.add(' . $data->id . ')', array('class' => 'btn btn-success text-uppercase'));
} else {
    echo Html::link(Yii::t('CartModule.default', 'NOT_AVAILABLE'), 'javascript:cart.notifier(' . $data->id . ');');
}
echo Html::endForm();
?>