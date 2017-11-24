<?php

use yii\helpers\Html;
use panix\engine\widgets\Pjax;
use panix\engine\grid\GridView;
?>





<?php

Pjax::begin([
    'id' => 'pjax-container', 'enablePushState' => false,
]);
?>
<?=

GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layoutOptions' => ['title' => $this->context->pageName], //'{items}{pager}{summary}'
    'columns' => [
        'email',
        [
            'attribute' => 'name',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center image'],
            'value' => function($model) {
                return Html::a($model->product->name, $model->product->getUrl()); //$model->renderGridImage('50x50');
            },
        ],
        [
            'attribute' => 'product.availability',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function($model) {
                return $model->product->availabilityItems[$model->product->availability]; //$model->renderGridImage('50x50');
            },
        ],
        [
            'attribute' => 'product.quantity',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => 'product.quantity',
        ],
        [
            'attribute' => 'totalEmails',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
           // 'attribute' => 'test',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center', 'data-confirm' => Yii::t('cart/default', 'Вы уверены?')],
            'value' => function($model) {
                return Html::a('Отправить письмо', ["shop/admin/notify/send", "product_id" => $model->product_id]);
            },
        ],
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
?>
<?php Pjax::end(); ?>