<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "movimentacao_estoque_mestre".
 *
 * @property integer $id
 * @property string $descricao
 * @property boolean $e_autorizado
 * @property boolean $e_remessa_recebida
 * @property integer $autorizado_por
 * @property string $salvo_em
 * @property integer $salvo_por
 * @property integer $filial_origem_id
 * @property integer $filial_destino_id
 * @property string $codigo_remessa_omie
 *
 * @property Administrador $autorizadoPor
 * @property Filial $filialDestino
 * @property Filial $filialOrigem
 * @property Administrador $salvoPor
 *
 * @author Unknown 26/10/2021
 */
class MovimentacaoEstoqueMestre extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 26/10/2021
     */
    public static function tableName()
    {
        return 'movimentacao_estoque_mestre';
    }

    /**
     * @inheritdoc
     * @author Unknown 26/10/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'codigo_remessa_omie'], 'string'],
            [['e_autorizado', 'e_remessa_recebida'], 'boolean'],
            [['autorizado_por', 'salvo_por', 'filial_origem_id', 'filial_destino_id'], 'default', 'value' => null],
            [['autorizado_por', 'salvo_por', 'filial_origem_id', 'filial_destino_id'], 'integer'],
            [['salvo_em', 'filial_origem_id', 'filial_destino_id'], 'required'],
            [['salvo_em'], 'safe'],
            [['autorizado_por'], 'exist', 'skipOnError' => true, 'targetClass' => Administrador::className(), 'targetAttribute' => ['autorizado_por' => 'id']],
            [['salvo_por'], 'exist', 'skipOnError' => true, 'targetClass' => Administrador::className(), 'targetAttribute' => ['salvo_por' => 'id']],
            [['filial_origem_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filial::className(), 'targetAttribute' => ['filial_origem_id' => 'id']],
            [['filial_destino_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filial::className(), 'targetAttribute' => ['filial_destino_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 26/10/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
            'e_autorizado' => 'E Autorizado',
            'autorizado_por' => 'Autorizado Por',
            'salvo_em' => 'Salvo Em',
            'salvo_por' => 'Salvo Por',
            'filial_origem_id' => 'Filial Origem ID',
            'filial_destino_id' => 'Filial Destino ID',
            'codigo_remessa_omie' => 'Codigo Remessa Omie',
	    'e_remessa_recebida' => 'É Remessa Recebida?',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public function getMovimentacaoEstoqueDetalhe()
    {
        return $this->hasMany(MovimentacaoEstoqueDetalhe::className(), ['movimentacao_estoque_mestre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public function getAutorizadoPor()
    {
        return $this->hasOne(Administrador::className(), ['id' => 'autorizado_por']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public function getFilialDestino()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_destino_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public function getFilialOrigem()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_origem_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public function getSalvoPor()
    {
        return $this->hasOne(Administrador::className(), ['id' => 'salvo_por']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public static function find()
    {
        return new MovimentacaoEstoqueMestreQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MovimentacaoEstoqueMestre, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 26/10/2021
*/
class MovimentacaoEstoqueMestreQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/10/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['movimentacao_estoque_mestre.nome' => $sort_type]);
    }
}
