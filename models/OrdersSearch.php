<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;
use Yii;

/**
 * OrdersSearch represents the model behind the search form of `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    public $sum;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['status', 'order_date', 'sum'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $user_id)
    {
        $query = Orders::find()
            ->select([
                'orders.id',
                'status',
                'order_date',
                'sum(products.`price` * sostav.quantity) as sum'
            ])
            ->innerJoin('sostav', 'orders.id= sostav.order_id')
            ->innerJoin('products', 'products.id=sostav.product_id')
            ->groupBy(['id', 'order_date'])
            ->where(['user_id' => $user_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'status',
                    'order_date',
                    'id',
                    'sum' => [
                        'desc' => [
                            'sum' => SORT_DESC,
                        ],
                        'asc' => [
                            'sum' => SORT_ASC,
                        ],

                    ]
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_date' => $this->order_date,

        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        $query->andFilterHaving(['like', 'sum', $this->sum]);


        return $dataProvider;
    }

   
}
