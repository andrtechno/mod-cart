<?php

Yii::app()->tpl->openWidget(array(
    'title' => $this->pageName,
    'htmlOptions'=>array('class'=>'fluid')
));
echo $model->getForm();
Yii::app()->tpl->closeWidget();
?>
