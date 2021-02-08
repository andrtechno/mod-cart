<?php

namespace panix\mod\cart\models\query;

use panix\engine\traits\query\DefaultQueryTrait;
use yii\db\ActiveQuery;
use yii\db\Exception;

class OrderStatusesQuery extends ActiveQuery
{

    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName . '.ordern' => SORT_DESC]);
        parent::init();
    }

}
