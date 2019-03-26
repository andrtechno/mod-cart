<?php
$data_before = $data->getDataBefore();
$data_after = $data->getDataAfter();
?>
<tr>
    <td>
        <a href="<?= Yii::app()->createUrl('/admin/users/default/update', array('id' => $data->user_id)) ?>"><?= $data->username ?></a>
        <br/>
        <span class="date"><?= CMS::date($data->date_create) ?></span>
    </td>
    <?php if (isset($data_before['changed'])){ ?>
        <td>
            <?php
            echo Yii::t('CartModule.admin', 'HISTORY_CHANGE_PRODUCT')  .': '. $data_before['name'] . '<br>';
            echo Yii::t('CartModule.admin', 'QUANTITY') .': '.  $data_before['quantity'];
            ?>
        </td>
        <td>
            <?php
            echo Yii::t('CartModule.admin', 'QUANTITY') .': '.  $data_after['quantity'];
            ?>
        </td>
    <?php }elseif ($data_before['deleted']){ ?>
        <td colspan="2">
            <?php
            echo Yii::t('CartModule.admin', 'HISTORY_DELETE_PRODUCT') .': '. $data_before['name'] . '<br>';
            echo Yii::t('CartModule.default', 'COST') .': '.  $data_before['price'] . '<br>';
            echo Yii::t('CartModule.admin', 'QUANTITY') .': '. $data_before['quantity'];
            ?>
        </td>
    <?php }else{ ?>
        <td colspan="2">
            <?php
            echo Yii::t('CartModule.admin', 'HISTORY_CREATE_PRODUCT',array('{name}'=>$data_before['name'])) . '<br>';
            echo Yii::t('CartModule.default', 'COST') .': '.  $data_before['price'] . '<br>';
            echo Yii::t('CartModule.admin', 'QUANTITY') .': '.  $data_before['quantity'];
            ?>
        </td>
    <?php } ?>
</tr>