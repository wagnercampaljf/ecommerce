<?php

namespace common\models;
use yii\db\ActiveRecord;
use Yii;

/**
 * Este é o model para a tabela "planilha_preco".
 *
 * @property string $path
 * @property integer $file_planilha
 * @property integer $coluna_codigo_fabricante
 * @property string $coluna_estoque
 * @property integer $coluna_preco
 * @property integer $coluna_preco_compra
 *
 * @author Unknown 29/06/2021
 */
class PlanilhaPreco extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/06/2021
     */
    public static function tableName()
    {
        return 'planilha_preco';
    }
    public $file_planilha;
    public $coluna_codigo_fabricante;
    public $coluna_estoque;
    public $coluna_preco;
    public $coluna_preco_compra;
    /**
     * @inheritdoc
     * @author Unknown 29/06/2021
     */
    public function rules()
    {
        return [
            [['file_planilha'], 'required', 'on' => ['create']],
            [['coluna_codigo_fabricante','coluna_estoque','coluna_preco', 'coluna_preco_compra'], 'integer'],
            [['path'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 29/06/2021
     */
    public function attributeLabels()
    {
        return [
            'path' => 'Path',
            'file_planilha' => 'File',

            'coluna_codigo_fabricante' => 'Coluna Codigo do Fabricante',

            'coluna_estoque' => 'Coluna Estoque',

            'coluna_preco' => 'Coluna Preço',
            'coluna_preco_compra' =>'Coluna Preço Compra'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/06/2021
    */
    public static function find()
    {
        return new PlanilhaPrecoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PlanilhaPreco, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/06/2021
*/
class PlanilhaPrecoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/06/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['planilha_preco.nome' => $sort_type]);
    }
}
