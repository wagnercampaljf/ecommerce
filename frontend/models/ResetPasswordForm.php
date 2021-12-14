<?php
namespace frontend\models;

use common\models\Comprador;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $repeat_password;
    /**
     * @var \common\models\Comprador
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  string $token
     * @param  array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Token inválido');
        }
        $this->_user = Comprador::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Token Inválido');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['repeat_password', 'required'],
            [
                ['repeat_password'],
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Os campos Senha e Repetir Senha devem ser iguais'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Senha',
            'repeat_password' => 'Repetir senha'
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = Yii::$app->security->generatePasswordHash($this->password);
        $user->removePasswordResetToken();

        $user->scenario = 'noRepeatPassword';
        return $user->save(true, ['password', 'password_reset_token']);
    }
}
