<?php

namespace panix\mod\cart\components\events;

use yii\base\ModelEvent;

/**
 * Class EventProduct
 *
 * @property $ordered_product \panix\mod\cart\models\OrderProduct
 * @package panix\mod\cart\components\events
 */
class EventProduct extends ModelEvent
{

    public $product_model;

    /**
     * @var \panix\mod\cart\models\OrderProduct
     */
    public $ordered_product;
    public $quantity;
   // public $params;
}
