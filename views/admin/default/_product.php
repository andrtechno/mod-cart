<?php
use panix\engine\Html;
use panix\engine\CMS;

$data_before = $data->getDataBefore();
$data_after = $data->getDataAfter();
?>
<tr>
    <td>
        <?php echo Html::a($data->username, ['/user/admin/default/update', 'id' => $data->user_id]); ?>

        <br/>
        <span class="date"><?= CMS::date(strtotime($data->date_create)) ?></span>
    </td>
    <?php if (isset($data_before['changed'])) { ?>
        <td>
            <?php
            echo Yii::t('cart/admin', 'HISTORY_CHANGE_PRODUCT') . ': ' . $data_before['name'] . '<br>';
            echo Yii::t('cart/admin', 'QUANTITY') . ': ' . $data_before['quantity'];
            ?>
        </td>
        <td>
            <?php
            echo Yii::t('cart/admin', 'QUANTITY') . ': ' . $data_after['quantity'];
            ?>
        </td>
    <?php } elseif ($data_before['deleted']) { ?>
        <td colspan="2">
            <?php if ($data_before['image']) { ?>
                <div class="float-left mr-3">
                    <?php echo Html::img($data_before['image'], ['height' => 50, 'class' => '']); ?>
                </div>
            <?php } ?>
            <div class="float-left">
                <?php
                echo Yii::t('cart/admin', 'HISTORY_DELETE_PRODUCT') . ': <strong>' . $data_before['name'] . '</strong><br>';
                echo Yii::t('cart/default', 'COST') . ': <strong>' . $data_before['price'] . ' '.$data_before['currency'].'</strong><br>';
                echo Yii::t('cart/admin', 'QUANTITY') . ': <strong>' . $data_before['quantity'].'</strong>';
                ?>
            </div>
        </td>
    <?php } else { ?>
        <td colspan="2">
            <?php if ($data_before['image']) { ?>
                <div class="float-left mr-3">
                    <?php echo Html::img($data_before['image'], ['height' => 50, 'class' => '']); ?>
                </div>
            <?php } ?>
            <div class="float-left">
                <?php
                echo Yii::t('cart/admin', 'HISTORY_CREATE_PRODUCT') . ': <strong>'.$data_before['name'].'</strong><br>';
                echo Yii::t('cart/default', 'COST') . ': <strong>' . $data_before['price'] . ' '.$data_before['currency'].'</strong><br>';
                echo Yii::t('cart/admin', 'QUANTITY') . ': <strong>' . $data_before['quantity'].'</strong>';

                ?>
            </div>
        </td>
    <?php } ?>
</tr>