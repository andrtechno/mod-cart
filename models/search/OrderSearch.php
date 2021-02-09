<?php

namespace panix\mod\cart\models\search;

use Yii;
use panix\engine\data\ActiveDataProvider;
use panix\mod\cart\models\Order;

class OrderSearch extends Order
{
    public static $counterFilter = 0;
    public $price_min;
    public $price_max;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status_id', 'price_min', 'price_max', 'delivery_id', 'payment_id'], 'integer'],
            [['status_id', 'user_name', 'total_price', 'created_at', 'updated_at'], 'safe'],
            [['user_phone', 'user_email', 'delivery_city','delivery_address','ttn'], 'string'],
            [['buyOneClick', 'call_confirm', 'paid','apply_user_points'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'status_id' => Yii::t('cart/Order', 'STATUS_ID'),
            'buyOneClick' => Yii::t('cart/Order', 'BUYONECLICK'),
            'call_confirm' => Yii::t('cart/Order', 'CALL_CONFIRM'),
            'paid' => Yii::t('cart/Order', 'PAID'),
            'delivery_city' => Yii::t('cart/Order', 'DELIVERY_CITY'),
            'apply_user_points' => Yii::t('cart/Order', 'Активированы бонусы?'),
            'delivery_address' => Yii::t('cart/Order', 'DELIVERY_ADDRESS'),
            'ttn' => Yii::t('cart/Order', 'TTN'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return \yii\base\Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();
        $className = substr(strrchr(__CLASS__, "\\"), 1);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if (isset($params[$className]['total_price']['min'])) {
            $this->price_min = $params[$className]['total_price']['min'];
            if (!is_numeric($this->price_min)) {
                $this->addError('total_price', Yii::t('yii', '{attribute} must be a number.', ['attribute' => 'min']));
                return $dataProvider;
            }
        }
        if (isset($params[$className]['total_price']['max'])) {
            $this->price_max = $params[$className]['total_price']['max'];
            if (!is_numeric($this->price_max)) {
                $this->addError('total_price', Yii::t('yii', '{attribute} must be a number.', ['attribute' => 'max']));
                return $dataProvider;
            }
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            //'status_id' => $this->status_id,
        ]);


        if ($this->price_max) {
            $query->applyPrice($this->price_max, '<=');
        }
        if ($this->price_min) {
            $query->applyPrice($this->price_min, '>=');
        }

        //search by column contact
        if ($this->user_name) {
            $query->andFilterWhere(['like', 'user_name', $this->user_name]);
            $query->orFilterWhere(['like', 'user_email', $this->user_name]);
            $query->orFilterWhere(['like', 'user_lastname', $this->user_name]);
        }
        $query->andFilterWhere(['like', 'user_phone', $this->user_phone]);
        $query->andFilterWhere(['like', 'user_email', $this->user_email]);
        $query->andFilterWhere(['like', 'status_id', $this->status_id]);
        $query->andFilterWhere(['like', 'delivery_id', $this->delivery_id]);
        $query->andFilterWhere(['like', 'payment_id', $this->payment_id]);
        $query->andFilterWhere(['like', 'delivery_city', $this->delivery_city]);
        $query->andFilterWhere(['like', 'delivery_address', $this->delivery_address]);
        $query->andFilterWhere(['like', 'ttn', $this->ttn]);
        $query->andFilterWhere(['buyOneClick' => $this->buyOneClick]);
        $query->andFilterWhere(['call_confirm' => $this->call_confirm]);
        $query->andFilterWhere(['paid' => $this->paid]);
        $query->andFilterWhere(['apply_user_points' => $this->apply_user_points]);

        if ($this->created_at)
            $query->andFilterWhere(['between', 'created_at', strtotime($this->created_at . ' 00:00:00'), strtotime($this->created_at . ' 23:59:59')]);
        if ($this->updated_at)
            $query->andFilterWhere(['between', 'updated_at', strtotime($this->updated_at . ' 00:00:00'), strtotime($this->updated_at . ' 23:59:59')]);

        return $dataProvider;
    }

}
