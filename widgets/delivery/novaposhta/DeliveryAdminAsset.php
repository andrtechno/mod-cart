<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use panix\engine\web\AssetBundle;


class DeliveryAdminAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/js';

    public $js = [
         'novaposhta_admin.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
