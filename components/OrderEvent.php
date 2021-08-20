<?php

namespace panix\mod\cart\components;

use yii\base\Event;

class OrderEvent extends Event
{
    public $order;
}
