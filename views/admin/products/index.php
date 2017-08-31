<?php

//use yii\helpers\Html;
use panix\engine\grid\AdminGridView;
use yii\widgets\Pjax;
?>
<div class="user-index">
<?php Pjax::begin(); ?>
    <?=
    // yii\grid\GridView
    AdminGridView::widget([
        'tableOptions' => ['class' => 'table table-striped'],
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'layout' => $this->render('@app/web/themes/admin/views/layouts/_grid_layout', ['title' => $this->context->pageName]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
          [
                'label' => 'Цена',
                'value' => 'price'
            ],
            /*  [
              'attribute' => 'role_id',
              'label' => Yii::t('user/default', 'Role'),
              'filter' => $role::dropdown(),
              'value' => function($model, $index, $dataColumn) use ($role) {
              $roleDropdown = $role::dropdown();
              return $roleDropdown[$model->role_id];
              },
              ],
              [
              'attribute' => 'status',
              'label' => Yii::t('user/default', 'Status'),
              'filter' => $user::statusDropdown(),
              'value' => function($model, $index, $dataColumn) use ($user) {
              $statusDropdown = $user::statusDropdown();
              return $statusDropdown[$model->status];
              },
              ],
              'email:email',
              'profile.full_name',
              'create_time', */


            ['class' => 'panix\engine\grid\columns\ActionColumn']
            ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
