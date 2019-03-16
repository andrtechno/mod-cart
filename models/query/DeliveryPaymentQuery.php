<?php

namespace panix\mod\cart\models\query;

use yii\db\ActiveQuery;
use panix\engine\traits\DefaultQueryTrait;

class DeliveryPaymentQuery extends ActiveQuery
{

    use DefaultQueryTrait;

    public function orderByName($sort = SORT_ASC)
    {
        return $this->joinWith('translations')
            ->addOrderBy(['{{%order__payment_translate}}.name' => $sort]);
    }

}
