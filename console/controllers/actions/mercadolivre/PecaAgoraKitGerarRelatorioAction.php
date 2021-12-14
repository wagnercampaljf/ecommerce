<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class PecaAgoraKitGerarRelatorioAction extends Action
{
    public function run()//$filial_id)
    {

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio Frete Produtos - Conta Antiga";
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

	    //print_r($response); die;
	   
        //TESTE
        //$meliAccessToken = $response->access_token;
	    //echo "\n\n"; print_r($meliAccessToken); echo "\n\n"; die;
        /*$response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&q=2515253287&access_token=' . $meliAccessToken);
        print_r($response_order); die;*/
        //TESTE

        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            $x = 0;
            $y = 0;
            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                //break;
                
                echo "\n".$x;
                
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                    
                    //print_r($venda); die;
                    
                    $response_shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$venda->shipping->id.'?access_token=' . $meliAccessToken);
                    if($response_shipping["httpCode"] > 300){
                        continue;
                    }
                    
                    $shipments_dados = ArrayHelper::getValue($response_shipping, "body");
                    //echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";print_r($shipments_dados); print_r($shipments_dados->mode);
                    
                    //if(!isset($venda->shipping->shipping_mode)){continue;} //Verificação se houve envio
                    if(!isset($venda->shipping->id)){continue;}
                    
                    //if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                    if($shipments_dados->mode =='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));
                        
                        //foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                        foreach(ArrayHelper::getValue($shipments_dados, 'shipping_items') as $itens){
                            //if(ArrayHelper::getValue($itens, 'id') != "MLB1450438520"){continue;}
                            
                            $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            if($response_valor_dimensao["httpCode"] > 300){
                                continue;
                            }
                            
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                            
                            echo " - ".ArrayHelper::getValue($itens, 'id');
                            
                            /*if(ArrayHelper::getValue($itens, 'id') == "MLB1322989801"){
                             print_r($response_valor_dimensao);
                             die;
                             }*/
                            
                        }
                    }
                }

                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        echo "\nFim Frete Produtos - Conta Antiga";
        //Código de criação da tabela de preços baseadas no ME - Antigo

        //print_r($produto_frete); die;

        //Código de criação da tabela de preços baseadas no ME - Novo
        echo "\nInicio Frete Produtos - Conta Nova";
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');

        if (is_object($response_conta_duplicada) && ArrayHelper::getValue($user_conta_duplicada, 'httpCode') < 400) {
            $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
                
            $x = 0;
            $y = 0;
            $data_atual = date('Y-m-d');
            
            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken_conta_duplicada);

            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                //break;

                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
            	    
                    $response_shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$venda->shipping->id.'?access_token=' . $meliAccessToken_conta_duplicada);
                    if($response_shipping["httpCode"] >= 300){
                        continue;
                    }
                    
                    $shipments_dados = ArrayHelper::getValue($response_shipping, "body");
                    
                    //if(!isset($venda->shipping->shipping_mode)){continue;}
                    if(!isset($venda->shipping->id)){continue;}

                    //if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                    if($shipments_dados->mode =='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));

                        //foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                        foreach(ArrayHelper::getValue($shipments_dados, 'shipping_items') as $itens){
                            //if(ArrayHelper::getValue($itens, 'id') != "MLB1522881748"){continue;}
                         
                            $response_valor_dimensao = $meli->get("/users/435343067/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            if($response_valor_dimensao["httpCode"] >= 300){
                                continue;
                            }
                            
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                            
                            /*if(ArrayHelper::getValue($itens, 'id') == "MLB1322989801"){
                                print_r($response_valor_dimensao);
                                die;
                            }*/
                        }
                    }
                }
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken_conta_duplicada);
            }
        }

        echo "\nFim Frete Produtos - Conta Nova";
        //Código de criação da tabela de preços baseadas no ME - Novo

        //die;
        
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
        
        print_r($produto_frete); 
        //die;
        //Tabela de fretes recomendados pelo ML - TÉRMINO
        
        echo "\n\nComeço da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [77]])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            
            $nome_arquivo = "/var/tmp/peca_agora_kit_ml_".date("Y-m-d_H-i-s").".csv";
            $arquivo_log = fopen($nome_arquivo, "a");
            // Escreve no log
            fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
            fwrite($arquivo_log, "produto_filial_id;produto_id;filial_id;preco_antes;preço;tipo;quantidade;meli_id;status");
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;

                $produto_filiais = $filial  ->getProdutoFilials()
                                            //->andWhere(['is not','meli_id',null])
                                            ->where(" meli_id is not null and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB864667465']])
                                            //->andWhere(['produto_filial.produto_id' => [44524]])
                                            //->andWhere(['produto_filial.id' => [436041]])
                                            //->joinWith('produto')
                                            //->andWhere(['like','produto.nome', 'CAPA PORCA'])
                                            //->andWhere(['=','e_preco_alterado',true])
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

        	    //if($produto_filial->filial_id == 43 && $k <= 17535){
                    //    continue;
        	    //}
                    
                    $preco = round($produto_filial->getValorMercadoLivre(), 2);

        		    /*if($produto_filial->filial_id == 77){
        			     $preco *= 1.3;
        		    }*/

                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produto_filial->id])->one();
                    
                    echo " - Conta Principal: ".$produto_filial->id;

        		    if($produto_filial_conta_duplicada){
                        echo " - Conta Duplicada: ".$produto_filial_conta_duplicada->id;
        		    }

                    //$preco = round($produto_filial->getValorMercadoLivre(), 2);
                    echo " ( ".$preco;
                    //if (ArrayHelper::keyExists($produto_filial->meli_id, $produto_frete, false)){
                   
                    //PRINCIPAL - PADRÃO///////////////////////////////////////////////////////
                    if (array_key_exists($produto_filial->meli_id, $produto_frete)){
                        echo " - COM VENDA (PADRÃO)";
                        if($preco>=510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id)/2)*1.1, 2);
                            $preco = $preco-10;
                        }
                        elseif($preco > 99 && $preco < 510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id)/2)*1.1, 2);
                            $preco =  $preco-16;
                        }
                    }
                    //PRINCIPAL - SEM JUROS///////////////////////////////////////////////////////
                    elseif (array_key_exists($produto_filial->meli_id_sem_juros, $produto_frete)){
                        echo " - COM VENDA (SEM JUROS)";
                        if($preco>=510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_sem_juros)/2)*1.1, 2);
                            $preco = $preco-10;
                        }
                        elseif($preco > 99 && $preco < 510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_sem_juros)/2)*1.1, 2);
                            $preco =  $preco-16;
                        }
                    }
                    //PRINCIPAL - FULL///////////////////////////////////////////////////////
                    elseif (array_key_exists($produto_filial->meli_id_full, $produto_frete)){
                        echo " - COM VENDA (FULL)";
                        if($preco>=510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_full)/2)*1.1, 2);
                            $preco = $preco-10;
                        }
                        elseif($preco > 99 && $preco < 510){
                            $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial->meli_id_full)/2)*1.1, 2);
                            $preco =  $preco-16;
                        }
                    }
                    elseif($produto_filial_conta_duplicada){
                        //CONTA DUPLICADA - PADRÃO///////////////////////////////////////////////////////
                        if (array_key_exists($produto_filial_conta_duplicada->meli_id, $produto_frete)){
                            echo " - COM VENDA (DUPLICADA)";
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id)/2)*1.1, 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 99 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id)/2)*1.1, 2);
                                $preco =  $preco-16;
                            }
                        }
                        //CONTA DUPLICADA - SEM JUROS///////////////////////////////////////////////////////
                        elseif (array_key_exists($produto_filial_conta_duplicada->meli_id_sem_juros, $produto_frete)){
                            echo " - COM VENDA - SEM JUROS (DUPLICADA)";
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_sem_juros)/2)*1.1, 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 99 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_sem_juros)/2)*1.1, 2);
                                $preco =  $preco-16;
                            }
                        }
                        //CONTA DUPLICADA - FULL///////////////////////////////////////////////////////
                        elseif (array_key_exists($produto_filial_conta_duplicada->meli_id_full, $produto_frete)){
                            echo " - COM VENDA - FULL (DUPLICADA)";
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_full)/2)*1.1, 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 99 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_conta_duplicada->meli_id_full)/2)*1.1, 2);
                                $preco =  $preco-16;
                            }
                        }
                        else{
                            if (array_key_exists($produto_filial_conta_duplicada->meli_id, $produto_frete_ml)){
                                echo " - SEM VENDA (DUPLICADA)";
                                if($preco>=510){
                                    $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.1, 2);
                                    $preco = $preco-10;
                                }
                                elseif($preco > 99 && $preco < 510){
                                    $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.1, 2);
                                    $preco =  $preco-16;
                                }
                            }
                        }
                    }
                    else{
                        if (array_key_exists($produto_filial->meli_id, $produto_frete_ml)){
                            echo " - SEM VENDA";
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.1, 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 99 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete_ml, $produto_filial->meli_id)/2)*1.1, 2);
                                $preco =  $preco-16;
                            }
                        }
                    }

                    
                    echo " - ".$preco." )";
                    
                    //$preco_conta_principal = $preco; //
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id.'?access_token='.$meliAccessToken);
                    /*print_r($response_tipo_anuncio);
                    
                    print_r($response_tipo_anuncio["body"]->price); echo "\n";
                    print_r($response_tipo_anuncio["body"]->status); echo "\n";
                    die;*/
                    
                    $preco_me_principal = $preco;
                    $status_me = false;
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
                        
                        if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                            $preco_conta_principal = $preco_me_principal*1.064;
                        }
                    }
                    
                    echo " - ".(($status_me?"ME":"Sem ME"))." - ".$preco; 
                    //continue;
                    
                    //Atualizar produto principal, conta principal
                    $preco_conta_principal = $preco_me_principal;
                    fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->produto_id.";".$produto_filial->filial_id.";".$preco_conta_principal.";".$response_tipo_anuncio["body"]->price.";Principal;".$produto_filial->quantidade.";".$produto_filial->meli_id.";".$response_tipo_anuncio["body"]->status);
                    //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, $preco_conta_principal, $produto_filial->quantidade, null , "CONTA PRINCIPAL - PRINCIPAL", $produto_filial);

                    //Atualizar produto SEM JUROS, conta principal
                    if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                        $preco_conta_principal_sem_juros = $preco;
                        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id_sem_juros.'?access_token='.$meliAccessToken);
                        /*if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
                            if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){*/
                        $preco_conta_principal_sem_juros = $preco*1.064;
                            //}
                        //}

                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->produto_id.";".$produto_filial->filial_id.";".$preco_conta_principal_sem_juros.";".$response_tipo_anuncio["body"]->price.";Sem Juros;".$produto_filial->quantidade.";".$produto_filial->meli_id_sem_juros.";".$response_tipo_anuncio["body"]->status);
                        //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, $preco_conta_principal_sem_juros, $produto_filial->quantidade, null, "CONTA PRINCIPAL - SEM JUROS", $produto_filial);
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
                        
                        fwrite($arquivo_log, "\n".$produto_filial->id.";".$produto_filial->produto_id.";".$produto_filial->filial_id.";".$preco_conta_principal_full.";".$response_tipo_anuncio["body"]->price.";Full;".$produto_filial->quantidade.";".$produto_filial->meli_id_full.";".$response_tipo_anuncio["body"]->status);
                        //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, $preco_conta_principal_full, $produto_filial->quantidade, null, "CONTA PRINCIPAL - FULL", $produto_filial);
                    }
                    
                    if($produto_filial_conta_duplicada){
			
                        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);   
    
                        $status_me = false;
                        if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
    
                            //Se o anuncio n  o for por ME, remover o frete.
                            //print_r($response_tipo_anuncio);die;
                            $response_categoria = $meli->get('https://api.mercadolibre.com/categories/'.ArrayHelper::getValue($response_tipo_anuncio, 'body.category_id'));
                            //print_r($response_categoria);die;
                            if ($response_categoria['httpCode'] >= 300) {
                                echo " - categoria n  o encontrada";
                            }
                            else{
    
                                foreach(ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes') as $modo_envio){
                                    if($modo_envio == "me2"){
                                        $status_me = true;
                                        break;
                                    }
                                }

                                if(!$status_me){
                                    if($preco>=510){
	    		                $preco = $preco-10;
                                    }
                                    elseif($preco > 120 && $preco < 510){
                                        $preco =  $preco-16;
                                    }
                                }
                            }

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
                        
                        if(isset($response_tipo_anuncio["body"]->price)){
                            fwrite($arquivo_log, "\n".$produto_filial_conta_duplicada->id.";".$produto_filial_conta_duplicada->produto_id.";".$produto_filial_conta_duplicada->filial_id.";".$preco_conta_duplicada.";".$response_tipo_anuncio["body"]->price.";Principal;".$produto_filial_conta_duplicada->quantidade.";".$produto_filial_conta_duplicada->meli_id.";".$response_tipo_anuncio["body"]->status);
                        }
                        
                        //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, $preco_conta_duplicada, $produto_filial->quantidade, null, "CONTA DUPLICADA - PRINCIPAL", $produto_filial_conta_duplicada);
                        
                        //Atualizar produto SEM JUROS, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $preco_conta_duplicada_sem_juros = $preco;
                            $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_sem_juros.'?access_token='.$meliAccessToken_conta_duplicada);
                            if ($response_tipo_anuncio['httpCode'] >= 300) {
                                echo " - produto nao encontrado no ML, tipo de anuncio";
                            }
                            else {
                                if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                    $preco_conta_duplicada_sem_juros = $preco*1.064;
                                }
                            }
                            
                            fwrite($arquivo_log, "\n".$produto_filial_conta_duplicada->id.";".$produto_filial_conta_duplicada->produto_id.";".$produto_filial_conta_duplicada->filial_id.";".$preco_conta_duplicada_sem_juros.";".$response_tipo_anuncio["body"]->price.";Sem Juros;".$produto_filial_conta_duplicada->quantidade.";".$produto_filial_conta_duplicada->meli_id.";".$response_tipo_anuncio["body"]->status);
                            //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, $preco_conta_duplicada_sem_juros, $produto_filial->quantidade, null, "CONTA DUPLICADA - SEM JUROS", $produto_filial_conta_duplicada);
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
                            
                            fwrite($arquivo_log, "\n".$produto_filial_conta_duplicada->id.";".$produto_filial_conta_duplicada->produto_id.";".$produto_filial_conta_duplicada->filial_id.";".$preco_conta_duplicada_full.";".$response_tipo_anuncio["body"]->price.";Full;".$produto_filial_conta_duplicada->quantidade.";".$produto_filial_conta_duplicada->meli_id.";".$response_tipo_anuncio["body"]->status);
                            //$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_full, $preco_conta_duplicada_full, $produto_filial->quantidade, null, "CONTA DUPLICADA - FULL", $produto_filial_conta_duplicada);
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
}
