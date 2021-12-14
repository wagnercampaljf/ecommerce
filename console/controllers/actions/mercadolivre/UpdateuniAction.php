<?php
namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateuniAction extends Action
{
    public function run($global_id)
    {
	//echo "=>1<=";
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

	//Código de criação da tabela de preços baseadas no ME
        $user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
        $response = ArrayHelper::getValue($user, 'body');

        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;

            $x = 0;
            $y = 0;

            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
		break;
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                        //if(ArrayHelper::getValue($venda, 'id') != $global_id){continue;}

                        if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            if(ArrayHelper::getValue($itens, 'id') != $global_id){continue;}

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
        //Código de criação da tabela de preços baseadas no ME

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => 96])
     	    ->andWhere(['<>', 'id', 43])
	    ->andWhere(['<>', 'id', 98])
	    ->all();
        /* @var $filial Filial */
        foreach ($filials as $filial) {
	    print_r($filial->nome);echo "\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
	    $response = ArrayHelper::getValue($user, 'body');
	    //print_r($response);
	    if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
		echo "opa!";
		$meliAccessToken = $response->access_token;
		$produtoFilials = $filial->getProdutoFilials()
                    ->andWhere(['is not','meli_id',null])
                    //->andWhere(['>','quantidade',0])
		    ->andWhere(['=','meli_id',$global_id])
		    //->andWhere(['meli_id' => ['MLB864716304','MLB878408129','MLB1094828216','MLB1157506499','MLB878427469','MLB864668192','MLB864733208','MLB864737038','MLB864717772','MLB864693382','MLB878431990','MLB878411996']])
		    //->andWhere(['=','filial_id',86])
                    ->all();
                foreach ($produtoFilials as $produtoFilial) {

		    //Update Status
                    $body = ['status' 		=> "active",
			     'sub_status'	=>[]
		    ];
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                    print_r($response);die;

		    $preco = round($produtoFilial->getValorMercadoLivre(), 2);
		    //echo "\nPreço Inicial:"; print_r($preco); echo "\n";
                    if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
			 //echo "\nPreço Adicional:"; print_r(ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)); echo "\n";
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

               	    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        continue;
                    }
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                            'error_yii');
                        continue;
                    }

                    //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',['produto' => $produtoFilial]);
		    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
echo 11111111111111;
                    $title = Yii::t('app', '{nome} ({cod})', [
                        'cod' => $produtoFilial->produto->codigo_global,
                        'nome' => $produtoFilial->produto->nome
                    ]);


                    //Update Item
		    //echo "==>";print_r($subcategoriaMeli); echo "<==";die;
		    $body = [
                        //"title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
			//"category_id" => "MLB191833",//MLB251640",//utf8_encode($subcategoriaMeli),
                        //"price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
                        "available_quantity" => $produtoFilial->quantidade,
			//"description" => utf8_encode($page),
			//'description' => utf8_encode('Peca Agora'),
			/*'attributes' =>[
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

                        ]*/
                    ];
		    //print_r($body);
                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );
		    print_r($response); 
                    Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }

                    //Update Descrição
                    //$body = ['text' => $page];
		    $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
			     'plain_text' => $page,
			     "available_quantity" => $produtoFilial->quantidade,
			     "price" => $preco];//round($produtoFilial->getValorMercadoLivre(), 2)];
			     
                    $response = $meli->put("items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
		    //print_r($response);
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
                    //Update Imagem
		    //echo $modo; die;
		    echo "\n <$preco> \n";
		    $body = [
                        "pictures" => $produtoFilial->produto->getUrlImagesML(),
			//"pictures" => $imagemTeste,
			//"description" => utf8_encode($page),
			"available_quantity" => $produtoFilial->quantidade,
			"price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
                        "shipping" => [
                            "mode"=> $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                        "warranty" => "6 meses",
                    ];
		    //print_r($body);	
                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );
		    //print_r($response);
                    Yii::info($response, 'mercado_livre_update');
		    //print_r($body);
		    //print_r($response);
		    //print_r(ArrayHelper::getValue($response, 'body.permalink'));
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }

		    $video_id = "";
		    $video_complemento  = explode("=",$produtoFilial->produto->video);
                    if (isset($video_complemento[1])){
	                    $video_codigo   	= explode("&",$video_complemento[1]);
			    $video_id		= $video_codigo[0];
		    }

		    //Update Descrição
                    $body = ["title" 	=> ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
			     "pictures" => $produtoFilial->produto->getUrlImagesML(),
			     "video_id"	=> $video_id,
		    ];
		    //print_r($body);echo "\n\n";
		    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,$body,[]);
                    //print_r($response);
		    Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }

		     //Update Título 
                     /*   $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produtoFilial->produto->codigo_global,'nome' => $produtoFilial->produto->nome]);
                        $body = ["title"    => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,$body,[]);
                        Yii::info($response, 'mercado_livre_update');
                        if ($response['httpCode'] >= 300) {
                                echo "ERROR \n";
                                Yii::error($response['body'], 'mercado_livre_update');
                        } else{ 
                                echo "OK \n";
                        }*/

		    /*$body = [
                        "price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
                    ];
                    print_r($body);     
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
		    print_r($response);

		    //Update Imagem
                    $body = [ "pictures" => $produtoFilial->produto->getUrlImagesML(), ];
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);*/
                    //print_r($response);

		    $body = [
                        "shipping" => [
                            "mode"=> $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                    ];
                    //print_r($body);
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,$body, []);
		    //echo "\n\n\n===>";print_r($response); echo "<===\n\n\n";

		    $body = [
                        //"category_id" => utf8_encode("MLB191833"),
			"category_id" => utf8_encode($subcategoriaMeli),//"MLB193799"),
                    ];
                    //print_r($body);
                    $response = $meli->put( "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
		    //echo "\n\n\n===>";print_r($response); echo "<===\n\n\n";

		    //Update garantia
                    /*$body = ["sale_terms" => [
					[	"id" => "WARRANTY_TYPE",
						"value_id" => "2230280"
					],
					[	"id" => "WARRANTY_TIME",
						"value_name" => "3 meses"
					]
			    	]
			];
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                    print_r($response);*/

		    //Update preço
		    $body = ["price" 	  => $preco,
			     "sale_terms" => [
                                        [       "id" => "WARRANTY_TYPE",
                                                "value_id" => "2230280"
                                        ],
                                        [       "id" => "WARRANTY_TIME",
                                                "value_name" => "3 meses"
                                        ]
                                 ],
			    /*"attributes" =>[
			    	 	[
                            	 	   "id"=> "MODEL",
                            	 	   "name"=> "Modelo",
                            	 	   "value_id"=> -1,
                            	 	   "attribute_group_id"=> "OTHERS",
                            	 	   "attribute_group_name"=> "Outros"
                            		],
				]*/
			    ];
		    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
		    //print_r($response);
                    if ($response['httpCode'] >= 300) {
                        echo "ERROR \n";
                        Yii::error($response['body'], 'mercado_livre_update');
                    } else{
                        echo "OK \n";

			//Início das outras contas do ML

			//$filiais_outros = Filial::find()->andWhere(['=', 'mercado_livre_secundario', 'true'])->all();

	                //foreach ($filiais_outros as $filial_outro){
	                    //$produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','filial_id',$filial_outro->id])
	                    //                                                ->andWhere(['=','produto_id', $produtoFilial->produto_id])
	                    //                                                ->all();
			    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();

	                    foreach ($produtos_filiais_outros as $produto_filial_outro){
echo "\n\n\n123123";
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
                	            	$body = [ "title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
						  "pictures" => $imagens_outro,
                	        		  "available_quantity" => $produtoFilial->quantidade,
				                  "price" => $preco,
						  //"category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
                	                        ];
                	                $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
					echo "\nAlterado";
					//print_r($response);
                	            }
                	            else{
                	                $body = [   	"title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
                	        	                "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
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
                	                        ];
                	                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
					//print_r($response);
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

		    //Retirar do Mercado Envios
		    /*$body = [
                        "shipping" => [
                            "mode"=> "not_specified",
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                    ];
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    print_r($response);*/

                }
            }
        }
    }
}
