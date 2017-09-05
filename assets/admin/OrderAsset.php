<?php


namespace panix\mod\cart\assets\admin;

use yii\web\AssetBundle;


class OrderAsset extends AssetBundle {

    public $sourcePath = '@cart/assets/admin';
    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
    public $js = [
         'js/orders.update.js',
    ];

}
