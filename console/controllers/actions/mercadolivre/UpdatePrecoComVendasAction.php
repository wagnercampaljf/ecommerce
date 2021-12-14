<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdatePrecoComVendasAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_preco_ml_com_vendas_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio Frete Produtos - Conta Antiga";
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            //echo "\n\n".$meliAccessToken."\n\n";
            $x = 0;
            $y = 0;
            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
		
                //break;
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
		    //echo "\n".ArrayHelper::getValue($venda, 'id'); continue;

                    if(!isset($venda->shipping->shipping_mode)){continue;}

                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
			    //if(ArrayHelper::getValue($itens, 'id') != "MLB1322989775"){continue;}

			    echo " - ";print_r(ArrayHelper::getValue($itens, 'id'));

                            $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                        }
                    }
                }
                
                //break;
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        echo "\nFim Frete Produtos - Conta Antiga";
        //Código de criação da tabela de preços baseadas no ME - Antigo
        
	//print_r($produto_frete);
        //die;
        
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
		    //echo "\n".ArrayHelper::getValue($venda, 'id'); continue;

                    if(!isset($venda->shipping->shipping_mode)){continue;}

                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            //if(ArrayHelper::getValue($itens, 'id') != "MLB1322989775"){continue;}
                            
                            $response_valor_dimensao = $meli->get("/users/435343067/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                        }
                    }
                }
                
                //break;
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken_conta_duplicada);
            }
        }
        echo "\nFim Frete Produtos - Conta Nova";
        //Código de criação da tabela de preços baseadas no ME - Novo
        
        print_r($produto_frete);
        
	$x = 0;
        foreach($produto_frete as $meli_id_venda => $produto_frete_dados){
            echo "\n".$x++." - ".$meli_id_venda." - "; 

	    //continue;

	    //if($x < 2918){continue;}

            print_r($produto_frete_dados);
            //continue;
            $produtoFilial = ProdutoFilial::find()  ->orWhere(['=','meli_id',$meli_id_venda])
                                                    ->orWhere(['=','meli_id_sem_juros',$meli_id_venda])
                                                    ->one();
            
            if($produtoFilial) {
                echo " - ".$produtoFilial->id;

                $produto_filial                 = new ProdutoFilial;
                $produto_filial_conta_duplicada = new ProdutoFilial;
                if(!is_null($produtoFilial->produto_filial_origem_id)){
                    $produto_filial                 = ProdutoFilial::find()->andWhere(['=','id',$produtoFilial->produto_filial_origem_id])->one();
                    $produto_filial_conta_duplicada = $produtoFilial;
                }
                else{
                    $produto_filial                 = $produtoFilial;
                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->one();
                }
          
		if(!$produto_filial){ //Se nao encontrar os registros da conta principal, nao tem como alterar
			echo " - Sem produto na conta principal";
			continue;
		}

		//echo "\nConta principal: "; print_r($produto_filial);      
		//echo "\nConta Duplicada: "; print_r($produto_filial_conta_duplicada);

                echo " - Conta Principal: ".$produto_filial->id." - Conta Duplicada: ".(($produto_filial_conta_duplicada) ? $produto_filial_conta_duplicada->id : "SEM CONTA DUPLICADA");
                
                if (is_null($produto_filial->valorMaisRecente)) {
                    echo " - sem valor recente";
                    continue;
                }
                
                $preco = round($produto_filial->getValorMercadoLivre(), 2);
                echo " - ".$preco." - ".$produto_frete_dados;

                if($preco>=510){
                    $preco += round((($produto_frete_dados*1.1)/2), 2);
                    $preco = $preco-10;
                }
                elseif($preco > 120 && $preco < 510){
                    $preco += round((($produto_frete_dados*1.1)/2), 2);
                    $preco =  $preco-16;
                }
                echo " - ".$preco;
                
                $preco_conta_principal = $preco;
                $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id.'?access_token='.$meliAccessToken);
                if ($response_tipo_anuncio['httpCode'] >= 300) {
                    echo " - produto nao encontrado no ML, tipo de anuncio";
                }
                else {
                    if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                        $preco_conta_principal = $preco*1.05;
                    }
                }

                $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, $preco_conta_principal, $produto_filial->quantidade);
                
                if(!is_null($produto_filial->meli_id_sem_juros)){
                    $preco_conta_principal_sem_juros = $preco;
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id_sem_juros.'?access_token='.$meliAccessToken);
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio";
                    }
                    else {
                        if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                            $preco_conta_principal_sem_juros = $preco*1.05;
                        }
                    }
                    
                    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, $preco_conta_principal_sem_juros, $produto_filial->quantidade);
                }
                
                if($produto_filial_conta_duplicada){
                    $preco_conta_duplicada = $preco;
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id.'?access_token='.$meliAccessToken_conta_duplicada);
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio";
                    }
                    else {
                        if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                            $preco_conta_duplicada = $preco*1.05;
                        }
                    }
                    
                    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, $preco_conta_duplicada, $produto_filial->quantidade);
                    
                    if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros)){
                        $preco_conta_duplicada_sem_juros = $preco;
                        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial_conta_duplicada->meli_id_sem_juros.'?access_token='.$meliAccessToken_conta_duplicada);
                        if ($response_tipo_anuncio['httpCode'] >= 300) {
                            echo " - produto nao encontrado no ML, tipo de anuncio";
                        }
                        else {
                            if(ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id')=="gold_pro"){
                                $preco_conta_duplicada_sem_juros = $preco*1.05;
                            }
                        }
                        
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, $preco_conta_duplicada_sem_juros, $produto_filial->quantidade);
                    }
                }
            }
            else{
                echo " - produto nao encontrado";
            }
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id, $preco, $quantidade, $modo = "me2"){
        
        //Atualização Modo de Envio
        $body = [
            "shipping" => [
                "mode" => $modo,
                "local_pick_up" => true,
                "free_shipping" => false,
                "free_methods" => [],
            ],
        ];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
        if ($response['httpCode'] >= 300) {
            echo " - ERRO Modo";
            fwrite($arquivo_log, "\n".$meli_id.";Modo não alterado");
        }
        else {
            echo " - OK Modo";
            fwrite($arquivo_log, "\n".$meli_id.";Modo alterado");
        }
        
        //Atualização Quantidade
        $body = ["available_quantity" => $quantidade,];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
        if ($response['httpCode'] >= 300) {
            echo " - ERRO Quantidade";
            fwrite($arquivo_log, ";Quantidade não alterada");
        }
        else {
            echo " - OK Quantidade";
            fwrite($arquivo_log, ";Quantidade alterada");
        }
        
        //Atualização Preço
        $body = ["price" => round($preco,2)];
        $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
        if ($response['httpCode'] >= 300) {
            echo " - ERRO Preço";
            fwrite($arquivo_log, ";Preço não alterado");
        }
        else {
            echo " - OK Preço \nPreço: ".ArrayHelper::getValue($response, 'body.permalink')."\n";
            fwrite($arquivo_log, ";Preço alterado");
        }
    }
}
