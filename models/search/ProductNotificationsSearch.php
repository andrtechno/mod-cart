<?php

namespace panix\mod\cart\models\search;

use panix\engine\data\ActiveDataProvider;
use panix\mod\cart\models\ProductNotifications;

class ProductNotificationsSearch extends ProductNotifications {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'product_id'], 'integer'],
            [['email'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = ProductNotifications::find();
        $query->joinWith('product');
        $query->groupBy('product_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => self::getSort()
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        //$query->andFilterWhere(['like', 'product.name', $this->name]);
        //$query->andFilterWhere(['like', 'product.quantity', $this->product->quantity]);

        return $dataProvider;
    }

}
