<?php

namespace frontend\models;

use Yii;

/**
 * Este é o model para a tabela "markup_mestre".
 *
 * @property integer $id
 * @property string $descricao
 * @property string $data_inicio
 * @property string $observacao
 * @property boolean $e_margem_absoluta_padrao
 * @property string $valor_minimo_padrao
 * @property string $valor_maximo_padrao
 * @property string $margem_padrao
 *
 * @property MarkupDetalhe[] $markupDetalhes
 *
 * @author Unknown 29/01/2021
 */
class MarkupMestre extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/01/2021
     */
    public static function tableName()
    {
        return 'markup_mestre';
    }

    /**
     * @inheritdoc
     * @author Unknown 29/01/2021
     */
    public function rules()
    {
        return [
            [['data_inicio', 'e_margem_absoluta_padrao', 'margem_padrao', 'e_markup_padrao'], 'required'],
            [['data_inicio'], 'safe'],
            [['observacao'], 'string'],
            [['e_margem_absoluta_padrao', 'e_markup_padrao'], 'boolean'],
            [['margem_padrao'], 'number'],
            [['descricao'], 'string', 'max' => 200]
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
            'descricao' => 'Descricao',
            'data_inicio' => 'Data Inicio',
            'observacao' => 'Observacao',
            'e_margem_absoluta_padrao' => 'E Margem Absoluta Padrao',
            'valor_minimo_padrao' => 'Valor Minimo Padrao',
            'valor_maximo_padrao' => 'Valor Maximo Padrao',
            'margem_padrao' => 'Margem Padrao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public function getMarkupDetalhes()
    {
        return $this->hasMany(MarkupDetalhe::className(), ['markup_mestre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public static function find()
    {
        return new MarkupMestreQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MarkupMestre, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/01/2021
*/
class MarkupMestreQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/01/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['markup_mestre.nome' => $sort_type]);
    }
}
