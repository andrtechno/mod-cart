<?php

namespace panix\mod\cart\models\query;

use panix\engine\CMS;
use panix\engine\traits\query\DefaultQueryTrait;
use yii\db\ActiveQuery;
use yii\db\Exception;

class OrderQuery extends ActiveQuery
{
    use DefaultQueryTrait;

    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName . '.id' => SORT_DESC]);
        parent::init();
    }

    /**
     * @param string $function
     * @return mixed|null
     */
    public function aggregateTotalPrice($function = 'MIN')
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        //$this->addSelect([$tableName . '.*', "{$function}({$tableName}.`total_price`) AS aggregation_price"]);
        //$this->orderBy(["aggregation_price" => ($function === 'MIN') ? SORT_ASC : SORT_DESC]);

        $this->addSelect(["{$function}({$tableName}.`total_price`) AS aggregation_price"]);

        $this->distinct(false);
        $this->limit(1);
        $result = $this->asArray()->one();
        if ($result) {
            return $result['aggregation_price'];
        }
        return null;
    }


    /**
     * Filter orders by total_price
     * @param $value int
     * @param $operator string '=', '>=', '<='
     * @return $this
     * @throws Exception
     */
    public function applyPrice($value, $operator = '=')
    {
        if (!in_array($operator, ['=', '>=', '<='])) {
            throw new Exception('error operator in ' . __FUNCTION__);
        }
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($value) {
            $this->andWhere("{$tableName}.`total_price` {$operator} {$value}");
        }
        return $this;
    }

    /**
     * Default status is new
     *
     * @param int $status_id
     * @return $this
     */
    public function status($status_id = 1)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere([$tableName . '.status_id' => $status_id]);
        return $this;
    }
}
