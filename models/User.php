<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string|null $patronymic
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $authKey
 * @property int $role_id
 * @property float $cash
 *
 * @property Orders[] $orders
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    public $password_repeat;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'login', 'email', 'password', 'password_repeat'], 'required'],
            [['role_id'], 'integer'],
            [['cash'], 'number'],
            [['name', 'surname', 'patronymic', 'login', 'email', 'password', 'authKey'], 'string', 'max' => 255],
            [['email'], 'unique'],
            ['email', 'email'],
            [['login'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
            ['password_repeat', 'compare', 'compareAttribute' =>'password'],
            ['password', 'string', 'min' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'фамилия',
            'patronymic' => 'Отчество',
            'login' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
            'authKey' => 'Auth Key',
            'role_id' => 'Role ID',
            'cash' => 'Баланс',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public static function findByUsername($login)
    {
        return self::findOne(['login' => $login]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }


    public function setAuth($save = false)
    {
        $this->authKey = Yii::$app->security->generateRandomString();
        $save && $this->save(false);
    }

    public function register()
    {
        $this->role_id = Role::getRoleId('avtor');
        $this->cash = 2000;
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->setAuth();
        return $this->save(false);
    }
}
