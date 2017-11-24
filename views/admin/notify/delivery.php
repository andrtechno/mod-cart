<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use panix\engine\grid\GridView;
?>

<?= Html::a(Yii::t('app', 'SEND'), array('/admin/shop/notify/deliverySend'), array('class' => 'btn btn-success btn-sm')) ?>
        
        
        
<?php

Pjax::begin([
    'id' => 'pjax-container', 'enablePushState' => false,
]);
?>
<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName], //'{items}{pager}{summary}'
    'columns' => [
        'email',
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
?>
<?php Pjax::end(); ?>
        <?php
        

        
        
/*
        $this->widget('ext.adminList.GridView', array(
            'dataProvider' => $dataProvider,
            'selectableRows' => false,
            'autoColumns' => false,
            'enableHeader' => false,
            'columns' => array(
                array(
                    'type' => 'html',
                    'name' => 'image',
                    'htmlOptions' => array('class' => 'text-center image'),
                    'value' => 'Html::link(Html::image($data->getMainImageUrl("50x50"),$data->name))',
                ),
                array(
                    'name' => 'name',
                    'type' => 'raw',
                    'value' => 'Html::link(Html::encode($data->name), array("/shop/admin/products/update", "id"=>$data->id))',
                ),
                'price',
                array(
                    'name' => 'manufacturer_id',
                    'type' => 'raw',
                    'value' => '$data->manufacturer ? Html::encode($data->manufacturer->name) : ""',
                    'filter' => Html::listData(ShopManufacturer::model()->orderByName()->findAll(), 'id', 'name')
                ),
                array(
                    'name' => 'supplier_id',
                    'type' => 'raw',
                    'value' => '$data->supplier_id ? Html::encode($data->supplier->name) : ""',
                    'filter' => Html::listData(ShopSuppliers::model()->findAll(), 'id', 'name')
                ),
                array(
                    //'name' => 'categories',
                    'type' => 'raw',
                    'header' => 'Категория/и',
                    'htmlOptions' => array('style' => 'width:100px'),
                    'value' => '$data->getCategories()',
                    'filter' => false
                ),
            ),
        ));*/
        ?>

