<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use panix\engine\grid\GridView;
?>





<?php

Pjax::begin([
    'id' => 'pjax-container', 'enablePushState' => false,
]);
?>
<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => $this->render('@admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]), //'{items}{pager}{summary}'
    'columns' => [
        'name',
        'color',
        [
            'class' => 'panix\engine\grid\columns\ActionColumn',
            'template' => '{update} {switch} {delete}',
        ]
    ]
]);
?>
<?php Pjax::end(); ?>