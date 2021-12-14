<?php

namespace common\models;

use Inacho\CreditCard;
use Yii;
use yii\base\Model;

class FormPagamento extends Model
{
    public $name;
    public $birthDay;
    public $cvcNumber;
    public $ccNumber;
    public $expirationDate;
    public $installments;
    public $method;

    public function rules()
    {
        return [
            [
                [
                    'name',
                    'birthDay',
                    'cvcNumber',
                    'ccNumber',
                    'expirationDate',
                    'installments'
                ],
                'required',
                'on' => 'moip_creditCard'
            ],
            [['expirationDate'], 'checkExpirationDate'],
            [['cvcNumber', 'ccNumber'], 'checkCreditCard', 'on' => 'moip_creditCard'],
            [['method'], 'safe', 'on' => ['moip_creditCard', 'moip_boleto']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Nome',
            'expirationDate' => 'Data de Vencimento',
            'birthDay' => 'Data de Nascimento',
            'ccNumber' => 'Número do Cartão',
            'cvcNumber' => 'Cód. de Segurança',
            'installments' => 'Parcelas'
        ];
    }

    public function checkExpirationDate($attribute, $params)
    {
        $expirationDate = date_create_from_format('m/y', $this->expirationDate);
        $valid = CreditCard::validDate($expirationDate->format('Y'), $expirationDate->format('m'));
        if (!$valid) {
            $this->addError('expirationDate', 'Cartão Vencido');
        }
    }

    public function checkCreditCard($attribute, $params)
    {
        $creditcard = CreditCard::validCreditCard($this->ccNumber);
        if (!$creditcard['valid']) {
            $this->addError('ccNumber', 'Cartão Inválido');
        }
        if (!CreditCard::validCvc($this->cvcNumber, $creditcard['type'])) {
            $this->addError('cvcNumber', 'Cód. de Segurança Inválido');
        }
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values) && isset($values['method'])) {
            $this->setScenario($values['method']);
            parent::setAttributes($values, $safeOnly);
        }
    }
}