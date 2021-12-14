<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "movimentacao_financeira_tipo".
 *
 * @property integer $id
 * @property string $descricao
 *
 * @property MovimentacaoFinanceira[] $movimentacaoFinanceiras
 *
 * @author Unknown 23/04/2021
 */
class MovimentacaoFinanceiraTipo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 23/04/2021
     */
    public static function tableName()
    {
        return 'movimentacao_financeira_tipo';
    }

    /**
     * @inheritdoc
     * @author Unknown 23/04/2021
     */
    public function rules()
    {
        return [
            [['descricao'], 'required'],
            [['descricao'], 'string']
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 23/04/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/04/2021
    */
    public function getMovimentacaoFinanceiras()
    {
        return $this->hasMany(MovimentacaoFinanceira::className(), ['movimentacao_financeira_tipo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/04/2021
    */
    public static function find()
    {
        return new MovimentacaoFinanceiraTipoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MovimentacaoFinanceiraTipo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 23/04/2021
*/
class MovimentacaoFinanceiraTipoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/04/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['movimentacao_financeira_tipo.nome' => $sort_type]);
    }
}
