<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "carrinho".
 *
 * @property integer $id
 * @property string $chave
 * @property string $dt_criacao
 * @property string $dt_fechamento
 * @property integer $comprador_id
 *
 * @property CarrinhoProdutoFilial[] $carrinhoProdutoFilials
 * @property Comprador $comprador
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Carrinho extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'carrinho';
    }

    /**
     * Retorna string padrao para nome do carrinho
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/12/2015
     */
    public static function nomeCarrinho()
    {
        $data = date("Y-m-d h:i:s");

        return 'Carrinho ' . \Yii::$app->formatter->asDate($data);
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['comprador_id'], 'integer'],
            [['dt_criacao', 'dt_fechamento'], 'safe'],
            [['chave'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chave' => 'Nome Carrinho',
            'dt_criacao' => 'Data Criação',
            'dt_fechamento' => 'Data Fechamento',
            'comprador_id' => 'Comprador ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getCarrinhoProdutoFilials()
    {
        return $this->hasMany(CarrinhoProdutoFilial::className(), ['carrinho_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getComprador()
    {
        return $this->hasOne(Comprador::className(), ['id' => 'comprador_id']);
    }

    /**
     * Calcula e retorna valor total de um carrinho
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/20/2015
     */
    public function getValorTotal()
    {
        $carrinhoProdutoFilials = CarrinhoProdutoFilial::find()->where(['carrinho_id' => $this->id])->with(['produtoFilial', 'produtoFilial.valorProdutoFilials'])->all();
        $valor = 0;
        foreach ($carrinhoProdutoFilials as $carrinhoProdutoFilial) {
            $valor += $carrinhoProdutoFilial->produtoFilial->getValorProdutoFilials()->ativo()->one()->getValorFinal(Yii::$app->params['isJuridica']());
        }

        return $valor;
    }
    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new CarrinhoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Carrinho, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class CarrinhoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['carrinho.nome' => $sort_type]);
    }
}
