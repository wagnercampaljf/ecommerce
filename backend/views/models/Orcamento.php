<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class Orcamento extends Model
{
    public $name;
    public $email;
    public $tel;
    public $subject;
    public $body;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'tel','subject' ,'body'], 'required'],
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
            'tel' => 'WhatsApp',
            'subject' => 'Modelo e Ano do Veículo',
            'body' => 'Peças que você gostaria de solicitar',
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
                ->setSubject($this->subject)
                ->setHtmlBody(
                    'Nome: ' . $this->name .
                    '<br>' .
                    'E-mail: ' . $this->email .
                    '<br>' .
                    'Tel: ' . $this->tel .
                    '<br>' .
                    'Modelo: ' . $this->subject .
                    '<br>' .
                    'Peças : ' .
                    '<br>' . $this->body
                )
                ->send();

            return true;
        }

        return false;
    }
}