<?php
namespace frontend\models;

use common\models\Comprador;
use yii\base\Model;
use yii\bootstrap\Html;

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
                'targetClass' => '\common\models\Comprador',
                'filter' => ['ativo' => Comprador::STATUS_ACTIVE],
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
        $user = Comprador::findOne([
            'ativo' => Comprador::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        $user->scenario = 'noRepeatPassword';
        if ($user) {
            if (!Comprador::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save(true, ['password_reset_token'])) {
                return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Alteração de senha ' . \Yii::$app->name)
                    ->send();
            }
        }

        return false;
    }
}
