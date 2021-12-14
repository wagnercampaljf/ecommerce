<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "valor_produto_menor_maior".
 *
 * @property integer $id
 * @property integer $produto_id
 * @property double $menor_valor
 * @property double $maior_valor
 * @property double $menor_valor_cnpj
 * @property double $maior_valor_cnpj
 *
 * @property Produto $produto
 *
 * @author Unknown 15/08/2018
 */
class ValorProdutoMenorMaior extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 15/08/2018
     */
    public static function tableName()
    {
        return 'valor_produto_menor_maior';
    }

    /**
     * @inheritdoc
     * @author Unknown 15/08/2018
     */
    public function rules()
    {
        return [
            [['produto_id'], 'required'],
            [['produto_id'], 'integer'],
            [['menor_valor', 'maior_valor', 'menor_valor_cnpj', 'maior_valor_cnpj'], 'number'],
            [['produto_id'], 'unique'],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 15/08/2018
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_id' => 'Produto ID',
            'menor_valor' => 'Menor Valor',
            'maior_valor' => 'Maior Valor',
            'menor_valor_cnpj' => 'Menor Valor Cnpj',
            'maior_valor_cnpj' => 'Maior Valor Cnpj',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/08/2018
    */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/08/2018
    */
    public static function find()
    {
        return new ValorProdutoMenorMaiorQuery(get_called_class());
    }
    
    public function labelTituloMenor($juridica = false)
    {
        return str_replace(',', ', <small>', $this->getLabelMenor($juridica)) . '</small>';
    }
    
    public function getLabelMenor($juridica = false)
    {
        return Yii::$app->formatter->asCurrency($this->getValorFinalMenor($juridica));
    }
    
    public function getValorFinalMenor($juridica = false)
    {
        $val = $this->valor;
        if ($juridica) {
            $val = $this->menor_valor_cnpj;
        }
        if ($val == '') {
            $val = $this->menor_valor;
        }
        
        return $val;
    }
    
    public function labelTituloMaior($juridica = false)
    {
        return str_replace(',', ', <small>', $this->getLabelMaior($juridica)) . '</small>';
    }
    
    public function getLabelMaior($juridica = false)
    {
        return Yii::$app->formatter->asCurrency($this->getValorFinalMaior($juridica));
    }
    
    public function getValorFinalMaior($juridica = false)
    {
        $val = $this->valor;
        if ($juridica) {
            $val = $this->maior_valor_cnpj;
        }
        if ($val == '') {
            $val = $this->maior_valor;
        }
        
        return $val;
    }
    

}

/**
 * Classe para contenção de escopos da ValorProdutoMenorMaior, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 15/08/2018
*/
class ValorProdutoMenorMaiorQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/08/2018
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['valor_produto_menor_maior.nome' => $sort_type]);
    }
}
