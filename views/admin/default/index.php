<?php

use yii\helpers\Html;
use panix\engine\grid\AdminGridView;

$user = Yii::$app->getModule("shop")->model("ShopProduct");
$manufacturer = Yii::$app->getModule("shop")->model("ShopManufacturer");



//$this->title = Yii::t('user/default', 'Users');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?php \yii\widgets\Pjax::begin(); ?>
    <?=
    // yii\grid\GridView
    AdminGridView::widget([
        'tableOptions' => ['class' => 'table table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => $this->render('@app/web/themes/admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'email:email',
            // 'profile.full_name',
 
            [
                'attribute' => 'manufacturer_id',
                'label' => Yii::t('shop/default', 'MANUFACTURER_ID'),
                'filter' => $manufacturer::dropdown(),
                'value' => function($model, $index, $dataColumn) use ($manufacturer) {
                    $dropdown = $manufacturer::dropdown();
                    return $dropdown[$model->manufacturer_id];
                },
            ],

            [
                'attribute' => 'name',
                'format' => 'html',
               // 'filter'=>Html::textInput($searchModel->name),
                'filter'=>true,
                'label' => Yii::t('shop/default', 'Status'),
                'value' => function($data) {
                    return Html::a($data->name, $data->getUrl());
                },
            ],
            // 'new_email:email',
             'date_create',
            // 'password',
            // 'auth_key',
            // 'api_key',
            // 'login_ip',
            // 'login_time',
            // 'create_ip',
            // 'create_time',
            // 'update_time',
            // 'ban_time',
            // 'ban_reason',

            ['class' => 'panix\engine\grid\columns\ActionColumn']
            ],
    ]);
    ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
