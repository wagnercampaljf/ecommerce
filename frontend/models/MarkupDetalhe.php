<?php

namespace frontend\models;

use Yii;

/**
 * Este é o model para a tabela "markup_detalhe".
 *
 * @property integer $id
 * @property integer $markup_mestre_id
 * @property boolean $e_margem_absoluta
 * @property string $valor_minimo
 * @property string $valor_maximo
 * @property string $margem
 *
 * @property MarkupMestre $markupMestre
 *
 * @author Unknown 29/01/2021
 */
class MarkupDetalhe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/01/2021
     */
    public static function tableName()
    {
        return 'markup_detalhe';
    }

    /**
     * @inheritdoc
     * @author Unknown 29/01/2021
     */
    public function rules()
    {
        return [
            [['markup_mestre_id', 'e_margem_absoluta', 'valor_minimo', 'valor_maximo', 'margem'], 'required'],
            [['markup_mestre_id'], 'integer'],
            [['e_margem_absoluta'], 'boolean'],
            [['valor_minimo', 'valor_maximo', 'margem'], 'number'],
            [['markup_mestre_id'], 'exist', 'skipOnError' => true, 'targetClass' => MarkupMestre::className(), 'targetAttribute' => ['markup_mestre_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 29/01/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'markup_mestre_id' => 'Markup Mestre ID',
            'e_margem_absoluta' => 'E Margem Absoluta',
            'valor_minimo' => 'Valor Minimo',
            'valor_maximo' => 'Valor Maximo',
            'margem' => 'Margem',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public function getMarkupMestre()
    {
        return $this->hasOne(MarkupMestre::className(), ['id' => 'markup_mestre_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public static function find()
    {
        return new MarkupDetalheQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MarkupDetalhe, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/01/2021
*/
class MarkupDetalheQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['markup_detalhe.nome' => $sort_type]);
    }
}
