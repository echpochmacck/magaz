<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property string $order_date
 * @property float $sum
 * 
 *
 * @property Sostav[] $sostavs
 * @property User $user
 */
class Orders extends \yii\db\ActiveRecord
{
    // public $sum;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'required'],
            [['user_id'], 'integer'],
            ['sum', 'required'],
            [['order_date'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status' => 'Статус',
            'order_date' => 'Дата Оформления',
            'sum' => 'Сумма'
        ];
    }

    /**
     * Gets query for [[Sostavs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSostavs()
    {
        return $this->hasMany(Sostav::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function checkSum(array $carzina)
    {
        $sum = 0;
        foreach ($carzina as $key => $value) {
            $sum += $value['price'] * $value['quantity'];
        }
        return $sum;
    }

    public static function checkEmptyCarzina(array $array)
    {
        $copy = $array;
        foreach ($array as $key => $ar) {
            if ($ar['quantity'] < 1) {
                unset($copy[$key]);
            }
        }
        return $copy;
    }


    public static function queryGoods($id)
    {
        return Sostav::find()
            ->select(['sostav.*', 'products.title as product_title'])
            ->innerJoin('products', 'sostav.product_id = products.id')
            ->where(['order_id' => $id])
        ;
    }
}
