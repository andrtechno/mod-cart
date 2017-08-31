<?php

$this->widget('ext.adminList.GridView', array(
    'dataProvider' => $dataProvider,
    'selectableRows' => false,
    'enableHeader' => true,
    'name' => $this->pageName,
    'filter' => $model,
));

?>
