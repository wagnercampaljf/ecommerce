<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "valor_produto_filial".
 *
 * @property integer $id
 * @property double $valor
 * @property string $dt_inicio
 * @property integer $produto_filial_id
 * @property string $dt_fim
 * @property boolean $promocao
 * @property boolean $e_valor_bloqueado
 * @property double $valor_cnpj
 * @property double $valor_compra
 * @property integer $dias_expedicao
 *
 * @property ProdutoFilial $produtoFilial
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ValorProdutoFilial extends \yii\db\ActiveRecord
{

    public $e_valor_bloqueado;
    public $dias_expedicao;

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'valor_produto_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['valor', 'produto_filial_id'], 'required'],
            [
                ['dt_inicio', 'produto_filial_id'],
                'unique',
                'targetAttribute' => ['dt_inicio', 'produto_filial_id'],
                'message' => 'Já existe um preço iniciando na mesma hora para o mesmo produto'
            ],
            [['id', 'produto_filial_id', 'dias_expedicao'], 'integer'],
            [['valor', 'valor_cnpj', 'valor_compra'], 'number', 'min' => 0],
            [['dt_fim'], 'compare', 'compareAttribute' => 'dt_inicio', 'operator' => '>='],
            [['dt_inicio', 'dt_fim'], 'safe'],
            [['promocao', 'e_valor_bloqueado'], 'boolean'],
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
            'valor' => 'Valor',
            'dt_inicio' => 'Data Inicio',
            'produto_filial_id' => 'Produto Filial ID',
            'dt_fim' => 'Data Fim',
            'promocao' => 'Promocao',
            'valor_cnpj' => 'Valor Cnpj',
	    'valor_compra' => 'Valor Compra',
	    'e_valor_bloqueado' => 'É valor bloqueado?',
            'dias_expedicao' => 'Dias de Expedição'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new ValorProdutoFilialQuery(get_called_class());
    }

    /**
     * a label do valor do produto é sempre seu valor, podendo variar entre o comum e o valor para cnpj.
     * Nunca deve retornar nulo!
     * @param bool $juridica se a label deve usar o valor_cnpj ou o valor
     * @author Vinicius Schettino 04/12/2014
     */
    public function getLabel($juridica = false)
    {
        return Yii::$app->formatter->asCurrency($this->getValorFinal($juridica));
    }

    /**
     * Retorna a label, prepara para ser utilizada em títulos (com os centavos em fonte menor)
     * @author Vinicius Schettino 04/12/2014
     */
    public function labelTitulo($juridica = false)
    {
        return str_replace(',', ',<small>', $this->getLabel($juridica)) . '</small>';
    }

    /**
     * Retorna o valor a ser mostrado e utilizado pelos usuários
     * @param bool $juridica usa o valor do cnpj
     * Nunca deve retornar nulo!
     * $juridica só faz efeito se $this->valor_cnpj não for nulo!
     * @author Vinicius Schettino 04/12/2014
     */
    public function getValorFinal($juridica = false)
    {
        $val = $this->valor;
        if ($juridica) {
            $val = $this->valor_cnpj;
        }
        if ($val == '') {
            $val = $this->valor;
        }

        return $val;
    }

    /**
     * @param \yii\db\ActiveRecord $record
     * @return bool
     * @author Igor Mageste
     */
    public function equals($record)
    {
        if ($this->isNewRecord || $record->isNewRecord) {
            $attributes = $this->attributes;
            $attributes_record = $record->attributes;

            foreach ($attributes as $attribute => $value) {
                if ($attribute === 'id') {
                    continue;
                }
                if ($value != $attributes_record[$attribute]) {
                    return false;
                }
            }

            return true;
        }

        return $this->tableName() === $record->tableName() && $this->getPrimaryKey() === $record->getPrimaryKey();
    }

    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        //echo "<pre>"; print_r($insert); echo "<br><br>"; print_r($changedAttributes); echo "<br><br>"; print_r($this); echo "</pre>"; die;

        $produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $this->produto_filial_id])->one();

        echo "<pre>"; print_r($produto_filial->atualizarMLPreco($this->valor)); echo "</pre>"; //die;

	/*$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $this->produto_filial_id])->one();
        //echo  $model->produto_filial_id."<br>".$produto_filial->filial_id;
        $filiais_mesmo_valor = array();
        switch ($produto_filial->filial_id){
            case 8:
                $filiais_mesmo_valor = [94,95,96];
                break;
            case 94:
                $filiais_mesmo_valor = [8,95,96];
                break;
            case 95:
                $filiais_mesmo_valor = [8,94,96];
                break;
            case 96:
                $filiais_mesmo_valor = [8,94,95, 87, 88, 89, 90];
                break;
        }
        
        foreach($filiais_mesmo_valor as $k => $filial_mesmo_valor_id){
            //echo "<br>Filial: ".$filial_mesmo_valor_id;
            $produto_filial_mesmo_valor = ProdutoFilial::find() ->andWhere(["=", "produto_id", $produto_filial->produto_id])
            ->andWhere(["=", "filial_id", $filial_mesmo_valor_id])
            ->one();
            
            if($produto_filial_mesmo_valor){
                $valor_produto_filial_mesmo_valor                       = new ValorProdutoFilial();
                $valor_produto_filial_mesmo_valor->produto_filial_id    = $produto_filial_mesmo_valor->id;
                $valor_produto_filial_mesmo_valor->valor                = $this->valor;
                $valor_produto_filial_mesmo_valor->valor_cnpj           = $this->valor_cnpj;
                $valor_produto_filial_mesmo_valor->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial_mesmo_valor->promocao             = $this->promocao;
                $valor_produto_filial_mesmo_valor->valor_compra         = $this->valor_compra;
                $valor_produto_filial_mesmo_valor->save();
            }
        }*/

	$atributos              = json_encode($this->attributes);
        Log::registrarLog($atributos, "valor_produto_filial", $this->id, 1, ($insert) ? 1 : 2);

    }

    public function afterDelete()
    {
        parent::afterDelete();

        $atributos              = json_encode($this->attributes);

        Log::registrarLog($atributos, "valor_produto_filial", $this->id, 1, 3);

    }

}

/**
 * Classe para contenção de escopos da ValorProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class ValorProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Maior Valor de um produto
     * Se o produto possui dois ou mais ValorProdutoFiliais ativos, o mais recente será utilizado
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 04/12/2014
     * alteração para receber tambem os valores dos produtos com 0 em estoque
     * Otavio 11/08/2016
     */
    public function maiorValorProduto($id, $juridica = false, $op = '>')
    {
        $val = 'valor_produto_filial.valor';
        if ($juridica) {
            $val = ('coalesce(valor_produto_filial.valor_cnpj, valor_produto_filial.valor)');
        }

        return $this
            ->innerJoinWith(['produtoFilial.filial.lojista'], true)
            ->andWhere(['lojista.ativo' => true])
            ->andWhere(['produto_filial.produto_id' => $id])
            ->andWhere('produto_filial.quantidade '. $op .' 0')
            ->orderBy([$val => SORT_DESC])->limit(1);
    }

    /**
     * Menor Valor de um produto
     * Se o produto possui dois ou mais ValorProdutoFiliais ativos, o mais recente será utilizado
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 04/12/2014
     * alteração para receber tambem os valores dos produtos com 0 em estoque
     * Otavio 11/08/2016
     */
    public function menorValorProduto($id, $juridica = false, $op = '>')
    {
        $val = 'valor_produto_filial.valor';
        if ($juridica) {
            $val = ('coalesce(valor_produto_filial.valor_cnpj, valor_produto_filial.valor)');
        }
        
        /*$command = $this
            ->innerJoinWith(['produtoFilial.filial.lojista'], true)
            ->andWhere(['lojista.ativo' => true])
            ->andWhere(['produto_filial.produto_id' => $id])
            ->andWhere('produto_filial.quantidade '. $op .' 0')
            ->orderBy([$val => SORT_ASC])->limit(1)
            ->createCommand();
        // mostra a instrução SQL
        echo $command->sql;
        print_r($command->params);
        die;*/

        return $this
            ->innerJoinWith(['produtoFilial.filial.lojista'], true)
            ->andWhere(['lojista.ativo' => true])
            ->andWhere(['produto_filial.produto_id' => $id])
            ->andWhere('produto_filial.quantidade '. $op .' 0')
            ->orderBy([$val => SORT_ASC])->limit(1);
    }

    /**
     * Apenas o valor ativo (na data de referencia passada) do produto em determinada filial.
     * @param null $dt_ref data formato timestamp. Se vazio, será considerado now();
     * @author Vinicius Schettino 04/12/2014
     * @return static
     */
    public function ativo($dt_ref = null)
    {
        if (is_null($dt_ref)) {
            $dt_ref = date('Y-m-d H:i:s');
        }
        $subquery = '(SELECT dt_inicio FROM valor_produto_filial WHERE valor_produto_filial.produto_filial_id = "produto_filial".id
                      AND valor_produto_filial.dt_inicio <= \'' . $dt_ref . '\'
                      AND (valor_produto_filial.dt_fim >=\'' . $dt_ref . '\' OR valor_produto_filial.dt_fim IS NULL)
                      ORDER BY dt_inicio
                      DESC limit 1)';

        return $this
            ->innerJoinWith('produtoFilial', false)
            ->andWhere('valor_produto_filial.dt_inicio <= \'' . $dt_ref . '\'')
            ->andWhere('(valor_produto_filial.dt_fim >=\'' . $dt_ref . '\' OR valor_produto_filial.dt_fim IS NULL)')
            ->andWhere('valor_produto_filial.dt_inicio >= ' . $subquery);
    }

    /**
     * Apenas o valor ativo (na data de referencia passada) do produto em determinada filial.
     * @param null $dt_ref data formato timestamp. Se vazio, será considerado now();
     * @author Vinicius Schettino 04/12/2014
     * @return static
     */
    public function recente($dt_ref = null)
    {
        if (is_null($dt_ref)) {
            $dt_ref = date('Y-m-d H:i:s');
        }

        return $this
            ->innerJoinWith('produtoFilial', true)
            ->andWhere('valor_produto_filial.dt_inicio <= \'' . $dt_ref . '\'')
            ->andWhere('(valor_produto_filial.dt_fim >=\'' . $dt_ref . '\' OR valor_produto_filial.dt_fim IS NULL)');
    }

    /**
     * Retorna o valor mais recente de um produtoFilial
     * O valor retornado não é necessariamente válido, ou seja, sua dt_fim pode ser menor do que a data atual
     * @return static
     * @author Vitor Horta 02/07/2015
     */
    public function maisRecente()
    {
        //return $this->orderBy('dt_fim DESC');
        return $this->orderBy('dt_fim DESC');
    }

    public function byProdutoFilial($produtoFilial_id)
    {
        return $this->andWhere(['produto_filial_id' => $produtoFilial_id]);
    }

    public function byCategoria($categoria_id)
    {
        if (is_null($categoria_id)) {
            return $this;
        }

        return $this->joinWith(['produtoFilial.produto.subcategoria.categoria'])->andWhere(['categoria.id' => $categoria_id]);
    }

    public function bySubcategoria($subcategoria_id)
    {
        if (is_null($subcategoria_id)) {
            return $this;
        }

        return $this->joinWith(['produtoFilial.produto.subcategoria'])->andWhere(['subcategoria.id' => $subcategoria_id]);
    }
}
