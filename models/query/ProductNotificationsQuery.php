<?php

namespace panix\mod\cart\models\query;

use yii\db\ActiveQuery;

class ProductNotificationsQuery extends ActiveQuery {

    public function published($state = 1) {
        return $this->andWhere(['switch' => $state]);
    }


}
