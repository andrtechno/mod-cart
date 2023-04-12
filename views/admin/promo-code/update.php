<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use panix\mod\shop\models\Brand;
use panix\mod\shop\models\Category;

$form = ActiveForm::begin();
print_r($model->categories);
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>

    <div class="card-body">
        <?= $form->field($model, 'code')->textInput(['maxlength' => 50]); ?>
        <?= $form->field($model, 'discount')->textInput(['maxlength' => 50]); ?>
        <?= $form->field($model, 'max_use'); ?>
        <?= $form->field($model, 'categories')
            ->dropDownList(ArrayHelper::map(Category::find()->all(), 'id', 'name'), [
                //'prompt' => 'Укажите производителя',
                'multiple' => 'multiple'
            ]);
        ?>
        <?= $form->field($model, 'brands')
            ->dropDownList(ArrayHelper::map(Brand::find()->all(), 'id', 'name'), [
                //  'prompt' => 'Укажите производителя',
                'multiple' => 'multiple'
            ]);
        ?>
    </div>
    <div class="card-footer text-center">
        <?= $model->submitButton(); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
