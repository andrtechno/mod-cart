<?php

namespace panix\mod\cart\models\query;

use yii\db\ActiveQuery;

class OrderQuery extends ActiveQuery {

    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName.'.id' => SORT_DESC]);
        parent::init();
    }

}
