<?php

namespace panix\mod\cart\widgets\delivery\meest;

use panix\engine\web\AssetBundle;


class DeliveryAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/js';

    public $js = [
         'meest.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
