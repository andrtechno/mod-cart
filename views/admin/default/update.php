<?php

use yii\helpers\Html;

use panix\ext\fancybox\Fancybox;

?>
<div class="row">
    <div class="col-sm-6">
        <div class="card bg-light">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">

                <?= $this->render('_form', ['model' => $model]) ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <?= Fancybox::widget(['target' => '.image a']); ?>
        <?php

        //echo Html::a('add', 'javascript:openAddProductDialog(' . $model->id . ');', ['class' => 'btn btn-success']);
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
                    echo $this->render('_order-products', ['model' => $model]);
                }
                ?>
            </div>

        <?php } ?>


    </div>
</div>
<div class="card bg-light">
    <div class="card-header">
        <h5>История действий заказа</h5>
    </div>
    <div class="card-body">
        <?php
        echo $this->render('_history', ['model' => $model]);
        ?>
    </div>
</div>

