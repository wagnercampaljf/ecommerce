<?php


namespace frontend\models;


use yii\base\Model;

class FormularioGarantia extends Model
{
    public $nome;
    public $email;

    public function rules()
    {
        return [
            [['nome', 'email'],'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome'=>'Nome Completo',
            'email'=>'Email',
        ];
    }

}