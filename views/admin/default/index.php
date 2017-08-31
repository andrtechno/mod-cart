<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
?>





<?php

Pjax::begin([
    'id' => 'pjax-container', 'enablePushState' => false,
]);
?>
<?=

yii\grid\GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => $this->render('@app/web/themes/admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]), //'{items}{pager}{summary}'
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'contentOptions' => ['class' => 'text-center']
        ],
        'user_name',
        [
            'attribute' => 'total_price',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function($model) {
        return panix\mod\shop\models\ShopProduct::formatPrice($model->total_price) . ' ' . Yii::$app->currency->main->symbol;
    }
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
?>
<?php Pjax::end(); ?>