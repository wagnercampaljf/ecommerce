<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreShipmentsItem;
use common\models\PedidoMercadoLivreShipments;

class UpdatePrecoPorFilialPecaAgoraAction extends Action
{
    public function run($filial_id = 0)
    {

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio Frete Produtos - Conta Antiga";
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
print_r($response);
        $meliAccessToken = $response->access_token;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

	    print_r($response); 
	    print_r($response_conta_duplicada);
	    //die;
	   
        //TESTE
            //$meliAccessToken = $response->access_token;
            //echo "\n\n"; print_r($meliAccessToken); echo "\n\n"; die;
            /*$response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&q=2515253287&access_token=' . $meliAccessToken);
            print_r($response_order); die;*/
        //TESTE

        $produto_frete['0'] = 0;
        $podutos_com_venda  = array();

        $x = 0;
        $y = 0;
        $data_atual = date('Y-m-d');

        $pedidos_mercado_livre = PedidoMercadoLivre::find()->orderBy(['id'=>SORT_ASC])->all();
        
        foreach($pedidos_mercado_livre as $i => $pedido_mercado_livre){
            
	    //break; //comentar
            
            if(is_null($pedido_mercado_livre->shipping_id || $pedido_mercado_livre->shipping_id == "")){continue;}
            
            $user_id = $pedido_mercado_livre->user_id;
            $token = $meliAccessToken;
            if($user_id == "435343067"){
                $token = $meliAccessToken_conta_duplicada;
            }
            
            /*$response_shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$pedido_mercado_livre->shipping_id.'?access_token=' . $token);
            //print_r($response_shipping); die;
            if($response_shipping["httpCode"] > 300){
                continue;
            }*/
            
            $pedido_mercado_livre_shipping = PedidoMercadoLivreShipments::find()->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])->one();
            if(!$pedido_mercado_livre_shipping){
                continue;
            }

            if($pedido_mercado_livre_shipping->mode =='me2'){
                $y++;
                echo "\n".$y." - ";print_r($pedido_mercado_livre->pedido_meli_id);
                
                $pedido_mercado_livre_shipping_itens = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipping->id])->orderBy(['id'=>SORT_ASC])->all();
                //foreach(ArrayHelper::getValue($shipments_dados, 'shipping_items') as $itens){
                foreach($pedido_mercado_livre_shipping_itens as $u => $pedido_mercado_livre_shipping_item){

                    if($pedido_mercado_livre_shipping_item->meli_id != "MLB1660801399"){continue; }
                    
                    $response_valor_dimensao = $meli->get("/users/".$user_id."/shipping_options/free?dimensions=".str_replace(".0","",$pedido_mercado_livre_shipping_item->dimensions));

                    if($response_valor_dimensao["httpCode"] > 300){
                        continue;
                    }
                    
                    $produto_frete[$pedido_mercado_livre_shipping_item->meli_id] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                    
                    $podutos_com_venda[] = $pedido_mercado_livre_shipping_item->meli_id;
                    
                    echo " - ".$pedido_mercado_livre_shipping_item->meli_id;
                    /*if(ArrayHelper::getValue($itens, 'id') == "MLB1322989801"){
                     print_r($response_valor_dimensao);
                     die;
                     }*/
                    
                }
            }
        }

        echo "\nFim Frete Produtos";
        //Código de criação da tabela de preços baseadas no ME 

        print_r($produto_frete);        
        
        //Tabela de fretes recomendados pelo ML - INICIO
        $produto_frete_ml['0'] = 0;
        
        $x = 0;
        $file = fopen("/var/tmp/arquivo_fretes.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            //print_r($line);
            //echo "\n".$x++." - ".$line[0];
            
            if($line[1] <= 0){continue;}
            
            $produto_frete_ml[$line[0]] = $line[1];
            
        }
        fclose($file);
        
        echo "Produtos Frete: "; 
        
        //print_r($produto_frete); 
        //die;
        //Tabela de fretes recomendados pelo ML - TÉRMINO
        
        echo "\n\nComeço da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = new Filial;
        if($filial_id == 0){
            $filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->orderBy('id')
                                    ->all();
        }
        else{
            $filials = Filial::find()->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->andWhere(['id' => [$filial_id]])
                                    ->orderBy('id')
                                    ->all();
        }
        

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
//continue;            
            $nome_arquivo = "/var/tmp/log_update_preco_ml_".str_replace(" ", "", $filial->nome)."_".date("Y-m-d_H-i-s").".csv";
            $arquivo_log = fopen($nome_arquivo, "a");
            // Escreve no log
            fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
            fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            
            print_r($response);

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;

                $produto_filiais = $filial  ->getProdutoFilials()
                                            //->andWhere(['is not','meli_id',null])
                                            ->where(" ((meli_id is not null and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) and e_preco_alterado ) or meli_id in (select distinct meli_id from pedido_mercado_livre_shipments_item)  or id in (select produto_filial.produto_filial_origem_id from produto_filial where meli_id in (select distinct meli_id from pedido_mercado_livre_shipments_item))) and produto_filial.atualizar_preco_mercado_livre ")
					    //->where(" id in (select distinct produto_filial_id from valor_produto_filial where valor > 74 and valor < 95) ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1660803192']])
                                            //->andWhere(['produto_filial.produto_id' => [31407]])
                                            ->andWhere(['produto_filial.id' => [499734]])
                                            //->joinWith('produto')
                                            //->andWhere(['like','produto.nome', 'CAPA PORCA'])
                                            //->andWhere(['=','e_preco_alterado',true])
                                            //->orWhere(["meli_id" => $podutos_com_venda])
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
                    
                    echo "\n ==> ".$k." - ".$produto_filial->id." - Quantidade: ".$produto_filial->quantidade;
                    
                    //continue;
                    
                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produto_filial->id])->one();
                    
                    echo " - Conta Principal: ".$produto_filial->id;

        		    if($produto_filial_conta_duplicada){
                        echo " - Conta Duplicada: ".$produto_filial_conta_duplicada->id;
        		    }

                    $preco = round($produto_filial->getValorMercadoLivre(), 2);
                    echo " (* ".$preco;
                    //if (ArrayHelper::keyExists($produto_filial->meli_id, $produto_frete, false)){
                   
		    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id.'?access_token='.$meliAccessToken);
		    echo print_r($response_tipo_anuncio["body"]->shipping->mode);

                    //PRINCIPAL - PADRÃO///////////////////////////////////////////////////////
                    if($preco >= 80){
                        if (array_key_exists($produto_filial->meli_id, $produto_frete)){
                            echo " - COM VENDA (PADRÃO)";
                            if($preco >= 511){
                                $preco = $preco - 11;
                            } 
                            elseif($preco<=97){
                                $preco = $preco - 6;
                            } 
                            elseif($preco > 97 && $preco < 511){
                                $preco = $preco - 17;
                            }
                            
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id)/2)*1.2, 2);
                        }
                        //PRINCIPAL - SEM JUROS///////////////////////////////////////////////////////
                        elseif (array_key_exists($produto_filial->meli_id_sem_juros, $produto_frete)){
                            echo " - COM VENDA (SEM JUROS)";
                            if($preco >= 511){
                                $preco = $preco - 11;
                            }
                            elseif($preco<=97){
                                $preco = $preco - 6;
                            }
                            elseif($preco > 97 && $preco < 511){
                                $preco = $preco - 17;
                            }
                            
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_sem_juros)/2)*1.2, 2);
                        }
                        //PRINCIPAL - FULL///////////////////////////////////////////////////////
                        elseif (array_key_exists($produto_filial->meli_id_full, $produto_frete)){
                            echo " - COM VENDA (FULL)";
                            if($preco >= 511){
                                $preco = $preco - 11;
                            }
                            elseif($preco<=97){
                                $preco = $preco - 6;
                            }
                            elseif($preco > 97 && $preco < 511){
                                $preco = $preco - 17;
                            }
                            
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_full)/2)*1.2, 2);
                        }
                        elseif($produto_filial_conta_duplicada){
                            //CONTA DUPLICADA - PADRÃO///////////////////////////////////////////////////////
                            if (array_key_exists($produto_filial_conta_duplicada->meli_id, $produto_frete)){
                                echo " - COM VENDA (DUPLICADA)";
                                if($preco >= 511){
                                    $preco = $preco - 11;
                                }
                                elseif($preco<=97){
                                    $preco = $preco - 6;
                                }
                                elseif($preco > 97 && $preco < 511){
                                    $preco = $preco - 17;
                                }
                                
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id)/2)*1.2, 2);
                            }
                            //CONTA DUPLICADA - SEM JUROS///////////////////////////////////////////////////////
                            elseif (array_key_exists($produto_filial_conta_duplicada->meli_id_sem_juros, $produto_frete)){
                                echo " - COM VENDA - SEM JUROS (DUPLICADA)";
                                if($preco >= 511){
                                    $preco = $preco - 11;
                                }
                                elseif($preco<=97){
                                    $preco = $preco - 6;
                                }
                                elseif($preco > 97 && $preco < 511){
                                    $preco = $preco - 17;
                                }
                                
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_sem_juros)/2)*1.2, 2);
                            }
                            //CONTA DUPLICADA - FULL///////////////////////////////////////////////////////
                            elseif (array_key_exists($produto_filial_conta_duplicada->meli_id_full, $produto_frete)){
                                echo " - COM VENDA - FULL (DUPLICADA)";
                                if($preco >= 511){
                                    $preco = $preco - 11;
                                }
                                elseif($preco<=97){
                                    $preco = $preco - 6;
                                }
                                elseif($preco > 97 && $preco < 511){
                                    $preco = $preco - 17;
                                }
                                
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_full)/2)*1.2, 2);
                            }
                            else{
                                if (array_key_exists($produto_filial_conta_duplicada->meli_id, $produto_frete_ml)){
                                    echo " - SEM VENDA (DUPLICADA)";
                                    if($preco >= 511){
                                        $preco = $preco - 11;
                                    }
                                    elseif($preco<=97){
                                        $preco = $preco - 6;
                                    }
                                    elseif($preco > 97 && $preco < 511){
                                        $preco = $preco - 17;
                                    }
                                    
                                    $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.2, 2);
                                }
                            }
                        }
                        else{
                            if (array_key_exists($produto_filial->meli_id, $produto_frete_ml)){
                                echo " - SEM VENDA";
                                if($preco >= 511){
                                    $preco = $preco - 11;
                                }
                                elseif($preco<=97){
                                    $preco = $preco - 6;
                                }
                                elseif($preco > 97 && $preco < 511){
                                    $preco = $preco - 17;
                                }
                                
                                $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.2, 2);
                            }
                        }
                    }

                    
                    echo " - ".$preco." )";
                    
                    //$preco_conta_principal = $preco; //
                    //$response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id.'?access_token='.$meliAccessToken);
                    //print_r($response_tipo_anuncio); die;
                    
                    $preco_me_principal = $preco;
                    /*$status_me = false;
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio";
                    }
                    else {
                        
                        //Se o anuncio não for por ME, remover o frete.
                        //print_r($response_tipo_anuncio);die;
                        $response_categoria = $meli->get('https://api.mercadolibre.com/categories/'.ArrayHelper::getValue($response_tipo_anuncio, 'body.category_id'));
                        //print_r($response_categoria);die;
                        if ($response_categoria['httpCode'] >= 300) {
                            echo " - categoria não encontrada";
                        }
                        else{
                            
                            foreach(ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes') as $modo_envio){
                                if($modo_envio == "me2"){
                                    $status_me = true;
                                    break;
                                }
                            }
                            
                            if(!$status_me){
                                if($preco_me_principal>=510){
                                    $preco_me_principal = $preco_me_principal-10;
                                }
                                elseif($preco_me_principal > 120 && $preco_me_principal < 510){
                                    $preco_me_principal =  $preco_me_principal-16;
                                }
                            }
                        }
                    }                    
                    echo " - ".(($status_me?"ME":"Sem ME"))." - ".$preco;*/ 
                    
                    //continue;
                    
                    if(isset($response_tipo_anuncio['body']->listing_type_id)){
                        if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                            $preco_conta_principal = $preco_me_principal*1.064;
                        }
                    }
                    
                    //Atualizar produto principal, conta principal
                    $preco_conta_principal = $preco_me_principal;
                    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, $preco_conta_principal, $produto_filial->quantidade, null , "CONTA PRINCIPAL - PRINCIPAL", $produto_filial); //ArrayHelper::getValue($response_tipo_anuncio, 'body.variations'));

                    //Atualizar produto SEM JUROS, conta principal
                    if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                        $preco_conta_principal_sem_juros = $preco;
                        $preco_conta_principal_sem_juros = $preco*1.064;

                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, $preco_conta_principal_sem_juros, $produto_filial->quantidade, null, "CONTA PRINCIPAL - SEM JUROS", $produto_filial);
                    }
                    
                    //Atualizar produto FULL, conta principal
                    if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                        $preco_conta_principal_full = $preco;
                        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id_full.'?access_token='.$meliAccessToken);
                        if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
                            if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                $preco_conta_principal_full = $preco*1.064;
                            }
                        }
                        
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, $preco_conta_principal_full, $produto_filial->quantidade, null, "CONTA PRINCIPAL - FULL", $produto_filial);
                    }
                    
                    if($produto_filial_conta_duplicada){
			
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);   
    
                        $status_me = false;
                        if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
                                if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                $preco_conta_principal = $preco*1.064;
                            }
                        }

                        echo " - ".(($status_me?"ME":"Sem ME"))." - ".$preco;
                        //continue;
                        
                        //Atualizar produto principal, conta duplicada
                        $preco_conta_duplicada = $preco;
                        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);
                        if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
                            if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                $preco_conta_duplicada = $preco*1.064;
                            }
                        }
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, $preco_conta_duplicada, $produto_filial->quantidade, null, "CONTA DUPLICADA - PRINCIPAL", $produto_filial_conta_duplicada);
                        
                        //Atualizar produto SEM JUROS, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $preco_conta_duplicada_sem_juros = $preco;
                            $preco_conta_duplicada_sem_juros = $preco*1.064;

                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, $preco_conta_duplicada_sem_juros, $produto_filial->quantidade, null, "CONTA DUPLICADA - SEM JUROS", $produto_filial_conta_duplicada);
                        }
                        
                        //Atualizar produto FULL, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $preco_conta_duplicada_full = $preco;
                            $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_full.'?access_token='.$meliAccessToken_conta_duplicada);
                            if ($response_tipo_anuncio['httpCode'] >= 300) {
                                echo " - produto nao encontrado no ML, tipo de anuncio";
                            }
                            else {
                                if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                    $preco_conta_duplicada_full = $preco*1.064;
                                }
                            }
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_full, $preco_conta_duplicada_full, $produto_filial->quantidade, null, "CONTA DUPLICADA - FULL", $produto_filial_conta_duplicada);
                        }
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
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id, $preco, $quantidade, $modo = "me2", $meli_origem, $produto_filial_e_preco_alterado){//, $variacoes){
        
        echo "\nORIGEM: ".$meli_origem;
        
        fwrite($arquivo_log, "\n".$meli_id.";".$meli_origem.";".$produto_filial_e_preco_alterado->filial_id);

    	$status_preco		= false;

    	
    	//Se valor do produto for menor que 100 reais, alterar removendo o frete grátis
    	/*if($preco < 98){
    	    $body = [
    	        "shipping"      => [
    	            "mode"          => "me2",
    	            "methods"       => [],
    	            "local_pick_up" => true,
    	            "free_shipping" => false,
    	            "logistic_type" => "cross_docking",
    	        ],
    	    ];
    	    
    	    $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
    	    if ($response['httpCode'] >= 300) {
    	        echo " - Frete: Erro";
    	        fwrite($arquivo_log, ";Frete não alterado");
    	    }
    	    else {
    	        echo " - Frete: Ok \nLink: ".ArrayHelper::getValue($response, 'body.permalink')."\n";
    	        $status_preco = true;
    	        fwrite($arquivo_log, ";Frete alterado");
    	    }
    	}
    	elseif($preco >= 98 && $preco < 100){
    	    $preço = 110.01;
    	}*/
    	
       
        //Atualização Preço
        if($preco >= 98 && $preco < 100){
            $preço = 110.01;
        }

	$body = [
                "price"     => round($preco,2),
                //"variation" => [],
        ];
                
        //Variações
        /*foreach($variacoes as $i => $variacao){
            //print_r($variacao);
            $body['variation'][] = [
                "id" => $variacao->id,
                "price" => round($preco,2),
                "attribute_combinations" => [
                    "id" => $variacao->attribute_combinations[0]->id,
                    "name" => $variacao->attribute_combinations[0]->name,
                    "value_id" => $variacao->attribute_combinations[0]->value_id,
                    "value_name" => $variacao->attribute_combinations[0]->value_name,
                    "value_struct" => $variacao->attribute_combinations[0]->value_struct,
                    "values" => [
                        "id" => $variacao->attribute_combinations[0]->values[0]->id,
                        "name" => $variacao->attribute_combinations[0]->values[0]->name,
                        "struct" => $variacao->attribute_combinations[0]->values[0]->struct,
                    ]
                ],
                "available_quantity" => $quantidade,
                "sold_quantity" => $variacao->sold_quantity,
                "sale_terms" => $variacao->sale_terms,
                "picture_ids" => $variacao->picture_ids,
                "seller_custom_field" => $variacao->seller_custom_field,
                "catalog_product_id" => $variacao->catalog_product_id,
                "inventory_id" => $variacao->inventory_id,
                "item_relations" => $variacao->item_relations,
            ];
        }*/
        
        //print_r($body);
        
        //die;

        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
        //print_r($response);
        if ($response['httpCode'] >= 300) {
            echo "\nPreço: Erro";
            fwrite($arquivo_log, ";Preço não alterado");
        }
        else {
            echo "nPreço: Ok \nLink: ".ArrayHelper::getValue($response, 'body.permalink')."\n";
            $status_preco = true;
            fwrite($arquivo_log, ";Preço alterado");
        }
        
        
	//Se valor do produto for menor que 100 reais, alterar removendo o frete grátis
        $body = [
                "shipping"      => [
                    "mode"          => "me2",
                    "methods"       => [],
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "logistic_type" => "cross_docking",
                ],
        ];

        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
        if ($response['httpCode'] >= 300) {
                echo " - Frete: Erro";
                fwrite($arquivo_log, ";Frete não alterado");
        }
        else {
                echo " - Frete: Ok \nLink: ".ArrayHelper::getValue($response, 'body.permalink')."\n";
		$status_preco = true;
                fwrite($arquivo_log, ";Frete alterado");
        }
        
        
        echo "Preço: ".$status_preco;

        if($status_preco){
    		$produto_filial_e_preco_alterado->e_preco_alterado = false;
    		if($produto_filial_e_preco_alterado->save()){
    		    echo " - STATUS ALTERADO";
    		}
    		else{
    		    echo " - STATUS NÃO ALTERADO";
    		}
    	}
    }
}
