<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "operacao_financeira".
 *
 * @property integer $id
 * @property string $numero
 * @property string $cliente_cpf_cnpj
 * @property string $cliente_nome
 * @property integer $filial_id
 *
 * @property MovimentacaoFinanceira[] $movimentacaoFinanceiras
 *
 * @author Unknown 26/04/2021
 */
class OperacaoFinanceira extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 26/04/2021
     */
    public static function tableName()
    {
        return 'operacao_financeira';
    }

    /**
     * @inheritdoc
     * @author Unknown 26/04/2021
     */
    public function rules()
    {
        return [
            [['numero', 'filial_id'], 'required'],
            [['numero', 'cliente_cpf_cnpj', 'cliente_nome'], 'string'],
            [['filial_id'], 'default', 'value' => null],
            [['filial_id'], 'integer']
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
            'cliente_cpf_cnpj' => 'Cliente Cpf Cnpj',
            'cliente_nome' => 'Cliente Nome',
            'filial_id' => 'Filial ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public function getMovimentacaoFinanceiras()
    {
        return $this->hasMany(MovimentacaoFinanceira::className(), ['operacao_financeira_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public static function find()
    {
        return new OperacaoFinanceiraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da OperacaoFinanceira, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 26/04/2021
*/
class OperacaoFinanceiraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 26/04/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['operacao_financeira.nome' => $sort_type]);
    }
}
