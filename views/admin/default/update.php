<?php

use panix\engine\Html;

use panix\ext\fancybox\Fancybox;

40155


?>
<?php if ($model->call_confirm) { ?>
    <div class="alert alert-info">Мне можно не звонить!</div>
<?php } ?>

<?php if ($model->points > 0) { ?>
    <div class="alert alert-info"><?= Yii::t('default','BONUS_ACTIVE',$model->points);?></div>
<?php } ?>
<?php if (Yii::$app->hasModule('novaposhta') && $model->deliveryMethod) { ?>
    <?php if ($model->deliveryMethod->system == 'novaposhta') { ?>
        <div class="text-right">
            <?= Html::a(Html::icon('novaposhta') . ' ' . Yii::t('novaposhta/default', 'CREATE_EXPRESS_WAYBILL_CART'), ['/admin/novaposhta/express-invoice/create', 'order_id' => $model->primaryKey], ['data-pjax' => 0, 'class' => 'btn btn-danger mb-3']); ?>
        </div>
    <?php }
} ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
    <div class="col-md-6">
        <?= Fancybox::widget(['target' => '.image a']); ?>
        <?php

        //echo Html::a('add', 'javascript:openAddProductDialog(' . $model->id . ');', ['class' => 'btn btn-success']);
        if (!$model->isNewRecord) {
            ?>


                <?php
                echo $this->render('_addProduct', [
                    'model' => $model,
                ]);
                ?>

            <div id="orderedProducts">
                <?php
                if (!$model->isNewRecord) {
                    echo $this->render('_order-products', ['model' => $model]);
                }
                ?>
            </div>

        <?php } else { ?>
            <div class="alert alert-info">Товары можно будет добавить после создание заказа</div>
        <?php } ?>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5><?= Yii::t('cart/admin', 'ORDER_HISTORY'); ?></h5>
    </div>
    <div class="card-body">
        <?php
        echo $this->render('_history', ['model' => $model]);
        ?>
    </div>
</div>

