<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "variacao_tipo".
 *
 * @property integer $id
 * @property string $descricao
 *
 * @property Variacao[] $variacaos
 *
 * @author Unknown 23/06/2021
 */
class VariacaoTipo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 23/06/2021
     */
    public static function tableName()
    {
        return 'variacao_tipo';
    }

    /**
     * @inheritdoc
     * @author Unknown 23/06/2021
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
     * @author Unknown 23/06/2021
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
     * @author Unknown 23/06/2021
    */
    public function getVariacaos()
    {
        return $this->hasMany(Variacao::className(), ['variacao_tipo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/06/2021
    */
    public static function find()
    {
        return new VariacaoTipoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da VariacaoTipo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 23/06/2021
*/
class VariacaoTipoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/06/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['variacao_tipo.descricao' => $sort_type]);
    }
}
