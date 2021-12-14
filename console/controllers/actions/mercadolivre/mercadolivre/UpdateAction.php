<?php

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
        $nome_arquivo = "/var/tmp/log_update_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME
        //$user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $user = $meli->refreshAccessToken('TG-5d3ee920d98a8e0006998e2b-193724256');
        
        $response = ArrayHelper::getValue($user, 'body');

        $produto_frete['0'] = 0;
        $meliAccessToken = $response->access_token;
        
        print_r($meliAccessToken); die;
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;

            print_r($meliAccessToken); die;
            
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
            //->andWhere(['id' => [84]])
    	    ->andWhere(['<>','id', 43])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            //continue;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            ->andWhere(['produto_filial.meli_id' => ['MLB1094830117']])
                                            //->andWhere(['produto_filial.id' => [142479]])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n ==> ".$produtoFilial->id;
        		    if ($produtoFilial->produto->fabricante_id != null) {
        
        			    $preco = round($produtoFilial->getValorMercadoLivre(), 2);
        
                        if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
                            //$preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
        
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco = $preco-10;
                            }
        				    elseif($preco<=120){
        				        $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco =  $preco-5;
                            }
        				    elseif($preco > 120 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco =  $preco-16;
                            }
                        }
        
                        //Aqui começa o código
                            if (is_null($produtoFilial->valorMaisRecente)) {
                                continue;
                            }
        
                            $title = Yii::t('app', '{nome} ({cod})', [
        	                        'cod' => $produtoFilial->produto->codigo_global,
        	                        'nome' => $produtoFilial->produto->nome
        	                   ]);
        
                            //Atualização Preço
                            $body = [ 
                                        "price" => $preco,//round($produtoFilial->getValorMercadoLivre(), 2),
                                        "available_quantity" => $produtoFilial->quantidade,
                                    ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
        				        fwrite($arquivo_log, "\n".$produtoFilial->id.";".$preco.";Preço não alterado;");
                            }else {
        				        fwrite($arquivo_log, "\n".$produtoFilial->id.";".$preco.";Preço alterado;");
                            }
        
                            //Atualização Título
                            $body = [
        	                        "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
        				        echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR";
                            } 
                            else {
                                echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ok";
        	                    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();
        
        	                    foreach ($produtos_filiais_outros as $produto_filial_outro){
                                    $preco_outro = round($preco, 2);
                                    if ($preco <= 500){
                                        $preco_outro = round(($preco * 0.95), 2);
                                    }
        
                                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                                    $response_outro = ArrayHelper::getValue($user_outro, 'body');
                                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                                        $meliAccessToken_outro = $response_outro->access_token;
                                        if($produto_filial_outro->meli_id != null){
                                            $body = [ 
                                                    "available_quantity" => $produtoFilial->quantidade,
                                                    "price" => $preco_outro,
                                                    "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                            		                ];
                                            $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                                            if ($response['httpCode'] >= 300) {
                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado não alterado");
                                            }else {
                                                fwrite($arquivo_log, $produto_filial_outro->id.";".$preco_outro.";Produto duplicado alterado");
                                            }
                                        }
                                        else{
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
                        		                        "mode" => $modo,
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
        echo "Fim da filial: " . $filial->nome . "       1\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
