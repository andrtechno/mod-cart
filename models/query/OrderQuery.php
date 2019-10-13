<?php

namespace panix\mod\cart\models\query;

use panix\mod\shop\models\Currency;
use yii\db\ActiveQuery;
use yii\db\Exception;

class OrderQuery extends ActiveQuery
{

    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName . '.id' => SORT_DESC]);
        parent::init();
    }

    public function aggregateTotalPrice($function = 'MIN')
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addSelect([$tableName . '.*', "{$function}({$tableName}.`total_price`) AS aggregation_price"]);
        $this->orderBy(["aggregation_price" => ($function === 'MIN') ? SORT_ASC : SORT_DESC]);
        $this->distinct(false);
        $this->limit(1);
        //$result = \Yii::$app->db->cache(function ($db) {
        $result = $this->asArray()->one();
        // }, 3600);

        if ($result) {
            return $result['aggregation_price'];
        }
        return null;
    }


    /**
     * Filter orders by total_price
     * @param $value int
     * @param $operator string '=', '>=', '<='
     * @throws Exception
     * @return $this
     */
    public function applyPrice($value, $operator = '=')
    {
        if (!in_array($operator, ['=', '>=', '<='])) {
            throw new Exception('error operator in '.__FUNCTION__);
        }
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($value) {
            $this->andWhere("{$tableName}.`total_price` {$operator} {$value}");
        }
        return $this;
    }
}
