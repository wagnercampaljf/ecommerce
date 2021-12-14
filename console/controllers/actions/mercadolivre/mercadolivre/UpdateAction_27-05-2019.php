<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateAction extends Action
{
    public function run()
    {
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

    	//Código de criação da tabela de preços baseadas no ME
            $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
            $response = ArrayHelper::getValue($user, 'body');
    
            $produto_frete['0'] = 0;
    
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
    
                $x = 0;
                $y = 0;
    
                $data_atual = date('Y-m-d');
    
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
                while (ArrayHelper::getValue($response_order, 'body.results') != null){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                        //if(ArrayHelper::getValue($venda, 'id') != 'MLB1063732490'){continue;}
    
            			if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                            $y++;
                            echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 
            
                            foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
            			        //if(ArrayHelper::getValue($itens, 'id') == 'MLB867584925'){
                                    //print_r($itens);
            			        //}
                                echo "\n"; print_r(ArrayHelper::getValue($itens, 'id'));
                                echo "\n"; print_r(ArrayHelper::getValue($itens, 'dimensions'));
                                $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($itens, 'id')])->one();
                                echo "\n".$produto_filial->produto->altura;
                                echo "x".$produto_filial->produto->largura;
                                echo "x".$produto_filial->produto->profundidade;
                                echo "x".$produto_filial->produto->peso;
                                
                                
                                $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                                $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                            }
                        }
                    }
                    $x += 50;
                    $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
                }
                //var_dump(ArrayHelper::keyExists('MLB864729660', $produto_frete, false));
                //var_dump(ArrayHelper::getValue($produto_frete, 'MLB864729660'));
            }
            echo "Tabela de preços gerada!\n";
            
            die;
        //Código de criação da tabela de preços baseadas no ME

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [60]])
	    //->andWhere(['<>','id', 92])
	    //->andWhere(['<>','id', 78])
	    //->andWhere(['<>','id', 82])
	    //->andWhere(['<>','id', 81])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
	    //continue;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                //echo "\n 123321 \n";

		$meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
                    ->andWhere(['is not','meli_id',null])
                    //->andWhere(['>','quantidade',0])
		    //->andWhere(['produto_filial.meli_id' => ['MLB883822941']])
		    //->andWhere(['produto_filial.id' => [144224]])
		    ->andWhere(['>','produto_filial.id',37958])
		    ->orderBy('produto_filial.id')
                    ->all();
		    //print_r($produtoFilials);
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {
		    echo $produtoFilial->produto->nome . "\n ";
                    //if ($k > 13560 && $produtoFilial->produto->fabricante_id != null) {
		    if ($produtoFilial->produto->fabricante_id != null) {

			    $preco = round($produtoFilial->getValorMercadoLivre(), 2);

                            if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
                                //$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);

                                if($preco>=510){
                                   	$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
					$preco = $preco-10;
                                }
				/*elseif($preco<=120){
                                    $preco =  $preco-5;
                                }*/
				elseif($preco > 120 && $preco < 510){
					$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
					$preco =  $preco-16;
                                }
                            }

		    	    echo $k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ";
			    //echo $produtoFilial->produto->subcategoria_id ." - ". $produtoFilial->produto->subcategoria->meli_id ;

			    //$subcategoriaMeli = "MLB191833"; //Subcategoria para 70,82 - Sem ME;
	                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
	                    if (!isset($subcategoriaMeli)) {
	                        continue;
	                    } else {
			    	if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
					$subcategoriaMeli = "MLB191833";
				}
			    }
			//Aqui começa o código
	                    if (is_null($produtoFilial->valorMaisRecente)) {
	                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
	                            'error_yii');
	                        continue;
	                    }
			
	                    //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',['produto' => $produtoFilial]);
			    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
	
	                    $title = Yii::t('app', '{nome} ({cod})', [
	                        'cod' => $produtoFilial->produto->codigo_global,
	                        'nome' => $produtoFilial->produto->nome
	                    ]);
	                    //Update Item
			    //echo "******sd******";
	                    $body = [
	                        "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
				//"category_id" => utf8_encode($subcategoriaMeli),
	                        "price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
	                        "available_quantity" => $produtoFilial->quantidade,
				'attributes' =>[
                	            [
        	                        'id' => 'PART_NUMBER',
        	                        'name' => 'Número da peça',
        	                        'value_id' => NULL,
        	                        'value_name' => $produtoFilial->produto->codigo_global,
        	                        'value_struct' => NULL,
        	                        'attribute_group_id' => 'DFLT',
        	                        'attribute_group_name' => 'Outros',
        	                    ],
        	                    [
        	                        "id"=> "BRAND",
        	                        "name"=> "Marca",
        	                        "value_id"=> null,
        	                        "value_name"=> $produtoFilial->produto->fabricante->nome,
        	                        "value_struct"=> null,
        	                        "attribute_group_id"=> "OTHERS",
        	                        "attribute_group_name"=> "Outros"
                        	    ],
				    [
                                	"id" => "EAN",
                                	"value_name" => $produtoFilial->produto->codigo_barras
                            	    ],
	
	                        ]
	
	                    ];
   			    //echo "------0------";
        	            $response = $meli->put(
        	                "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
        	                $body,
        	                []
        	            );
			    //print_r($response);
			    //echo "------1-----";
        	            Yii::info($response, 'mercado_livre_update');
        	            if ($response['httpCode'] >= 300) {
        	                Yii::error($response['body'], 'mercado_livre_update');
        	            }
	
	                    //Update Descrição
	                    //$body = ['text' => $page];
			    $body = ['plain_text' => $page];
	                    $response = $meli->put(
	                        "items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken,
	                        $body,
        	                []
	                    );
	                    Yii::info($response, 'mercado_livre_update');
        	            if ($response['httpCode'] >= 300) {
        	                Yii::error($response['body'], 'mercado_livre_update');
        	            }
	
			    //1 para me2 (Mercado Envios)
		            //2 para not_especified (a combinar)
		            //3 para customizado

			    switch ($produtoFilial->envio) {
				case 1:
	        			$modo = "me2";
	        			break;
				case 2:
	        			$modo = "not_specified";
	        			break;
	    			case 3:
	        			$modo = "custom";
	       				break;
			    }
			    //echo "$".$modo."$";

			    $video_id = "";
	                    $video_complemento  = explode("=",$produtoFilial->produto->video);
	                    if (isset($video_complemento[1])){
	                            $video_codigo       = explode("&",$video_complemento[1]);
	                            $video_id           = $video_codigo[0];
	                    }

        	            //Update Imagem
        	            $body = [
        	                "pictures" => $produtoFilial->produto->getUrlImagesML(),
				"video_id" => $video_id,
				"available_quantity" => $produtoFilial->quantidade,
				"price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
        	                "shipping" => [
        	                    "mode" => $modo,
        	                    "local_pick_up" => true,
        	                    "free_shipping" => false,
        	                    "free_methods" => [],
        	                ],
        	                "warranty" => "3 meses contra defeitos de fabricação.",
        	            ];
	
	                    $response = $meli->put(
	                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
	                        $body,
	                        []
	                    );
			    //print_r($response);
			    //print_r(ArrayHelper::getValue($response, 'body.permalink'));echo "\n\n";

	                    $body = ["sale_terms" => [
	                        [       "id" => "WARRANTY_TYPE",
	                            "value_id" => 2230280
	                        ],
	                        [       "id" => "WARRANTY_TIME",
	                            "value_name": "3 meses"
	                        ]
	                    ]
	                    ];
	                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
	                    print_r($response);
	                    
                Yii::info($response, 'mercado_livre_update');
                if ($response['httpCode'] >= 300) {
	                        Yii::error($response['body'], 'mercado_livre_update');
				echo "ERROR \n";
				//print_r($response);
			    } else {
				//echo " - ". ArrayHelper::getValue($response, 'body.permalink');echo "\n";
				echo "ok \n";

				//Início das outras contas do ML

		                    //$filiais_outros = Filial::find()->andWhere(['=', 'mercado_livre_secundario', 'true'])->all();

		                    //foreach ($filiais_outros as $filial_outro){
		                    //$produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','filial_id',$filial_outro->id])
		                    //                                                ->andWhere(['=','produto_id', $produtoFilial->produto_id])
		                    //                                                ->all();
		                    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();

		                    foreach ($produtos_filiais_outros as $produto_filial_outro){

		                        $imagens_outro = array();

			                if($produto_filial_outro->filial->mercado_livre_logo){
		                            $imagens_outro = $produtoFilial->produto->getUrlImagesML();
          			        }
                		        else{
        		                    $imagens_outro = $produtoFilial->produto->getUrlImagesMLSemLogo();
		                        }

        		                $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
		                        $response_outro = ArrayHelper::getValue($user_outro, 'body');

                		        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                		            $meliAccessToken_outro = $response_outro->access_token;
                		            if($produto_filial_outro->meli_id != null){
                		                $body = [ "pictures" => $imagens_outro,
                		                    "available_quantity" => $produtoFilial->quantidade,
                		                    "price" => $preco,
                		                ];
                		                $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                		                echo "\nAlterado";
                		            }
                		            else{
                		                $body = [   "title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
                		                    "category_id" => utf8_encode($subcategoriaMeli),
                		                    "listing_type_id" => "bronze",
                		                    "currency_id" => "BRL",
                		                    "price" => $preco,
                		                    "available_quantity" => utf8_encode($produtoFilial->quantidade),
                		                    "seller_custom_field" => utf8_encode($produto_filial_outro->id),
                		                    "condition" => "new",
                		                    "description" => ["plain_text" => $page],
                		                    "pictures" => $imagens_outro,//$produtoFilial->produto->getUrlImagesML(),
                		                    "shipping" => [
                		                        "mode" => $modo,
                		                        "local_pick_up" => true,
                		                        "free_shipping" => false,
                		                        "free_methods" => [],
                		                    ],
                		                    "warranty" => "6 meses",
						    //"item_id" => $produtoFilial->id,
                		                ];
                		                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                		                if ($response['httpCode'] < 300) {
                		                    $produto_filial_outro->meli_id = $response['body']->id;
        		                            $produto_filial_outro->save();
        		                        }
        		                        echo "\nCriado";
        		                    }
        		                }
        		            }
		                    //}

		                    //Fim das outras contas do ML

	                    }

			//Aqui termina o código

		    }
                }
            }
            echo "Fim da filial: " . $filial->nome . "\n";
        }
    }
}
