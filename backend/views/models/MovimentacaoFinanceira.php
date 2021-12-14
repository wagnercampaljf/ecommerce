<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "movimentacao_financeira".
 *
 * @property integer $id
 * @property string $numero
 * @property string $data_hora
 * @property string $valor
 * @property string $valor_total
 * @property integer $operacao_financeira_id
 * @property integer $movimentacao_financeira_tipo_id
 *
 * @property MovimentacaoFinanceiraTipo $movimentacaoFinanceiraTipo
 * @property OperacaoFinanceira $operacaoFinanceira
 *
 * @author Unknown 26/04/2021
 */
class MovimentacaoFinanceira extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 26/04/2021
     */
    public static function tableName()
    {
        return 'movimentacao_financeira';
    }

    /**
     * @inheritdoc
     * @author Unknown 26/04/2021
     */
    public function rules()
    {
        return [
            [['numero', 'data_hora', 'valor', 'valor_total', 'operacao_financeira_id', 'movimentacao_financeira_tipo_id'], 'required'],
            [['numero'], 'string'],
            [['data_hora'], 'safe'],
            [['valor', 'valor_total'], 'number'],
            [['operacao_financeira_id', 'movimentacao_financeira_tipo_id'], 'default', 'value' => null],
            [['operacao_financeira_id', 'movimentacao_financeira_tipo_id'], 'integer'],
            [['movimentacao_financeira_tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => MovimentacaoFinanceiraTipo::className(), 'targetAttribute' => ['movimentacao_financeira_tipo_id' => 'id']],
            [['operacao_financeira_id'], 'exist', 'skipOnError' => true, 'targetClass' => OperacaoFinanceira::className(), 'targetAttribute' => ['operacao_financeira_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 26/04/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numero' => 'Numero',
            'data_hora' => 'Data Hora',
            'valor' => 'Valor',
            'valor_total' => 'Valor Total',
            'operacao_financeira_id' => 'Operacao Financeira ID',
            'movimentacao_financeira_tipo_id' => 'Movimentacao Financeira Tipo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public function getMovimentacaoFinanceiraTipo()
    {
        return $this->hasOne(MovimentacaoFinanceiraTipo::className(), ['id' => 'movimentacao_financeira_tipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public function getOperacaoFinanceira()
    {
        return $this->hasOne(OperacaoFinanceira::className(), ['id' => 'operacao_financeira_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public static function find()
    {
        return new MovimentacaoFinanceiraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MovimentacaoFinanceira, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 26/04/2021
*/
class MovimentacaoFinanceiraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['movimentacao_financeira.nome' => $sort_type]);
    }
}
