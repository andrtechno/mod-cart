<?php

use yii\helpers\Html;

use panix\ext\fancybox\Fancybox;
?>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->context->pageName) ?></h3>
            </div>
            <div class="panel-body">

                <?= $this->render('_form', ['model' => $model]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <?= Fancybox::widget(['target' => '.image a']); ?>
        <?php

        echo Html::a('add', 'javascript:openAddProductDialog(' . $model->id . ');',['class'=>'btn btn-success']);
        if (!$model->isNewRecord) {
            ?>

            <div id="dialog-modal" style="display: none;" title="<?php echo Yii::t('cart/admin', 'CREATE_PRODUCT') ?>">
                <?php
                echo $this->render('_addProduct', array(
                    'model' => $model,
                ));
                ?>
            </div>
            <div id="orderedProducts">
                <?php
                if (!$model->isNewRecord) {
                    echo $this->render('_orderedProducts', array(
                        'model' => $model,
                    ));
                }
                ?>
            </div>

        <?php } ?>



    </div>
</div>

<?php

echo $this->render('_history', array(
    'model' => $model,
));


