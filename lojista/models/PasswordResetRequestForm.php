<?php
namespace lojista\models;

use common\models\Usuario;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'exist',
                'targetClass' => '\common\models\Usuario',
                'filter' => ['ativo' => Usuario::STATUS_ACTIVE],
                'message' => 'Este email não consta em nosso sistema.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user = Usuario::findOne([
            'ativo' => Usuario::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        $user->scenario = 'noRepeatPassword';
        if ($user) {
            if (!Usuario::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose('passwordResetTokenLojista', ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Alteração de senha para ' . \Yii::$app->name)
                    ->send();
            }

        }

        return false;
    }
}
