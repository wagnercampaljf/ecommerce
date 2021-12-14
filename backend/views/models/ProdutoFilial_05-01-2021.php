<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_filial".
 *
 * @property integer $id
 * @property integer $produto_id
 * @property integer $filial_id
 * @property integer $quantidade
 * @property integer $meli_id
 * @property integer $meli_id_sem_juros
 * @property integer $meli_id_full
 * @property boolean $status_b2w
 * @property integer $envio
 * @property boolean $atualizar_preco_mercado_livre
 *
 * @property ValorProdutoFilial[] $valorProdutoFilials
 * @property PedidoProdutoFilial[] $pedidoProdutoFilials
 * @property Pedido[] $pedidos
 * @property Produto $produto
 * @property Filial $filial
 * @property CarrinhoProdutoFilial $carrinhoProdutoFilial
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'produto_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_id', 'filial_id', 'quantidade'], 'required'],
            [['produto_id', 'filial_id', 'quantidade','envio'], 'integer'],
            [['meli_id', 'meli_id_sem_juros', 'meli_id_full', 'status_b2w', 'atualizar_preco_mercado_livre'], 'safe'],
	    //[['uni_produto_id_filial_id_produto_filial_origem_id'],'unique'],
	    ['envio', 'default', 'value'=>1]
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
            'produto_id' => 'Produto ID',
            'filial_id' => 'Filial ID',
            'quantidade' => 'Quantidade',
            'envio' => 'Tipo de envio',
	    'atualizar_preco_mercado_livre' => 'Atualizar preço no Mercado Livre?'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getValorProdutoFilials()
    {
        return $this->hasMany(ValorProdutoFilial::className(), ['produto_filial_id' => 'id']);
    }

    public function getValorAtual()
    {
        return $this->hasOne(ValorProdutoFilial::className(),
            ['produto_filial_id' => 'id'])->ativo();
    }

    /**
     * Relação com ValorProdutoFilial mais recente
     * @return mixed
     * @author Vitor Horta 02/07/2015
     */
    public function getValorMaisRecente()
    {
        return $this->hasOne(ValorProdutoFilial::className(),['produto_filial_id' => 'id'])->ativo();
    }

    public function getValor()
    {
        return $this->valorMaisRecente->valor;
    }

    public function getValorMercadoLivre()
    {	
	//$valorml = $this->valorMaisRecente->valor * 1.074;
	//$valorml = $this->valorMaisRecente->valor * 1.09; //20-08-2020
	$valorml = $this->valorMaisRecente->valor * 1.11; //21-08-2020
	/*if($valorml>=120){
		return $valorml+10;	
	} else{
        	return $valorml+5;
	}*/
	//20-08-2020
	/*if($valorml>=500){
    		return $valorml+10;
    	} elseif($valorml<=109){
            	return $valorml+5;
    	} elseif($valorml > 109 and $valorml < 500){
    	    	return $valorml+16;
    	}*/
	if($valorml>=500){
                return $valorml+11;
        } elseif($valorml<=91){
                return $valorml+6;
        } elseif($valorml > 91 and $valorml < 500){
                return $valorml+25;
        }
    }

    public function getValorSkyhub()
    {
        //return $this->valorMaisRecente->valor * 1.16;
	return $this->valorMaisRecente->valor * 1.2;
    }

    public function getValor_cnpj()
    {
        return $this->valorMaisRecente->valor_cnpj;
    }

    public function getEstoque()
    {
        return $this->quantidade;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedidoProdutoFilials()
    {
        return $this->hasMany(PedidoProdutoFilial::className(), ['produto_filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::className(), ['id' => 'pedido_id'])->viaTable(
            'pedido_produto_filial',
            ['produto_filial_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getLojista()
    {
        return $this->hasOne(Lojista::className(), ['id' => 'lojista_id'])->viaTable(
            'filial',
            ['id' => 'filial_id']
        );
    }

    /**
     * Retorna um array no formato correto para integrar com a api do skyhub
     * @return array
     * @author Tércio Garcia 06/09/2017
     */

    public function getSkyhubDataPrecoQuantidade()
    {
        $product['sku'] = $this->id;
        $product["name"] = $this->produto->nome;
        $product["description"] = $this->produto->codigo_similar .'<br>'. $this->produto->aplicacao.'<br>'.$this->produto->aplicacao_complementar;
//        if ($this->produto->descricao) {
//            $product["description"] = $this->produto->descricao;
//        } else {
//            $product["description"] = $this->produto->subcategoria->descricao;
//        }
        $product["qty"] = $this->quantidade;
        $product["status"] = 'enabled';
        $product["garantia"] = '3 Meses';
        $product["price"] = round($this->getValorSkyhub(), 2);
        $product["promotional_price"] = round($this->getValorSkyhub(), 2);
        $product["weight"] = $this->produto->peso;
        $product["height"] = $this->produto->altura;
        $product["width"] = $this->produto->largura;
        $product["length"] = $this->produto->profundidade;
	$product["brand"] = "Peça Agora";
        $product["nbm"] = $this->produto->codigo_montadora;
        $product["ean"] = "";//$this->produto->codigo_barras;
        $product["categories"] = [
            [
                'code' => $this->produto->subcategoria->id,
                'name' => 'Automotivo > Autopeças > ' . $this->produto->subcategoria->categoria->nome . ' > ' . $this->produto->subcategoria->nome,
            ]
        ];
        //$product["images"] = $this->produto->getUrlImages(false);
        //$product["images"] = $this->produto->getUrlImagesB2WComLogo();
        $product["images"] = $this->produto->getUrlImagesB2WWebp();
        $product["specifications"] = [
            [
                'key' => 'aplicacao',
                'value' => $this->produto->codigo_similar . '<br>'
                    . $this->produto->aplicacao . '<br>'
                    . $this->produto->aplicacao_complementar,
	    ]
        ];
        return ['product' => $product];
    }

    public function getSkyhubData()
    {

	$preco = round($this->getValorSkyhub(), 2);

	$quantidade = 0;
	if($preco <= 300){
		$quantidade = $this->quantidade;
	}

	$descricao = $this->produto->codigo_similar .'<br>'. $this->produto->aplicacao.'<br>'.$this->produto->aplicacao_complementar;
        $product['sku'] 		= $this->id;
        $product["name"] 		= $this->produto->nome;
        $product["description"] 	= $descricao;
        $product["qty"] 		= $quantidade;
        $product["status"] 		= 'enabled';
	$product["garantia"] 		= '3 Meses';
        $product["price"]		= $preco;
        $product["promotional_price"] 	= $preco;
        $product["weight"] 		= $this->produto->peso;
        $product["height"] 		= $this->produto->altura;
        $product["width"] 		= $this->produto->largura;
        $product["length"] 		= $this->produto->profundidade;
        $product["brand"] 		= "Peça Agora";
        $product["nbm"] 		= $this->produto->codigo_montadora;
        $product["ean"] 		= "";//$this->produto->codigo_barras;
        $product["categories"] 		= [
						[
				                'code' => $this->produto->subcategoria->id,
				                'name' => 'Automotivo > Autopeças > ' . $this->produto->subcategoria->categoria->nome . ' > ' . $this->produto->subcategoria->nome,
					        ]
				        ];
        //$product["images"] = $this->produto->getUrlImages(false);
	//$product["images"] = $this->produto->getUrlImagesB2WComLogo();
	$product["images"] = $this->produto->getUrlImagesB2WWebp();
        $product["specifications"] 	= [
						[
				                'key' => 'aplicacao',
				                'value' => $this->produto->codigo_similar . '<br>' . $this->produto->aplicacao . '<br>' . $this->produto->aplicacao_complementar,
						]
					  ];
//echo 444444;
	//print_r($product);
        return ['product' => $product];
    }


    public function getImagens()
    {
        return $this->hasMany(Imagens::className(), ['produto_id' => 'produto_id']);
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
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getCarrinhoProdutoFilial()
    {
        return $this->hasOne(CarrinhoProdutoFilial::className(), ['produto_filial_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new ProdutoFilialQuery(get_called_class());
    }

}

/**
 * Classe para contenção de escopos da ProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_filial.nome' => $sort_type]);
    }

    public function produtoDisponivel()
    {
        return $this->andWhere('produto_filial.quantidade > 0');
    }

    public function byIds($ids)
    {
        return $this->andWhere(['id' => $ids]);
    }

    public function byProduto($id)
    {
        return $this->andWhere(['produto_filial.produto_id' => $id]);
    }

    public function byFilial($id)
    {
        return $this->andWhere(['produto_filial.filial_id' => $id]);
    }

    public function lojistaAtivo()
    {
        return $this->joinWith(['lojista'])->andWhere(['lojista.ativo' => true]);
    }

    public function byCodGlobal($cod_global)
    {
        return $this->joinWith('produto')->andWhere(['produto.codigo_global' => $cod_global]);
    }

    public function byCodFabricante($cod_fabricante)
    {
        return $this->joinWith('produto')->andWhere(['produto.codigo_fabricante' => $cod_fabricante]);
    }

    public function comValorRecente()
    {
        return $this->innerJoin('(SELECT DISTINCT ON (valor_produto_filial.produto_filial_id) valor_produto_filial.produto_filial_id AS a ,valor_produto_filial.*, MAX (dt_fim)
        	FROM
        	"valor_produto_filial" 
        	GROUP BY 
        	valor_produto_filial. ID, valor_produto_filial.produto_filial_id) "valor_produtoFilial" ON "valor_produtoFilial"."produto_filial_id" = "produto_filial"."id"');
    }

    public function hasImage()
    {
        return $this->joinWith('imagens')->andWhere(['IS NOT', 'imagens.imagem_sem_logo',NULL]);
    }
}
