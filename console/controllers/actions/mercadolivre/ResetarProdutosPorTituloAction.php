<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\Produto;
use common\models\ProdutoCondicao;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\MarcaProduto;

class ResetarProdutosPorTituloAction extends Action
{
    public function run()//$filial_id)
    {
        
        $l = 0;
        //$file = fopen("/var/tmp/lista_scania_conta_principal_sem_juros.csv", 'r');
        //$file = fopen("/var/tmp/lista_scania_conta_duplicada_principal.csv", 'r');
        $file = fopen("/var/tmp/lista_scania_conta_principal_principal.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            echo "\n".$l++." - ".$line[0];
            
            $produtosArray[] = $line[0];
        }
        fclose($file);
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
        
        echo "\n\nComeço da rotina de atualização do título dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [86]])
            //->andWhere(['<>','id',98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            
            $nome_arquivo = "/var/tmp/log_resetar_titulo_ml_".str_replace(" ", "", $filial->nome)."_".date("Y-m-d_H-i-s").".csv";
            $arquivo_log = fopen($nome_arquivo, "a");
            // Escreve no log
            fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
            fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                //echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                //echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;

                $produto_filiais = $filial  ->getProdutoFilials()
                                            //->andWhere(['is not','meli_id',null])
                                            ->where(" meli_id is not null and (upper(nome) like '%SCANIA%' or upper(aplicacao) like '%SCANIA%' or upper(aplicacao_complementar) like '%SCANIA%') or upper(descricao) like '%SCANIA%'  ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1528400982']])
                                            //->andWhere(['produto_filial.meli_id_sem_juros' => $produtosArray])
                                            ->andWhere(['produto_filial.meli_id' => $produtosArray])
                                            //->andWhere(['produto_filial.id' => [315656]])
                                            //->andWhere(['produto_filial.id' => []])
                                            ->joinWith('produto')
                                            //->andWhere(['like','produto.nome', 'SCANIA'])
                                            //->andWhere(['=','e_nome_alterado',true])
                                            //->andWhere(['is not', 'meli_id_sem_juros', null])
                                            ->orderBy(['produto_filial.id' => SORT_ASC])
                                            ->all();

                foreach ($produto_filiais as $k => $produto_filial) {
 
                    if($k%5000==0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                        
                        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
                        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
                        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
                        
                        echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                        echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;
                    }
                    
                    /*if(!array_key_exists($produto_filial->produto_id, $produtosArray)){
                        continue;
                    }*/
                    
                    echo "\n\n ==> ".$k." - ".$produto_filial->id." - ".$produto_filial->produto->nome;
                    //continue;
                    
        		    /*if($produto_filial->filial_id == 96 && $produto_filial->id <= 453976){
                        continue;
        		    }*/
                    
                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produto_filial->id])->one();
                    
                    echo " - Conta Principal: ".$produto_filial->id;

        		    if($produto_filial_conta_duplicada){
                        echo " - Conta Duplicada: ".$produto_filial_conta_duplicada->id;
        		    }

        		    $title = Yii::t('app', '{nome}', ['nome' => $produto_filial->produto->nome]);
        		    
        		    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produto_filial]);
        		    $page = str_replace("'", "", $page);
        		    $page = str_replace("<p>", " ", $page);
        		    $page = str_replace("</p>", " ", $page);
        		    $page = str_replace("<br>", "\n", $page);
        		    $page = str_replace("<BR>", "\n", $page);
        		    $page = str_replace("<br/>", "\n", $page);
        		    $page = str_replace("<BR/>", "\n", $page);
        		    $page = str_replace("<strong>", " ", $page);
        		    $page = str_replace("</strong>", " ", $page);
        		    $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
        		    $page = str_replace('</span>', " ", $page);
        		    $page = str_replace('<span>', " ", $page);
        		    $page = str_replace('<ul>', " ", $page);
        		    $page = str_replace('</ul>', " ", $page);
        		    $page = str_replace('<li>', "\n", $page);
        		    $page = str_replace('</li>', " ", $page);
        		    $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
        		    $page = str_replace('<h1>', " ", $page);
        		    $page = str_replace('</h1>', " ", $page);
        		    $page = str_replace('<h2>', " ", $page);
        		    $page = str_replace('</h2>', " ", $page);
        		    $page = str_replace('<h3>', " ", $page);
        		    $page = str_replace('</h3>', " ", $page);
        		    $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
        		    $page = str_replace('>>>', "(", $page);
        		    $page = str_replace('<<<', ")", $page);
        		    $page = str_replace('<u>', " ", $page);
        		    $page = str_replace('</u>', "\n", $page);
        		    $page = str_replace('<b>', " ", $page);
        		    $page = str_replace('</b>', " ", $page);
        		    $page = str_replace('<o:p>', " ", $page);
        		    $page = str_replace('</o:p>', " ", $page);
        		    $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
        		    $page = str_replace('<del>', " ", $page);
        		    $page = str_replace('</del>', " ", $page);
        		    $page = str_replace('/', "-", $page);
        		    $page = str_replace('<em>', " ", $page);
        		    $page = str_replace('<-em>', " ", $page);
        		    
        		    $nome = $title;
        		    if(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
        		        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@11@', $nome);
        		    }
        		    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
        		        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@10@', $nome);
        		    }
        		    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
        		        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@9@', $nome);
        		    }
        		    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
        		        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@8@', $nome);
        		    }
        		    
        		    $titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
        		    
                    //Atualizar produto principal, conta principal
        		    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, "CONTA PRINCIPAL - PRINCIPAL", $page, $titulo_novo, $produto_filial);

                    //Atualizar produto SEM JUROS, conta principal
                    //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, "CONTA PRINCIPAL - SEM JUROS", $page, $titulo_novo, $produto_filial);
                                        
                    //Atualizar produto FULL, conta principal
                    //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, "CONTA PRINCIPAL - FULL", $page, $titulo_novo, $produto_filial);
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id,$meli_origem, $page, $title, $produto_filial_e_nome_alterado){
        
    	echo "\nORIGEM: ".$meli_origem;
    	
    	$response_item = $meli->get('https://api.mercadolibre.com/items/'.$meli_id.'?access_token='.$token);
    	
    	if(!isset($response_item["body"]->title)){
    	    return;
    	}
    	
    	print_r($response_item["body"]->title); 
    	
    	if (strpos($response_item["body"]->title, "Para Scania")){
    	    echo " - Possui PARA SCANIA";
    	}
    	else{
    	    if (strpos($response_item["body"]->title, "Scania")){
    	        echo " - Possui SCANIA";
    	        
    	        $body = [
    	            "status"                => "paused"
    	        ];
    	        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
    	        if ($response['httpCode'] >= 300) {
    	            echo " - Produto não pausado";
    	        }
    	        else {
                    echo " - Produto pausado";
                    
                    $produto_filial_correcao = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    
                    $marca_produto = MarcaProduto::find()->andWhere(['=','id',$produto_filial_correcao->produto->marca_produto_id])->one();
                    $marca = "OPT";
                    if($marca_produto){
                        $marca = $marca_produto->nome;
                    }
                    
                    
                    $produto_condicao = ProdutoCondicao::find()->andWhere(['=', 'id', $produto_filial_correcao->produto->produto_condicao_id])->one();
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    if($produto_condicao){
                        switch ($produto_condicao->meli_id){
                            case "new":
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                                break;
                            case "used":
                                $condicao = "used";
                                $condicao_id = "2230581";
                                $condicao_name = "Usado";
                                break;
                            case "recondicionado":
                                $condicao = "new";
                                $condicao_id = "2230582";
                                $condicao_name = "Recondicionado";
                                break;
                            default:
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                        }
                    };
                    
                    $body = [
                        "title" => mb_substr($title,0,60),
                        "category_id" => utf8_encode($response_item["body"]->category_id),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode($response_item["body"]->price),
                        "available_quantity" => utf8_encode($response_item["body"]->available_quantity),
                        "seller_custom_field" =>utf8_encode($produto_filial_correcao->id),
                        "condition" => $response_item["body"]->condition,
                        "description" => ["plain_text" => $page],//utf8_encode($page)],
                        "pictures" => $produto_filial_correcao->produto->getUrlImagesML(),
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
                        ],
                        'attributes' =>[
                            [
                                'id'                    => 'PART_NUMBER',
                                'name'                  => 'Número de peça',
                                'value_id'              => null,
                                'value_name'            => $produto_filial_correcao->produto->codigo_global,
                                'value_struct'          => null,
                                'values'                => [[
                                    'id'    => null,
                                    'name'  => $produto_filial_correcao->produto->codigo_global,
                                    'struct'=> null,
                                ]],
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                            ],
                            [
                                "id"=> "BRAND",
                                "name"=> "Marca",
                                "value_id"=> null,
                                "value_name"=> $marca,
                                "value_struct"=> null,
                                "attribute_group_id"=> "OTHERS",
                                "attribute_group_name"=> "Outros"
                            ],
                            [
                                'id'                    => 'ITEM_CONDITION',
                                'name'                  => 'Condição do item',
                                'value_id'              => $condicao_id,
                                'value_name'            => $condicao_name,
                                'value_struct'          => null,
                                'values'                => [[
                                    'id'    => $condicao_id,
                                    'name'  => $condicao_name,
                                    'struct'=> null,
                                ]],
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                            ]
                        ]
                        
                    ];
                    //print_r($body); //die;
                    $response = $meli->post("items?access_token=" . $token,$body);

                    if ($response['httpCode'] >= 300) {
                        print_r($response);
                        //print_r($body);
                        fwrite($arquivo_log, "\n".$produto_filial_correcao->id.";;erro");
                        echo "erro";
                    } else {
                        $produto_filial_correcao->meli_id = $response['body']->id;
                        
                        $meli_salvo = ($produto_filial_correcao->save() ? "meli_id salvo" : "meli_id não salvo");
                        
                        echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                        fwrite($arquivo_log, "\n".$produto_filial_correcao->id.";".ArrayHelper::getValue($response, 'body.permalink').";ok;".$meli_salvo);
                       
                    }
    	        }
    	    }
    	    else{
    	        echo " - NÃO possui SCANIA";
    	    }
    	}

    }
}
