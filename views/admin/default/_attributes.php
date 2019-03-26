<tr>
    <td>
        <a href="<?= Yii::app()->createUrl('/admin/users/default/update', array('id' => $data->user_id)) ?>"><?= $data->username ?></a>
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