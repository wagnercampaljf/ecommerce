<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 11/12/2015
 * Time: 13:48
 */

namespace common\models;


use yii\base\Model;
use yii\web\IdentityInterface;

abstract class MudarSenhaForm extends Model
{
    public $password;
    public $new_password;
    public $repeat_password;
    public $user_id;

    protected $_user = false;

    public function rules()
    {
        return [
            [['password', 'new_password', 'repeat_password', 'user_id'], 'required'],
            [['password', 'new_password', 'repeat_password'], 'string', 'min' => 6],
            [['password'], 'validatePassword'],
            [
                ['repeat_password'],
                'compare',
                'compareAttribute' => 'new_password',
                'message' => 'Repita a senha exatamente igual',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Senha Atual',
            'new_password' => 'Nova Senha',
            'repeat_password' => 'Repita a senha',
            'user_id' => 'Usuário'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->$attribute)) {
            $this->addError($attribute, 'Senha Incorreta.');
        }
    }

    public function changePassword()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->setPassword($this->new_password);

            return $user->save();
        } else {
            return false;
        }
    }

    /**
     * Encontra o Usuário pelo [[user_id]]
     *
     * @return IdentityInterface|null
     */
    abstract public function getUser();
}