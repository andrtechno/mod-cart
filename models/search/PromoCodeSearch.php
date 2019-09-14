<?php

namespace panix\mod\cart\models\search;

use panix\engine\data\ActiveDataProvider;
use panix\mod\cart\models\PromoCode;

class PromoCodeSearch extends PromoCode
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'used', 'max_use'], 'integer'],
            [['code'], 'safe'],
            [['created_at', 'updated_at'], 'date', 'format' => 'php:Y-m-d']
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
        $query = PromoCode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);


        $query->andFilterWhere(['like', 'code', $this->code]);
        $query->andFilterWhere(['like', 'used', $this->used]);
        $query->andFilterWhere(['like', 'max_use', $this->max_use]);

        return $dataProvider;
    }

}
