<?php
use panix\engine\Html;
use panix\engine\CMS;
?>
<tr>
    <td>
        <?php echo Html::a($data->username,['/admin/users/default/update',['id' => $data->user_id]]); ?>

        <br>
        <span class="date"><?= CMS::date($data->date_create) ?></span>
    </td>
    <td>
        <?php
        foreach ($data->getDataBefore() as $key => $val) {
            if (!empty($val)) {
                echo "$key: <span class=\"text-danger\">{$val}</span>" . '<br>';
            }
        }
        ?>
    </td>
    <td>
        <?php
        foreach ($data->getDataAfter() as $key => $val) {
            if (!empty($val)) {
                echo "$key: <span class=\"text-success\">{$val}</span>" . '<br>';
            }
        }
            ?>
    </td>
</tr>