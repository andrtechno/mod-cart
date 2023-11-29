<?php

use panix\engine\Html;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Delivery;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\telinput\PhoneInput;
use panix\engine\CMS;
use panix\mod\user\models\User;

/**
 * @var $this \yii\web\View
 */
?>

<?php
$related = false;
if (!$model->user_id) {
    $user = User::find()->where(['email' => $model->user_email])->one();
    if ($user) {
        $related = true;
    }
}
if ($related) {

    ?>
    <!-- Modal -->
    <div class="modal fade" id="diffModal" tabindex="-1" role="dialog" aria-labelledby="diffModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel"><?= Yii::t('cart/admin','SIMILARTY_IN'); ?> <strong><?= $model->user_email; ?></strong></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered m-0">
                            <tr>
                                <th></th>
                                <th>Заказ</th>
                                <th><?= Yii::t('cart/Order','USER_ID'); ?></th>
                                <th>% сходство</th>
                            </tr>

                            <tr>
                                <td><strong><?= Yii::t('cart/Order','USER_NAME'); ?></strong></td>
                                <td><?= trim($model->user_name); ?></td>
                                <td><?= (!empty($user->first_name)) ? trim($user->first_name) : Yii::$app->formatter->asHtml(null); ?></td>
                                <td>
                                    <?php
                                    similar_text(trim($model->user_name), trim($user->first_name), $percent);
                                    ?>
                                    <?= Html::tag('span', round($percent, 0) . '%', ['class' => 'text-' . (($percent > 80) ? 'success' : 'danger')]); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= Yii::t('cart/Order','USER_LASTNAME'); ?></strong></td>
                                <td><?= trim($model->user_lastname); ?></td>
                                <td><?= ($user->last_name) ? trim($user->last_name) : Yii::$app->formatter->asHtml(null); ?></td>
                                <td>
                                    <?php
                                    $d = similar_text(trim($model->user_lastname), trim($user->last_name), $percent12);
                                    ?>
                                    <?= Html::tag('span', round($percent12, 0) . '%', ['class' => 'text-' . (($percent12 > 80) ? 'success' : 'danger')]); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>IP created</strong></td>
                                <td><?= $model->ip_create; ?></td>
                                <td><?= $user->ip_create; ?></td>
                                <td>
                                    <?php
                                    $d = similar_text($model->ip_create, $user->ip_create, $percent_ip);
                                    ?>
                                    <?= Html::tag('span', round($percent_ip, 0) . '%', ['class' => 'text-' . (($percent_ip > 80) ? 'success' : 'danger')]); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= Yii::t('cart/Order','USER_PHONE'); ?></strong></td>
                                <td><?= CMS::phone_format($model->user_phone); ?></td>
                                <td><?= ($user->phone) ? CMS::phone_format($user->phone) : Yii::$app->formatter->asHtml(null); ?></td>
                                <td>
                                    <?php
                                    $d = similar_text($model->user_phone, $user->phone, $percent13);
                                    ?>
                                    <?= Html::tag('span', round($percent13, 0) . '%', ['class' => 'text-' . (($percent13 > 80) ? 'success' : 'danger')]); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php \yii\widgets\ActiveForm::begin(['action' => ['related']]); ?>
                    <?= Html::hiddenInput('order_id', $model->id); ?>
                    <?= Html::hiddenInput('user_id', $user->id); ?>
                    <span class="text-danger">
                        <i class="icon-warning"></i> <?= Yii::t('cart/admin','LINK_ORDER_FOUND_USER') ?>
                    </span>
                    <?= Html::submitButton(Yii::t('cart/admin','TO_TIE'), ['class' => 'btn btn-success']) ?>
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
$form = ActiveForm::begin([
    'id' => 'order-form',
    'fieldConfig' => [
        'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-4\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-form-label',
            'offset' => 'offset-sm-4 offset-lg-3 offset-xl-4',
            'wrapper' => 'col-sm-8 col-md-8 col-lg-9 col-xl-8',
            'error' => '',
            'hint' => '',
        ],
    ]

]);
?>


    <div class="card-body">
        <h5 class="mt-3 mb-3 text-center"><?= Yii::t('cart/default','DELIVERY_PAYMENT'); ?></h5>
        <?=
        $form->field($model, 'status_id')->dropDownList(ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name'));
        ?>
        <?=
        $form->field($model, 'payment_id')->dropDownList(ArrayHelper::map(Payment::find()->all(), 'id', 'name'), [
            'prompt' => html_entity_decode($model::t('SELECT_PAYMENT'))
        ]);
        ?>
        <?=
        $form->field($model, 'delivery_id')->dropDownList(ArrayHelper::map(Delivery::find()->all(), 'id', 'name'), [
            'prompt' => html_entity_decode($model::t('SELECT_DELIVERY'))
        ]);
        ?>

        <div id="delivery-form">
            <?php
            if ($model->delivery_id) {
                $delivery = \panix\mod\cart\models\Delivery::findOne($model->delivery_id);
                $system = $delivery->getDeliverySystemClass();
                if ($system instanceof \panix\mod\cart\components\delivery\BaseDeliverySystem) {
                    echo $system->processRequestAdmin($delivery, $model);
                }
            }
            ?>

        </div>

        <?php //echo $form->field($model, 'delivery_address')->textInput(); ?>
        <?= $form->field($model, 'ttn')->textInput()->hint('После заполнение ТТН, клиенту будет отправлено уведомление на почту.'); ?>

        <h5 class="mt-3 mb-3 text-center">Личная информация</h5>
        <?= $form->field($model, 'user_name')->textInput(); ?>
        <?= $form->field($model, 'user_lastname')->textInput(); ?>
        <?= $form->field($model, 'user_email', [
            'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-4\">{label}</div>\n{hint}\n{beginWrapper}{input}{related}\n{error}{endWrapper}",
            'parts' => [
                '{related}' => ($related) ? '<button type="button" class="btn text-danger btn-sm btn-link" data-toggle="modal" data-target="#diffModal"><i class="icon-warning text-danger"></i> '.Yii::t('cart/admin','MATCH_USER').'</button>' : ''
            ]
        ])->textInput(); ?>
        <?php
        if (!$model->isNewRecord && $model->user_phone) { ?>
            <?= $form->field($model, 'user_phone', [
                'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-4\">{label}</div>\n{hint}\n{beginWrapper}{input}{call}\n{error}{endWrapper}",
                'parts' => [
                    '{call}' => Html::a(Html::icon('phone') . ' '.Yii::t('cart/admin','CALL'), 'tel:' . $model->user_phone, ['class' => 'mt-2 mt-lg-0 float-none float-lg-right btn btn-light'])
                ]
            ])->widget(PhoneInput::class, [
                'jsOptions' => [
                    'autoPlaceholder' => 'off'
                ]
            ]); ?>


        <?php } else {
            echo $form->field($model, 'user_phone')->widget(PhoneInput::class, [
                'jsOptions' => [
                    'autoPlaceholder' => 'off'
                ]
            ]);
        }
        ?>
        <?= $form->field($model, 'user_comment')->textArea(); ?>
        <h5 class="mt-3 mb-3 text-center">Прочее</h5>
        <?= $form->field($model, 'admin_comment')->textArea(); ?>
        <?php
        if (!$model->apply_user_points) {
            echo $form->field($model, 'discount')->textInput();
        }
        ?>
        <?= $form->field($model, 'invoice')->textInput(['maxlength' => 50]); ?>
        <?= $form->field($model, 'paid')->checkbox(); ?>
    </div>
    <div class="card-footer text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/default', 'CREATE') : Yii::t('app/default', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php

$this->registerJs("
$('#order-delivery_id').change(function(){
        $.ajax({
            url: common.url('/admin/cart/delivery/process?id=' + $(this).val()),
            type: 'POST',
            dataType: 'html',
            data: $('#order-form').serialize(),
            success: function (data) {
                $('#delivery-form').html(data);
                $('#delivery-form').removeClass('pjax-loader');
            },
            beforeSend:function(){
                $('#delivery-form').addClass('pjax-loader');
            }
        });
});

");
