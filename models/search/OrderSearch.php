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
            [['user_phone', 'user_email', 'ttn'], 'string'],
            [['buyOneClick', 'call_confirm', 'paid', 'apply_user_points'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'status_id' => Yii::t('cart/Order', 'STATUS_ID'),
            'buyOneClick' => Yii::t('cart/Order', 'BUYONECLICK'),
            'call_confirm' => Yii::t('cart/Order', 'CALL_CONFIRM'),
            'paid' => Yii::t('cart/Order', 'PAID'),
            'apply_user_points' => Yii::t('cart/Order', 'Активированы бонусы?'),
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

        $query->andFilterWhere(['like', 'delivery_id', $this->delivery_id]);
        $query->andFilterWhere(['like', 'payment_id', $this->payment_id]);
        $query->andFilterWhere(['like', 'ttn', $this->ttn]);

        $query->andFilterWhere(['status_id' => $this->status_id]);

        if ($this->buyOneClick)
            $query->andFilterWhere(['buyOneClick' => $this->buyOneClick]);
        if ($this->call_confirm)
            $query->andFilterWhere(['call_confirm' => $this->call_confirm]);
        if ($this->paid)
            $query->andFilterWhere(['paid' => $this->paid]);
        if ($this->apply_user_points)
            $query->andFilterWhere(['apply_user_points' => $this->apply_user_points]);


        $timezone = Yii::$app->settings->get('app', 'timezone');
        $date_utc2 = new \DateTime();
        $date_utc2->setTimezone(new \DateTimeZone($timezone));
        list($year, $month, $day) = explode('-',$this->created_at);
        $date_utc2->setDate($year, $month, $day)->setTime(0, 0, 0, 0);

        $from_date = $date_utc2->getTimestamp();
        $to_date = $date_utc2->modify('+1 day')->getTimestamp() - 1;

        if ($this->created_at)
            $query->andFilterWhere(['between', 'created_at', $from_date, $to_date]);
        if ($this->updated_at)
            $query->andFilterWhere(['between', 'updated_at', $from_date, $to_date]);


        return $dataProvider;
    }

}
