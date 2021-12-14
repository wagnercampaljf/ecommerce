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

class UpdateCorrecaoGlobalAction extends Action
{
    public function run()
    {
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

	//Código de criação da tabela de preços baseadas no ME
	//$user = $meli->refreshAccessToken('TG-5b5f1c7be4b09e746623a2ca-193724256');
       	/*$user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
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
                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
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
	echo "Tabela de preços gerada!\n";*/
	//Código de criação da tabela de preços baseadas no ME

        $filials = Filial::find() 
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [74,92,4,86,71]])
	    ->andWhere(['<>','id', 98])
	    ->andWhere(['<>','id', 43])
	    //->andWhere(['=','id', 86])
            ->all();

	$filial_duplicada		= Filial::find()->andWhere(['=', 'id', 98])->one();
	$user_duplicada			= $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
	$response_duplicada		= ArrayHelper::getValue($user_duplicada, 'body');
	$meliAccessToken_duplicada	= $response_duplicada->access_token;
	//print_r($meliAccessToken_duplicada); die;

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
	    //print_r($user);
            $response = ArrayHelper::getValue($user, 'body');
	    //print_r($response);
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial->getProdutoFilials()
		    //->joinWith('produto')
                    //->andWhere(['like','upper(produto.nome)','CAIXA DISTRIBUI'])
		    //->andWhere(['like','upper(produto.nome)','LATERAL'])
                    //->andWhere(['is not','meli_id',null])
                    //->andWhere(['>','quantidade',0])
		    //->andWhere(['produto_filial.meli_id'=>['MLB1296285460','MLB1094156514']])
		    //->andWhere(['produto_filial.meli_id'=>['MLB901634941','MLB1104344932','MLB1104339610','MLB1124083972','MLB1196596989','MLB878436683','MLB1141699138','MLB883825964','MLB883829444','MLB878421692','MLB1094833173']])
		    //->andWhere(['produto_filial.id'=>[37850,36716,138260,139188,36640,36449,37169,36472,35179,35603,34238,37391,38447,36730,72557,38370,37560,36658,36877,36547,36451,35310,35498,37848,35439,34495,36576,34492,36580,35841,34607,35778,37658,34119,33842,37601,37635,35683,37031,37647,37088,248834,248752,248753,248859,249127,249128,249162,249163,248611,248612,248546,248801,249197,37348,37360,248746,248747,248721,248722]])
		    //->andWhere(['produto_filial.produto_id'=>[291730, 222586, 278861, 287616, 279999,  15692,  44623,  15558, 228684,  56236, 228857, 278598, 227624, 313327, 277861, 286326,  15694,  15691,  15561,  15222, 285025,  56150, 289799,  12408, 231799, 287845,  56037, 226434, 240929, 307172,  15688, 231807, 287882,  15390,  57306,  15389, 227841,  15563, 231463, 231786,238015, 280990, 272359, 279506, 275074, 277513, 274926,  12678, 275598, 273662,  15693, 237314, 231802, 275979, 287905,  15695, 293462, 277510,  15557,  28321, 287843, 275597, 232829, 277931, 235880,  15071, 278512,  56068, 282201, 284989,  12659, 285963, 299081, 284965, 273340, 284787, 230581, 291647]])
		    ->orderBy('produto_filial.id')
                    ->all();
		    //print_r($produtoFilials);
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {
		    //echo $k . " - " .$produtoFilial->produto->nome . "\n "; continue;
                    //if ($produtoFilial->produto->fabricante_id != 109) {
		    //if($produtoFilial->filial_id == 96 and $produtoFilial->produto->fabricante_id != 109){
		    /*if($produtoFilial->produto->altura <= 70 and $produtoFilial->produto->largura <= 70 and $produtoFilial->produto->profundidade <= 70){
		    	continue;
		    }*/

		    /*if(($k > 12300 && $produtoFilial->filial_id == 97) || ($k > 12300 && $produtoFilial->filial_id == 38)){
                            echo " - Já alterado;";
                            continue;
                    }*/

		    if ($produtoFilial->produto->fabricante_id != null) {
			    $title = Yii::t('app', '{nome} ({cod})', [
                                'cod' => $produtoFilial->produto->codigo_global,
                                'nome' => $produtoFilial->produto->nome
                            ]);

			    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ".((strlen($title) <= 60) ? $title : substr($title, 0, 60));
			    //continue;

			    //Preço, descomentar quanto for alterar preços
			    //$preco = round($produtoFilial->getValorMercadoLivre(), 2);

			    /*if($preco >= 120){
				echo "Preço maior";
				continue;
			    }*/

//echo "===><===";
			    /*if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
				//$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);

				if($preco>=510){
				    $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                            	    $preco = $preço-10;
                            	} 
				//elseif($preco<=120){
                            	//    $preco =  $preco-5;
                            	//}
				elseif($preco > 120 && $preco < 510){
				    $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                            	    $preco =  $preco-16;
                            	}
			    }*/
			    //echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ";
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
	                    /*if (is_null($produtoFilial->valorMaisRecente)) {
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
        	                "warranty" => "6 meses",
        	            ];
	
	                    $response = $meli->put(
	                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
	                        $body,
	                        []
	                    );
			    //print_r($response);
			    //print_r(ArrayHelper::getValue($response, 'body.permalink'));echo "\n\n";
	                    Yii::info($response, 'mercado_livre_update');
	                    if ($response['httpCode'] >= 300) {
	                        Yii::error($response['body'], 'mercado_livre_update');
				echo "ERROR \n";
				//print_r($response);
			    } else {
				echo " - ". ArrayHelper::getValue($response, 'body.permalink');echo "\n";
				echo "ok \n";
	                    }*/

			//Aqui termina o código

			//Update Imagem

			    //Update Item
                            //echo "******sd******";
                            /*$body = [//"category_id" => utf8_encode($subcategoriaMeli),
				     "shipping" => [
                                    			"mode" => "not_specified",
                                     			"local_pick_up" => true,
                                     			"free_shipping" => false,
                                     			"free_methods" => [],
                                     	],
				     //"price" => round($produtoFilial->getValorMercadoLivre(), 2),
				    ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
			    print_r($response);
			    if ($response['httpCode'] >= 300) {
				echo "Erro";//.ArrayHelper::getValue($response, 'body.permalink');
                            }else{
				echo "Ok - ".ArrayHelper::getValue($response, 'body.permalink');
			    }*/



			    //Update Atributos

			    $lado_id 	= "-1";
			    $lado_name	= null;
			    if(strpos($produtoFilial->produto->nome,"ESQUERD")){
				$lado_id    = "364128";
				$lado_name  = "Esquerdo";
			    }
			    else{
				if(strpos($produtoFilial->produto->nome,"DIREIT")){
                                     $lado_id    = "364127";
                                     $lado_name  = "Direito";
                            	}
			    }

			    $posicao_id 	= "-1";
			    $posicao_name	= null;
			    if(strpos($produtoFilial->produto->nome,"DIANT")){
                                $posicao_id    = "405827";
                                $posicao_name  = "Dianteira";
                            }
                            else{
                                if(strpos($produtoFilial->produto->nome,"TRAS")){
                                     $posicao_id    = "116949";
                                     $posicao_name  = "Traseira";
                                }
                            }

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

			    //if(($produtoFilial->produto->altura < 70) && ($produtoFilial->produto->largura < 70) && ($produtoFilial->produto->profundidade < 70)){
				//$subcategoriaMeli = "MLB251640";
			    //}

                            $body = [
				//"available_quantity" => utf8_encode($produtoFilial->quantidade),
				//"pictures" => $produtoFilial->produto->getUrlImagesML(),
				"category_id" => utf8_encode($subcategoriaMeli),
				//"condition" => "used",
				//"price" => round($produtoFilial->getValorMercadoLivre(), 2),
				/*"shipping" => [
                                    "mode" => $modo,
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],*/
				//'attributes' =>[
				    /*[
                                        'id' 			=> 'PART_NUMBER',
                                        'name' 			=> 'Número da peça',
                                        'value_id' 		=> NULL,
                                        'value_name' 		=> $produtoFilial->produto->codigo_global,
                                        'value_struct' 		=> NULL,
                                        'attribute_group_id' 	=> 'DFLT',
                                        'attribute_group_name' 	=> 'Outros',
                                    ],*/
				    /*[
					'id'			=> 'BRAND',
				      	'name'			=> 'Marca',
				      	'value_id'		=> null,
				      	'value_name'		=> 'OPT',
				      	'value_struct'		=> null,
				      	'attribute_group_id'	=> 'OTHERS',
				      	'attribute_group_name'	=> 'Outros'
				    ],*/
                                    /*[
                                        'id' 			=> 'CONNECTOR_GENDER',
                                        'name' 			=> 'Gênero do conector',
                                        'value_id' 		=> '2210104',
                                        'value_name' 		=> 'Macho',
					'value_struct' 		=> null,
                                        'attribute_group_id' 	=> 'OTHERS',
                                        'attribute_group_name'	=> 'Outros',
                                    ],
                                    [
                                        'id'                    => 'TERMINAL_QUANTITY',
                                        'name'                  => 'Quantidade de terminais',
                                        'value_id'              => null,
                                        'value_name'            => '1',
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'MODEL',
                                        'name'                  => 'Modelo',
                                        'value_id'              => null,
                                        'value_name'            => 'CHAVE',
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'TIPS_DIAMETER',
                                        'name'                  => 'Diâmetro das puntas',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'MATERIAL',
                                        'name'                  => 'Material',
                                        'value_id'              => '4837600',
                                        'value_name'            => 'Aço',
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'WRENCH_LENGTH',
                                        'name'                  => 'Comprimento da chave',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'AIRBAG_INCLUDED',
                                        'name'                  => 'Airbag incluído',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
				    [
                                        'id'                    => 'ORIGIN',
                                        'name'                  => 'Origem',
                                        'value_id'              => null,
                                        'value_name'            => 'IMPORTADO',
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'RADIO_CONTROLS_INCLUDED',
                                        'name'                  => 'Controles de estéreo incluídos',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'STEERING_WHEEL_GRIP_MATERIAL',
                                        'name'                  => 'Material do agarre',
                                        'value_id'              => '2707741',
                                        'value_name'            => 'Sintético',
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'STEERING_WHEEL_TYPE',
                                        'name'                  => 'Tipo',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'SIDE_POSITION',
                                        'name'                  => 'Lado',
                                        'value_id'              => "5365767",//$lado_id,
                                        'value_name'            => "Esquerdo/Direito",//$lado_name,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
                                        'id'                    => 'POSITION',
                                        'name'                  => 'Posição',
                                        'value_id'              => $posicao_id,
                                        'value_name'            => $posicao_name,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
                                    /*[
                                        'id'                    => 'BUMPER_BRACKET_MATERIAL',
                                        'name'                  => 'Material do suporte de pára-choques',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'ACCESSORY_TYPE',
                                        'name'                  => 'Tipo de acessório',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'LENGTH',
                                        'name'                  => 'Comprimento',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'REFRIGERANT_TYPE',
                                        'name'                  => 'Tipo de refrigerante',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'SEALS_INCLUDED',
                                        'name'                  => 'Selos incluídos',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'SYSTEM_PRESSURE',
                                        'name'                  => 'Pressão do sistema',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
                                    /*[
                                        'id'                    => 'CLUTCH_BEARING_INSIDE_DIAMETER',
                                        'name'                  => 'Diâmetro interno do rolamento de embreagem',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => '',
                                        'attribute_group_name'  => '',
                                    ],
                                    [
                                        'id'                    => 'CLUTCH_BEARING_RACE_INCLUDED',
                                        'name'                  => 'Pista do rolamento de embreagem incluída',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'CLUTCH_BEARING_OUTSIDE_DIAMETER',
                                        'name'                  => 'Diâmetro externo do rolamento de embreagem',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'SEALED_CLUTCH_BEARING',
                                        'name'                  => 'Rolamento de embreagem selado',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'BENDIX_GEAR_TEETH',
                                        'name'                  => 'Dentes bendix',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'DIRECTION_ROTATION',
                                        'name'                  => 'Sentido de rotação',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'STARTER_VOLTAGE',
                                        'name'                  => 'Voltagem',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
                                    [
                                        'id'                    => 'WIRE_LENGTH',
                                        'name'                  => 'Comprimento do cabo',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
/*				    [
                                        'id'                    => 'BRAKE_BOOSTER_DIAPHRAGM_TYPE',
                                        'name'                  => 'Tipo de diafragma do servo-freio',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'BRAKE_BOOSTER_TYPE',
                                        'name'                  => 'Tipo de servo-freio',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'BRAKE_BOOSTER_USE',
                                        'name'                  => 'Uso do servo-freio',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'INCLUDES_MASTER_CYLINDER',
                                        'name'                  => 'Inclui cilindro principal',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],
                                    [
                                        'id'                    => 'INCLUDES_PEDAL_ROD_EXTENSION',
                                        'name'                  => 'Inclui extensão da barra do pedal',
                                        'value_id'              => '-1',
                                        'value_name'            => null,
                                        'value_struct'          => null,
                                        'attribute_group_id'    => 'OTHERS',
                                        'attribute_group_name'  => 'Outros',
                                    ],*/
				    /*[
					"id"			=> "ORIGIN",
				        "name"			=> "Origem",
				        "value_id"		=> null,
				        "value_name"		=> "IMPORTADO",
				        "value_struct"		=> null,
				        "attribute_group_id"	=> "OTHERS",
					"attribute_group_name"	=> "Outros",
				    ],*/

                                //]
			    ];

			    //echo "\n\n\n".(json_encode($body))."\n\n\n";
			    //die;
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);

			//echo "\n\n\nitems/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken."\n\n\n";

			    if ($response['httpCode'] >= 300) {
				//print_r($response); //die;
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }else{
				//print_r($response);
                            	echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');

				$produto_filial_duplicado = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])
										 ->andWhere(['=','filial_id',98])
										 ->one();
				if($produto_filial_duplicado){
					$response_duplicada = $meli->put("items/{$produto_filial_duplicado->meli_id}?access_token=" . $meliAccessToken_duplicada, $body, []);
					echo " |Duplicada Encontrada - ";//.ArrayHelper::getValue($response_duplicada, 'body.permalink');

					if ($response_duplicada['httpCode'] >= 300) {
                        		        echo " |Erro";
                		        }else{
		                                echo " |Ok";
					}
				}
				else{
					//print_r($response);
					echo " -  Erro (Duplicada NÃO encontrada";
				}
                            }
		    }
                }
            }
            echo "Fim da filial: " . $filial->nome . "\n";
        }
    }
}

