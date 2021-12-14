<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdatePrecoAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_preco_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME
        $user = $meli->refreshAccessToken('TG-5d3ee920d98a8e0006998e2b-193724256');
        $response = ArrayHelper::getValue($user, 'body');

        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;

            $x = 0;
            $y = 0;
            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
            //break;
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                    //if(ArrayHelper::getValue($venda, 'id') != 'MLB1063732490'){continue;}
                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            if(ArrayHelper::getValue($itens, 'id') == 'MLB867584925'){
                                print_r($itens);
                            }
                            $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                        }
                    }
                }

                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        echo "Tabela de preços gerada!\n";
        //Código de criação da tabela de preços baseadas no ME

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [72]])
    	    ->andWhere(['<>','id', 43])
	    ->andWhere(['<>','id', 98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1235256674','MLB1094833277','MLB971490507','MLB864707646','MLB864697349','MLB1094830263','MLB1094833242','MLB864697419','MLB1103762075','MLB867579531','MLB1104343605','MLB864693090','MLB864694476','MLB864690511','MLB864697309','MLB883826748','MLB1094829699','MLB883825923','MLB883823037','MLB1094829717','MLB878431164','MLB883822465','MLB883829158','MLB883826744','MLB1094828475','MLB917987328','MLB883829604','MLB1235260391','MLB1235256992','MLB883828844','MLB883826561','MLB883822707','MLB883822365','MLB883829800','MLB1094832773','MLB1094829654','MLB1094832842','MLB878431546','MLB883826172','MLB1094829407','MLB883822732','MLB917990634','MLB883826977','MLB1094832637','MLB883822216','MLB1094829499','MLB1094829499','MLB1235257715','MLB883822930','MLB878424409','MLB883829748','MLB883825964','MLB878436644','MLB883826290','MLB1094820487','MLB878427963','MLB883826174','MLB878432690','MLB878428036','MLB975202781','MLB878431167','MLB878436667','MLB1235256833','MLB878424087','MLB939172432','MLB878427463','MLB878430536','MLB878437100','MLB878437100','MLB1235261501','MLB1235257735','MLB878436836','MLB883826029','MLB1094828229','MLB878419831','MLB1094829248','MLB878424394','MLB878427502','MLB878427502','MLB878420877','MLB878420877','MLB878427891','MLB878428332','MLB878427523','MLB878421423','MLB1235257815','MLB878416202','MLB878420868','MLB878421641','MLB1094829286','MLB901637062','MLB878416224','MLB1094829164','MLB878425275','MLB1234296833','MLB1234296833','MLB901631789','MLB878410343','MLB878413686','MLB1094820020','MLB1094828898','MLB878428350','MLB901631774','MLB878410346','MLB878421395','MLB878410348','MLB1235261573','MLB883823214','MLB903833668','MLB878408641','MLB901636885','MLB901634638','MLB878418798','MLB878420585','MLB901632049','MLB901634184','MLB878432955','MLB878415879','MLB1239695224','MLB901634192','MLB878415709','MLB878403575','MLB1094828995','MLB878419451','MLB878418213','MLB878412368','MLB878415469','MLB878419842','MLB878407119','MLB878414163','MLB901634196','MLB878414402','MLB878416803','MLB878411236','MLB878417345','MLB878420702','MLB878412720','MLB878413571','MLB878414243','MLB878410105','MLB878403970','MLB878412112','MLB878413439','MLB878413077','MLB878411450','MLB878409987','MLB878418323','MLB878418215','MLB878414854','MLB878409341','MLB1094828965','MLB878419415','MLB1094827891','MLB1235261368','MLB878409386','MLB878421863','MLB878413864','MLB1094819926','MLB878416906']])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB864694520']])
					    ->andWhere(['=','produto_filial.id', 34263])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n ==> ".$produtoFilial->id;

		    if(!$produtoFilial->atualizar_preco_mercado_livre){
			echo " - Não Atualiza ML";
			continue;
		    }

		    if ($produtoFilial->produto->fabricante_id != null) {

        		$preco = round($produtoFilial->getValorMercadoLivre(), 2);

                        if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
                            //$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);

                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco = $preco-10;
                            }
        		    /*elseif($preco<=120){
        			$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco =  $preco-5;
                            }*/
        	            elseif($preco > 120 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco =  $preco-16;
                            }
                        }

                        //Aqui começa o código
                            if (is_null($produtoFilial->valorMaisRecente)) {
                                continue;
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

			    //Atualização Modo de Envio
                            $body = [
                                        "shipping" => [
                                            "mode" => $modo,
                                            "local_pick_up" => true,
                                            "free_shipping" => false,
                                            "free_methods" => [],
                                        ],
                                ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
			    //print_r($response);
                            //Atualização Preço
			    $body = [
                                        "available_quantity" => $produtoFilial->quantidade,
                                ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            $body = [
					"price" => $preco,
                                ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
			    //print_r($body);
                            if ($response['httpCode'] >= 300) {
				    print_r($response);
			            echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR";
				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Preço não alterado");
                            }
                            else {
//    				print_r($response);
				echo "\n\n\n";var_dump(ArrayHelper::getValue($response, 'body.category_id'));echo "\n\n\n";
	                            echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ok";
    				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Preço alterado");

	    	                    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();

        	                    foreach ($produtos_filiais_outros as $produto_filial_outro){
	                                    $preco_outro = round($preco, 2);
	                                    /*if ($preco <= 500){
	                                        $preco_outro = round(($preco * 0.95), 2);
	                                    }*/

	                                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
	                                    $response_outro = ArrayHelper::getValue($user_outro, 'body');

	                                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {

	                                        $meliAccessToken_outro = $response_outro->access_token;
	                                        if($produto_filial_outro->meli_id != null){
						    $body = [
                                                            "shipping" => [
                                                                    "mode" => $modo,
                                                                    "local_pick_up" => true,
                                                                    "free_shipping" => false,
                                                                    "free_methods" => [],
                                                                ],
                                                    ];
                                                    $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
						    //print_r($response);
	                                            $body = [
	                                                    "available_quantity" => $produtoFilial->quantidade,
	                                                    "price" => $preco_outro,
	                            		    ];
	                                            $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
						    //print_r($response);
	                                            if ($response['httpCode'] >= 300) {
	                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado não alterado");
	                                            }else {
	                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado alterado");
	                                            }
	                                        }
	                                        else{
						    $title = Yii::t('app', '{nome} ({cod})', [
			                                        'cod' => $produtoFilial->produto->codigo_global,
			                                        'nome' => $produtoFilial->produto->nome
			                                   ]);
	                                            $body = [
	                                                    "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
	                                                    "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
	                        		            "listing_type_id" => "bronze",
	                        		            "currency_id" => "BRL",
	                        		            "price" => $preco_outro,//round(($preco * 0.9), 2),//$preco,
	                        		            "available_quantity" => utf8_encode($produtoFilial->quantidade),
	                        		            "condition" => "new",
	                        		            "pictures" => $produtoFilial->produto->getUrlImagesML(),//$imagens_outro,//$produtoFilial->produto->getUrlImagesML(),
	                        		            "shipping" => [
	                        		                        "mode" => "me2",
	                        		                        "local_pick_up" => true,
	                        		                        "free_shipping" => false,
	                        		                        "free_methods" => [],
	                        		                    ],
	                                                    "sale_terms" => [
	                                        			        [       "id" => "WARRANTY_TYPE",
	                                        			                "value_id" => "2230280"
	                                        			        ],
	                                        			        [       "id" => "WARRANTY_TIME",
	                                        			                "value_name" => "3 meses"
	                                			                ]
	                                                        ]
	                        		                ];
	                                            $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
						    //print_r($response);
						    if ($response['httpCode'] < 300) {
	                                                $produto_filial_outro->meli_id = $response['body']->id;
	                                                $produto_filial_outro->save();
	                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado criado");
	                                            }
	                                            else {
	                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado não criado");
	                                            }
           		                    }
            		                }
            		            }
                            }
                        //Aqui termina o código
                    }
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
