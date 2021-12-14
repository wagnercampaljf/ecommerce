<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "endereco_filial".
 *
 * @property integer $id
 * @property string $cep
 * @property string $logradouro
 * @property string $complemento
 * @property string $referencia
 * @property integer $cidade_id
 * @property integer $filial_id
 *
 * @property Filial $filial
 * @property Cidade $cidade
 *
 * @author Vinicius Schettino 02/12/2014
 */
class EnderecoFilial extends \yii\db\ActiveRecord
{
    public $estado;

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'endereco_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['cep', 'logradouro', 'cidade_id', 'numero'], 'required'],
            [['filial_id'], 'required', 'on' => 'update'],
            [['cidade_id', 'filial_id'], 'integer'],
            [['cep'], 'string', 'max' => 10],
            [['logradouro', 'bairro', 'referencia'], 'string', 'max' => 255],
            [['numero', 'complemento'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cep' => 'CEP',
            'logradouro' => 'Logradouro',
            'complemento' => 'Complemento',
            'referencia' => 'Referência',
            'cidade_id' => 'Cidade',
            'filial_id' => 'Filial',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCidade()
    {
        return $this->hasOne(Cidade::className(), ['id' => 'cidade_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new EnderecoFilialQuery(get_called_class());
    }

    public function __toString()
    {
        $complemento = (isset($this->complemento) ? ' - ' . $this->complemento . ', ' : ', ');
        $end = $this->logradouro . ', ' . $this->numero . $complemento . $this->bairro . ' ' . $this->cidade->getLabel() . '. Brasil';

        return $end;
    }
}

/**
 * Classe para contenção de escopos da EnderecoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class EnderecoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['endereco_filial.nome' => $sort_type]);
    }
}
