<?php

namespace common\models;

use Yii;
use yiibr\brvalidator\CnpjValidator;

/**
 * Este é o model para a tabela "filial".
 *
 * @property integer $id
 * @property string $nome
 * @property string $razao
 * @property string $documento
 * @property boolean $juridica
 * @property integer $cep
 * @property integer $lojista_id
 * @property integer $banco_id
 * @property string $numero_banco
 * @property integer $id_tipo_empresa
 * @property boolean $mercado_livre_secundario
 * @property boolean $mercado_livre_logo
 *
 * @property CaracteristicaFilial[] $caracteristicaFilials
 * @property Caracteristica[] $caracteristicas
 * @property Pedido[] $pedidos
 * @property Usuario[] $usuarios
 * @property CupomDesconto[] $cupomDescontos
 * @property EnderecoFilial[] $enderecoFilial
 * @property ProdutoFilial[] $produtoFilials
 * @property Lojista $lojista
 * @property Banco $banco
 * @property FilialServicoTransportadora[] $filialServicoTransportadoras
 * @property ServicoTransportadora[] $servicoTransportadoras
 * @property TransporteProprioFilial[] $transporteProprioFilials
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Filial extends \yii\db\ActiveRecord implements SearchModel
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'razao', 'documento', 'token_moip',  'telefone'], 'required'],
            [['nome', 'razao','refresh_token_meli'], 'string'],
            [['juridica', 'mercado_livre_secundario', 'mercado_livre_logo'], 'boolean'],
            [['telefone', 'telefone_alternativo'], 'string', 'max' => 20],
            [['lojista_id', 'banco_id', 'id_tipo_empresa'], 'integer'],
            ['documento', CnpjValidator::className()],
            [['numero_banco'], 'string', 'max' => 150],
            ['porcentagem_venda', 'number']
	    
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'razao' => Yii::t('app', 'Razão'),
            'documento' => Yii::t('app', 'Documento'),
            'juridica' => Yii::t('app', 'Jurídica'),
            'lojista_id' => Yii::t('app', 'Lojista'),
            'banco_id' => Yii::t('app', 'Banco'),
            'numero_banco' => Yii::t('app', 'Número Banco'),
            'token_moip' => Yii::t('app', 'Token Moip'),
            'numero_agencia' => Yii::t('app', 'Número Agência'),
            'porcentagem_venda' => Yii::t('app', 'Porcentagem Venda'),
            'id_tipo_empresa' => Yii::t('app', 'Tipo Empresa'),
            'telefone' => Yii::t('app', 'Telefone'),

            'telefone_alternativo' => Yii::t('app', 'Telefone Alternativo'),
            'mercado_livre_secundario' => Yii::t('app', 'Mercado Livre Secundário?'),
            'mercado_livre_logo' => Yii::t('app', 'Mercado Livre Com Logo?'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getCaracteristicaFilials()
    {
        return $this->hasMany(CaracteristicaFilial::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getCaracteristicas()
    {
        return $this->hasMany(Caracteristica::className(),
            ['id' => 'caracteristica_id'])->viaTable('caracteristica_filial', ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCupomDescontos()
    {
        return $this->hasMany(CupomDesconto::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getEnderecoFilial()
    {
        return $this->hasOne(EnderecoFilial::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoFilials()
    {
        return $this->hasMany(ProdutoFilial::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getLojista()
    {
        return $this->hasOne(Lojista::className(), ['id' => 'lojista_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getBanco()
    {
        return $this->hasOne(Banco::className(), ['id' => 'banco_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getFilialServicoTransportadoras()
    {
        return $this->hasMany(FilialServicoTransportadora::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getServicoTransportadoras()
    {
        return $this->hasMany(ServicoTransportadora::className(),
            ['id' => 'servico_transportadora_id'])->viaTable('filial_servico_transportadora', ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getTransporteProprioFilials()
    {
        return $this->hasMany(TransporteProprioFilial::className(), ['filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getTipoEmpresa()
    {
        return $this->hasOne(TipoEmpresa::className(), ['id' => 'id_tipo_empresa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new FilialQuery(get_called_class());
    }

    public function getLabelSearch()
    {
        return $this->nome;
    }
}

/**
 * Classe para contenção de escopos da Filial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class FilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['filial.nome' => $sort_type]);
    }
}
