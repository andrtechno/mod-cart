<?php

namespace panix\mod\cart\widgets\promocode;

use Yii;
use yii\helpers\Html;
use panix\engine\data\Widget;

class PromoCodeWidget extends Widget
{

    public $model;
    public $attribute;

    public function run()
    {

        return $this->render($this->skin, []);

    }

}
