<?php

namespace panix\mod\cart\widgets\delivery\meest;

use panix\engine\web\AssetBundle;


class DeliveryAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/js';

    public $js = [
         'novaposhta.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
