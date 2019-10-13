<?php

namespace panix\mod\cart\models\search;

use Yii;
use panix\engine\data\ActiveDataProvider;
use panix\mod\cart\models\Order;

class OrderSearch extends Order
{
    public $price_min;
    public $price_max;
//            'max' => (int)Order::find()->aggregateTotalPrice('MAX'),
//'min' => (int)Order::find()->aggregateTotalPrice('MIN'),
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_min'],
                'number',
                'min' => (int)Order::find()->aggregateTotalPrice('MIN'),
                'max' => (int)Order::find()->aggregateTotalPrice('MAX')
            ],
            [['price_max'],
                'number',
                'min' => (int)Order::find()->aggregateTotalPrice('MIN'),
                'max' => (int)Order::find()->aggregateTotalPrice('MAX')
            ],
            [['id', 'status_id', 'price_min', 'price_max'], 'integer'],
            [['name', 'slug', 'status_id', 'user_name', 'total_price'], 'safe'],
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

        $query->andFilterWhere(['like', 'user_name', $this->user_name]);
        $query->andFilterWhere(['like', 'status_id', $this->status_id]);
        //$query->andFilterWhere(['like', 'total_price', $this->total_price]);

        return $dataProvider;
    }

}
