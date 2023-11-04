<?php

namespace panix\mod\cart\widgets\delivery\meest;

use panix\engine\web\AssetBundle;


class DeliveryAdminAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/js';

    public $js = [
         'meest_admin.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
