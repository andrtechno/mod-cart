<?php

namespace panix\mod\cart\models\query;

use yii\db\ActiveQuery;

class DeliveryPaymentQuery extends ActiveQuery {

    public function published($state = 1) {
        return $this->andWhere(['switch' => $state]);
    }

    public function orderByName($sort = SORT_ASC) {
        return $this->joinWith('translations')
                        ->addOrderBy(['{{%shop_payment_method_translate}}.name' => $sort]);
    }

    public function orderByPosition($sort = SORT_ASC) {
        return $this->addOrderBy(['ordern' => $sort]);
    }

}
