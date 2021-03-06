<?php
//2222
namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;

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
	//echo "<pre>"; print_r($this->valorMaisRecente->valor); echo "</pre>"; //die;
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
	//return $this->valorMaisRecente->valor * 1.2;
        return $this->valorMaisRecente->valor * 1.25;

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
        $product["qty"] = 0;//$this->quantidade;
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
        $product["qty"] 		= 0;//$quantidade;
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


    public function atualizarMLDiasExpedicao(){

        $retorno = array();

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'id', $this->id])->orderBy(["filial_id" => SORT_ASC])->all();

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');

        foreach($produtos_filiais as $j => $produto_filial){

            if($produto_filial->produto->dias_expedicao <= 0 || $produto_filial->produto->dias_expedicao == null){
        	    $retorno[$produto_filial->id]["meli_id_status"] = "Dias de Expedição não alterado";
        	    return $retorno;
	    }
            $dias = $produto_filial->produto->dias_expedicao;

	    echo "(*".$dias."*)";

	    $body = [
	            //"category_id" => utf8_encode($subcategoriaMeli),
	            "sale_terms" => [[
	                "id"            => "MANUFACTURING_TIME",
	                "name"          => "Disponibilidade de estoque",
	                "value_id"      => null,
	                "value_name"    => $dias." dias",
	                "value_struct"  =>  [[
	                    "number"    => $dias,
	                    "unit"      => "dias"
	                ]],
	                "values"    =>  [[
	                    "id"        => null,
	                    "name"      => $dias." dias",
	                    "struct"    =>  [
	                        "number"    => $dias,
	                        "unit"      => "dias"
	                    ]
	                ]]
	            ]]
	        ];

            $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
            //echo "\n".$j." - ".$produto_filial->id." - ".$produto_filial->filial_id;

            if(is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == ""){
                echo " - Filial fora do ML";
                continue;
            }

            $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
            $response           = ArrayHelper::getValue($user, 'body');
            $meliAccessToken    = $response->access_token;

            if(!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> ""){
                $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
                $response = $meli->put("items/{$produto_filial->meli_id}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Dias de Expedição não alterado";
                    echo "\nDias de Expedição não alterado (MELI_ID)";
                }
                else {
                    $retorno[$produto_filial->id]["meli_id_status"] = "Dias de Expedição alterado";
                    echo "\nDias de Expedição (MELI_ID)";
                }
                
            }
            if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Dias de Expedição não alterado";
                    echo "\nDias de Expedição (MELI_ID_SEM_JUROS)";
                }
                else {
                    $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "Dias de Expedição alterado";
                    echo "\nDias de Expedição (MELI_ID_SEM_JUROS)";
                }
            }
            if(!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> ""){
                $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
                $response = $meli->put("items/{$produto_filial->meli_id_full}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Dias de Expedição não alterado";
                    echo "\nDias de Expedição (MELI_ID_FULL)";
                }
                else {
                    $retorno[$produto_filial->id]["meli_id_full_status"] = "Dias de Expedição alterado";
                    echo "\nDias de Expedição (MELI_ID_FULL)".$response["body"]->permalink;
                }
            }
            if(!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> ""){
                $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
                $response = $meli->put("items/{$produto_filial->meli_id_flex}?api_version=2&access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Dias de Expedição não alterado";
                    echo "\nDias de Expedição não alterado (MELI_ID_FLEX)";
                }
                else {
                    $retorno[$produto_filial->id]["meli_id_flex_status"] = "Dias de Expedição alterado";
                    echo "\nDias de Expedição alterado (MELI_ID_FLEX)";
                }
            }
        }
        
        return $retorno;
    }


    public function atualizarMLPreco($valor = null){

	if(is_null($this->meli_id)){
		return "Produto fora do ML";
	}

	if(is_null($this->filial->refresh_token_meli)){
                return "Filial fora do ML";
        }

        $meli_id_array          = array();
        $produtos_mercado_livre = array();
        
        if(is_null($valor)){

	    $produto_filial_id = $this->id;
    	    if(!is_null($this->produto_filial_origem_id)){
                $produto_filial_origem = ProdutoFilial::find()->andWhere(["=", "id", $this->produto_filial_origem_id])->one();
                if($produto_filial_origem){
                    $produto_filial_id = $produto_filial_origem->id;
        		}
                else{
                    return "Origem não encontrada!";
        		}
    	    }

            $valor_produto_filial = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $this->id])->orderBy(["id" => SORT_DESC])->one();
	    if($valor_produto_filial){
            	$valor = $valor_produto_filial->valor;
	    }
	    else{
		return "Sem valor cadastrado!";
	    }
        }

        echo "(".$valor.")";
        $valor_principal    = $valor * 1.11;
	echo "(".$valor_principal.")";
        $valor_sem_juros    = $valor * 1.17;
        $valor_full         = $valor * 1.18;

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
        
        $filial = Filial::find()->andWhere(['=','id',$this->filial_id])->one();
        //$filial = Filial::find()->andWhere(['=', 'id', 98])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        //print_r($meliAccessToken); die;

        $valor_envio = 0;

        if(!is_null($this->meli_id) && $this->meli_id <> ""){
            $meli_id_array[] = $this->meli_id;
            $produtos_mercado_livre[] = ["token" => $meliAccessToken, "meli_id" => $this->meli_id, "tipo" => "meli_id"];
        }
        if(!is_null($this->meli_id_sem_juros) && $this->meli_id_sem_juros <> ""){
            $meli_id_array[] = $this->meli_id_sem_juros;
            $produtos_mercado_livre[] = ["token" => $meliAccessToken, "meli_id" => $this->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
        }
        if(!is_null($this->meli_id_full) && $this->meli_id_full <> ""){
            $meli_id_array[] = $this->meli_id_full;
            $produtos_mercado_livre[] = ["token" => $meliAccessToken, "meli_id" => $this->meli_id_full, "tipo" => "meli_id_full"];
        }

        $produtos_filial_duplicado = ProdutoFilial::find()->andWhere(["=", "produto_filial_origem_id", $this->id])->all();
        foreach($produtos_filial_duplicado as $produto_filial_duplicado){

            $filial_conta_duplicada = Filial::find()->andWhere(['=','id',$produto_filial_duplicado->filial_id])->one();
            $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

            if(!is_null($produto_filial_duplicado->meli_id) && $produto_filial_duplicado->meli_id <> ""){
                $meli_id_array[] = $produto_filial_duplicado->meli_id;
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id, "tipo" => "meli_id"];
            }
            if(!is_null($produto_filial_duplicado->meli_id_sem_juros) && $produto_filial_duplicado->meli_id_sem_juros <> ""){
                $meli_id_array[] = $produto_filial_duplicado->meli_id_sem_juros;
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
            }
            if(!is_null($produto_filial_duplicado->meli_id_full) && $produto_filial_duplicado->meli_id_full <> ""){
                $meli_id_array[] = $produto_filial_duplicado->meli_id_full;
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_full, "tipo" => "meli_id_full"];
            }
        }

        $meli_id_texto = "'111',";
	foreach($meli_id_array as $meli_id_venda){
		$meli_id_texto .= "'".$meli_id_venda."',";
	}
	$meli_id_texto = rtrim($meli_id_texto, ",");
	//echo " shipping_option_list_cost is not null and shipping_option_list_cost <> shipping_option_cost and produto_meli_id in (".$meli_id_texto.") ";

        //$pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(["produto_meli_id" => $meli_id_array])->orderBy(["id" => SORT_DESC])->one();
        $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()  ->join("INNER JOIN", "pedido_mercado_livre", "pedido_mercado_livre.id = pedido_mercado_livre_id")
                                                                           //->andWhere(["produto_meli_id" => $meli_id_array])
                                                                           //->andWhere(["is not", "shipping_option_list_cost", null])
									   ->where(" shipping_option_list_cost is not null and shipping_option_list_cost <> shipping_option_cost and produto_meli_id in (".$meli_id_texto.") ")
                                                                           ->orderBy(["shipping_option_list_cost" => SORT_DESC])
                                                                           ->one();
        
        //print_r($pedido_mercado_livre_produto->pedidoMercadoLivre); echo "\n\n";
        //print_r($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_list_cost); echo "\n\n";
        //print_r($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_cost); echo "\n\n";
        //print_r($pedido_mercado_livre_produto->quantity);
        //die;

        if($pedido_mercado_livre_produto){

            //$pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "id", $pedido_mercado_livre_produto->pedido_mercado_livre_id])->one();
            //$valor_envio = ($pedido_mercado_livre_produto->pedido_mercado_livre->shipping_option_list_cost * 1.2) - ($pedido_mercado_livre_produto->pedido_mercado_livre->shipping_option_cost);

            $quantidade = 0;
            $pedido_mercado_livre_produto_quantidades = PedidoMercadoLivreProduto::find()   ->andWhere(["=", "pedido_mercado_livre_id", $pedido_mercado_livre_produto->pedido_mercado_livre_id])
                                                                                            ->all();
            foreach($pedido_mercado_livre_produto_quantidades as $pedido_mercado_livre_produto_quantidade){
                $quantidade +=  $pedido_mercado_livre_produto_quantidade->quantity;
            }
            
            $valor_envio = ($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_list_cost * 1.2) - ($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_cost);
            $valor_envio /= $quantidade;//$pedido_mercado_livre_produto->quantity;
            $valor_envio = ($valor_envio < 0) ? ($valor_envio * (-1)) : $valor_envio;
        
        }
        else{
            if($valor >= 500){
        		if($valor_envio < 11){
        	       $valor_envio = 11;
        		}
            } 
            elseif($valor<=65){
                if($valor_envio < 6){
        	       $valor_envio = 6;
                }
            } 
            elseif($valor > 65 and $valor < 500){
                if($valor_envio < 25){
                   $valor_envio = 25;
                }
            }
        }

        if($valor >= 500){
                if($valor_envio < 11){
                       	$valor_envio = 11;
                }
        } elseif($valor<=65){
               	if($valor_envio < 6){
                       	$valor_envio = 6;
               	}
        } elseif($valor > 65 and $valor < 500){
               	if($valor_envio < 25){
                       	$valor_envio = 25;
        	}
        }

        echo "(".$valor_principal.")";
        $valor_principal    += $valor_envio;
        $valor_sem_juros    += $valor_envio;
        $valor_full         += $valor_envio;
        
        foreach($produtos_mercado_livre as $k => $produto_mercado_livre){
            
            $valor_final = $valor_principal;
            switch ($produto_mercado_livre["tipo"]) {
                case "meli_id":
                    $valor_final = $valor_principal;
                    break;
                case "meli_id_sem_juros":
                    $valor_final = $valor_sem_juros;
                    break;
                case "meli_full":
                    $valor_final = $valor_full;
                    break;
            }
            
            //Atualização Preço
            $body = ["price" => round($valor_final,2)];
            $response = $meli->put("items/{$produto_mercado_livre["meli_id"]}?access_token=" . $produto_mercado_livre["token"], $body, [] );
            if ($response['httpCode'] >= 300) {
                //$mensagem_retorno   .= '<div class="text-danger h4">Preço não atualizado no Mercado Livre</div>';
                $produtos_mercado_livre[$k]["resposta_ml"] = $response;
            }
            else {
		$produtos_mercado_livre[$k]["resposta_ml"] = ["permalink" => $response["body"]->permalink];
                //$mensagem_retorno   .= '<div class="text-success h4">Preço  atualizado no Mercado Livre</div>';
                //$link       = '<div class="h4"><a class="text-primary" href="'.ArrayHelper::getValue($response, 'body.permalink').'">LINK ('.$meli_origem.')</a></div>';
            }
        }

        return [$produtos_mercado_livre, $valor, $valor_envio, $valor_principal, $valor_sem_juros, $valor_full];

    }

    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        //echo "<pre>"; var_dump($insert); echo "</pre>"; die;

        $atributos              = json_encode($this->attributes);

        Log::registrarLog($atributos, "produto_filial", $this->id, 1, ($insert) ? 1 : 2);

    }

    public function afterDelete()
    {
        parent::afterDelete();

        $atributos              = json_encode($this->attributes);

        Log::registrarLog($atributos, "produto_filial", $this->id, 1, 3);

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
