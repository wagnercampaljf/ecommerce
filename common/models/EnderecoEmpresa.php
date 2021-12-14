<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "endereco_empresa".
 *
 * @property integer $id
 * @property string $cep
 * @property string $logradouro
 * @property string $complemento
 * @property string $referencia
 * @property string $estado
 * @property string $bairro
 * @property string $numero
 * @property integer $cidade_id
 * @property integer $empresa_id
 *
 * @property Empresa $empresa
 * @property Cidade $cidade
 *
 * @author Vinicius Schettino 02/12/2014
 */
class EnderecoEmpresa extends \yii\db\ActiveRecord
{
    /**
     * @author Otavio Augusto Ferreira Rodrigues  12/03/2015
     */
    public $estado;

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'endereco_empresa';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['cep', 'logradouro', 'cidade_id', 'numero', 'bairro'], 'required', ],
            [['empresa_id'], 'required', 'on' => 'update'],
            [['id', 'cidade_id', 'empresa_id'], 'integer'],
            [['cep'], 'string', 'max' => 10],
            [['logradouro', 'referencia', 'estado'], 'string', 'max' => 255],
            [['complemento', 'bairro'], 'string', 'max' => 50],
            [['numero'], 'string', 'max' => 15]
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
            'cep' => 'Cep',
            'logradouro' => 'Logradouro',
            'complemento' => 'Complemento',
            'referencia' => 'Referência',
            'cidade_id' => 'Cidade',
            'empresa_id' => 'Empresa',
            'estado' => 'Estado',
            'cidade' => 'Cidade',
            'bairro' => 'Bairro',
            'numero' => 'Número'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['id' => 'empresa_id']);
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
     * Retorna endereco completo em forma de string
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/12/2015
     */
    public function getLabelEndereco()
    {

        return $this->logradouro . ' ' . $this->numero . ' ' . $this->complemento . ' ' . $this->bairro . ' ' . $this->cep . ' ' . $this->cidade;
    }


    public function __toString()
    {
        return $this->getLabelEndereco();
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new EnderecoEmpresaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da EnderecoEmpresa, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class EnderecoEmpresaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['endereco_empresa.nome' => $sort_type]);
    }

    public function byComprador($comprador_id)
    {
        return $this->joinWith(['empresa.compradors'])->andFilterWhere(['comprador.id' => $comprador_id]);
    }
}
