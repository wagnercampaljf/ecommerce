<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 09/05/2016
 * Time: 13:48
 */

namespace common\models;

use Yii;
use yii\base\Model;

class Portal extends Model
{
    public $name;
    public $email;
    public $telefone;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'telefone'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
//            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
            'name' => 'Nome',
            'email' => 'E-mail',
            'telefone' => 'Telefone',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([$this->email => $this->name])
                ->setSubject($this->name)
                ->setHtmlBody(
                    'Nome: ' . $this->name .
                    '<br>' .
                    'E-mail: ' . $this->email .
                    '<br>' .
                    'Telefone: ' . $this->telefone
                )
                ->send();

            return true;
        }

        return false;
    }
}