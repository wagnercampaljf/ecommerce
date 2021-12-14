<?php

namespace common\models;

use Yii;
use yiibr\brvalidator\CnpjValidator;
use yiibr\brvalidator\CpfValidator;

/**
 * Este é o model para a tabela "empresa".
 *
 * @property integer $id
 * @property string $nome
 * @property string $razao
 * @property string $documento
 * @property boolean $juridica
 * @property string $email
 * @property string $telefone
 * @property string $telefone_alternativo
 * @property integer $grupo_id
 * @property string $observacao
 * @property integer $id_tipo_empresa
 *
 * @property Grupo $grupo
 * @property TipoEmpresa $idTipoEmpresa
 * @property EnderecoEmpresa[] $enderecoEmpresas
 * @property Veiculo[] $veiculos
 * @property CupomDesconto[] $cupomDescontos
 * @property Comprador[] $compradors
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Empresa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'empresa';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            // [['documento'], 'required'],
            [['juridica'], 'boolean'],
            [['grupo_id', 'id_tipo_empresa'], 'integer'],
            [['nome', 'razao', 'email'], 'string', 'max' => 150],
            [['documento'], CnpjValidator::className(), 'on' => 'juridica'],
            [['documento'], CpfValidator::className(), 'on' => 'fisica'],
            [['documento'], 'unique'],
            [['telefone', 'telefone_alternativo'], 'string', 'max' => 20],
            [['observacao'], 'string', 'max' => 400],
            [['email'], 'string', 'max' => 150]
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
            'nome' => 'Nome',
            'razao' => 'Razao',
            'documento' => 'CNPJ/CPF',
            'juridica' => 'Juridica',
            'email' => 'Email',
            'telefone' => 'Telefone',
            'telefone_alternativo' => 'Telefone Alternativo',
            'grupo_id' => 'Grupo ID',
            'observacao' => 'Observação',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getGrupo()
    {
        return $this->hasOne(Grupo::className(), ['id' => 'grupo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEnderecosEmpresa()
    {
        return $this->hasMany(EnderecoEmpresa::className(), ['empresa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEnderecoEmpresa()
    {
        return $this->hasOne(EnderecoEmpresa::className(), ['empresa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getVeiculos()
    {
        return $this->hasMany(Veiculo::className(), ['empresa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCupomDescontos()
    {
        return $this->hasMany(CupomDesconto::className(), ['empresa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCompradors()
    {
        return $this->hasMany(Comprador::className(), ['empresa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new EmpresaQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 06/03/2015
     */
    public function getIdTipoEmpresa()
    {
        return $this->hasOne(TipoEmpresa::className(), ['id' => 'id_tipo_empresa']);
    }

    /**
     * Retorna string do documento em formato CPF ou CNPJ
     * @return mixed
     * @autor Vitor Horta
     * @since 0.1
     */
    public function getDocumentoLabel()
    {
        if (strlen($this->documento) > 11) {
            return \Yii::$app->formatter->asCNPJ($this->documento);
        }
        return \Yii::$app->formatter->asCPF($this->documento);
    }
}

/**
 * Classe para contenção de escopos da Empresa, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class EmpresaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['empresa.nome' => $sort_type]);
    }
}
