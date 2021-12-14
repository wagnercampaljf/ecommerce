<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_ano_modelo".
 *
 * @property integer $produto_id
 * @property string $ano_modelo_id
 *
 * @property Produto $produto
 * @property AnoModelo $anoModelo
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoAnoModelo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'produto_ano_modelo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_id', 'ano_modelo_id'], 'required'],
            [['produto_id'], 'integer'],
            [['ano_modelo_id'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'produto_id' => 'Produto ID',
            'ano_modelo_id' => 'Ano Modelo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getAnoModelo()
    {
        return $this->hasOne(AnoModelo::className(), ['id' => 'ano_modelo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new ProdutoAnoModeloQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ProdutoAnoModelo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class ProdutoAnoModeloQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_ano_modelo.nome' => $sort_type]);
    }
}
