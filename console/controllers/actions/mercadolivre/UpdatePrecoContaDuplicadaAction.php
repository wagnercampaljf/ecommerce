<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdatePrecoContaDuplicadaAction extends Action
{
    public function run()
    {
	//die;

        $nome_arquivo = "/var/tmp/log_update_preco_ml_conta_duplicada_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;produto_filial_conta_duplicada_id;status;produto_filial_conta_duplicada;preco;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial_duplicada       = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro             = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
	print_r($user_outro); //die;
        $response_outro         = ArrayHelper::getValue($user_outro, 'body');
        $meliAccessToken_outro  = $response_outro->access_token;
	echo "\n==> ".$meliAccessToken_outro;

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio Frete Produtos - Conta Antiga";
        
        $user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
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
                    //if(ArrayHelper::getValue($venda, 'id') != 'MLB1063732490'){continue;}
		    if(!isset($venda->shipping->shipping_mode)){continue;}
		    /*print_r($venda);

		    $shipping_id = ArrayHelper::getValue($venda, 'shipping.id');
		    $shipping = $meli->get('https://api.mercadolibre.com/shipments/'.$shipping_id.'?access_token='.$meliAccessToken);*/
		    //print_r($shipping);
		    //print_r($venda);

                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
		    //if(ArrayHelper::getValue($shipping, 'shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 

                        //foreach(ArrayHelper::getValue($shipping, 'shipping_items') as $itens){
			foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
			    /*if(ArrayHelper::getValue($itens, 'id') == 'MLB1094836758'){
                                print_r(ArrayHelper::getValue($itens, 'id'));
				print_r($itens);
				print_r($response_valor_dimensao);
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
        //die;
        //Código de criação da tabela de preços baseadas no ME - Novo
        echo "\nInicio Frete Produtos - Conta Nova";

	$filial_duplicada = Filial::find()->andWhere(['=', 'id', 98])->one();
        $user = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $y = 0;
            $data_atual = date('Y-m-d');
            
            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
	//print_r($response_order);

            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                //break;
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                    //if(ArrayHelper::getValue($venda, 'id') != 'MLB1063732490'){continue;}
		    //print_r($venda); continue;

		    if(!isset($venda->shipping->shipping_mode)){continue;}

                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));

                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            $response_valor_dimensao = $meli->get("/users/435343067/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                        }
                    }
                }
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        echo "\nFim Frete Produtos - Conta Nova";
        //Código de criação da tabela de preços baseadas no ME - Novo

        //die;
        
        echo "\n\nComeço da rotina de atualização dos produtos no ML";
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

	    $user_outro             = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
	    $response_outro         = ArrayHelper::getValue($user_outro, 'body');
            $meliAccessToken_outro  = $response_outro->access_token;
	    echo "\n==> ".$meliAccessToken_outro;//die;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->andWhere(['is not','meli_id',null])
					    //->andWhere(['produto_filial.id' => [341355,213647,212954,212766,342075,135099,189340,212508,212504,213405,213210,213641,213788,213645,213638,210614,210710,210712,327122,338864,329161,329703,329893,330298,330300,330459,330590,330634,189336,101771,212830,210718,211164,214054,213637,210716,210891,214318,210714,212501,212302,212769,211949,212765,213644,212768,214337,214340,211989,212876,210711,214373,210717,214420,322446,323016,324831,326838,329467,341095,189324,214344,210965,211950,212502,211940,214421,213154,211503,213643,211943,210713,211942,211952,210964,214339,214319,211951,211944,211946,214423,214425,214419,213769,214240,213646,213152,213947,211948,213153,212506,212507,213636,212447,212280,213639,213640,211975,214042,214043,211935,212503,213948,212770,214418,322449,323166,210615,210708,211162,211159,211161,211160,211163,211054,211442,211441,211505,211363,211504,211812,211974,212026,212006,211813,211973,211941,211976,212279,212301,212500,212875,213078,213077,212953,213084,213627,213628,214055,214045,213946,214338,214341,214320,214336,214335,214609,214371,214374,214536,329430,329483,329727,329766,329765,330266,214271,210715,213625,210709,211990,212767,210617,211938,211953,214281,214046,211945,214270,332642,333241,336404,337940,339820,320332,341153,342496,341093,341094,341091,189871,210616,212505,211939,214424,214422,213642,211934,211947,214044,212764,214041,214322,211689,212831,212763,213626,335029,320329,330274,320325,211937,211936,214321,212877,213949,340962,340969]])
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1235256674','MLB1094833277','MLB971490507','MLB864707646','MLB864697349','MLB1094830263','MLB1094833242','MLB864697419','MLB1103762075','MLB867579531','MLB1104343605','MLB864693090','MLB864694476','MLB864690511','MLB864697309','MLB883826748','MLB1094829699','MLB883825923','MLB883823037','MLB1094829717','MLB878431164','MLB883822465','MLB883829158','MLB883826744','MLB1094828475','MLB917987328','MLB883829604','MLB1235260391','MLB1235256992','MLB883828844','MLB883826561','MLB883822707','MLB883822365','MLB883829800','MLB1094832773','MLB1094829654','MLB1094832842','MLB878431546','MLB883826172','MLB1094829407','MLB883822732','MLB917990634','MLB883826977','MLB1094832637','MLB883822216','MLB1094829499','MLB1094829499','MLB1235257715','MLB883822930','MLB878424409','MLB883829748','MLB883825964','MLB878436644','MLB883826290','MLB1094820487','MLB878427963','MLB883826174','MLB878432690','MLB878428036','MLB975202781','MLB878431167','MLB878436667','MLB1235256833','MLB878424087','MLB939172432','MLB878427463','MLB878430536','MLB878437100','MLB878437100','MLB1235261501','MLB1235257735','MLB878436836','MLB883826029','MLB1094828229','MLB878419831','MLB1094829248','MLB878424394','MLB878427502','MLB878427502','MLB878420877','MLB878420877','MLB878427891','MLB878428332','MLB878427523','MLB878421423','MLB1235257815','MLB878416202','MLB878420868','MLB878421641','MLB1094829286','MLB901637062','MLB878416224','MLB1094829164','MLB878425275','MLB1234296833','MLB1234296833','MLB901631789','MLB878410343','MLB878413686','MLB1094820020','MLB1094828898','MLB878428350','MLB901631774','MLB878410346','MLB878421395','MLB878410348','MLB1235261573','MLB883823214','MLB903833668','MLB878408641','MLB901636885','MLB901634638','MLB878418798','MLB878420585','MLB901632049','MLB901634184','MLB878432955','MLB878415879','MLB1239695224','MLB901634192','MLB878415709','MLB878403575','MLB1094828995','MLB878419451','MLB878418213','MLB878412368','MLB878415469','MLB878419842','MLB878407119','MLB878414163','MLB901634196','MLB878414402','MLB878416803','MLB878411236','MLB878417345','MLB878420702','MLB878412720','MLB878413571','MLB878414243','MLB878410105','MLB878403970','MLB878412112','MLB878413439','MLB878413077','MLB878411450','MLB878409987','MLB878418323','MLB878418215','MLB878414854','MLB878409341','MLB1094828965','MLB878419415','MLB1094827891','MLB1235261368','MLB878409386','MLB878421863','MLB878413864','MLB1094819926','MLB878416906']])
                                            //->andWhere(['produto_filial.id' => [105007]])
					    //->andWhere(['produto_filial.produto_filial_origem_id' => [72337, 103390,  72336, 103179, 102936, 103436, 108229, 136720, 136616, 108232, 102916, 103319,  71426,  72334,  72366,  71427,  72333,  58040,  71428,  72694,  72338,  72365, 109536,  72188, 58041, 108283, 136706, 136677,  72153]])
                                            //->andWhere(['>=','produto_filial.id', 330140])
                                            //->joinWith('produto')
                                            //->andWhere(['like','produto.codigo_fabricante', 'CX.'])
                                            //->andWhere(['=','e_preco_alterado',true])
                                            //->andWhere(['produto_filial.id' => [109671]])
					    ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k." ==> ".$produtoFilial->id." - ".$produtoFilial->produto->codigo_fabricante;

		    //print_r($produto_frete);

                    fwrite($arquivo_log, "\n".$produtoFilial->id);

		    //if(($produtoFilial->id <= 134545 || $produtoFilial->id >= 342124) && $produtoFilial->filial_id == 72){
		    /*if ((strpos($produtoFilial->produto->codigo_fabricante,"CX.") === false)){
                         echo " - Pular";
                         continue;
                    }*/

	            //echo " - ".$produtoFilial->produto->nome;
		    //continue;

                    $produto_filial_outro = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                    ->andWhere(['=', 'filial_id', 98])
                    //->andWhere(['=','e_preco_alterado',true])
                    ->one();
                    
                    if($produto_filial_outro){
                        echo " - Destino: ".$produto_filial_outro->id." - ".$produto_filial_outro->meli_id;
              
			/*if (!ArrayHelper::keyExists($produto_filial_outro->meli_id, $produto_frete, false)){
				echo " - Sem Vendas";
				continue;
			}*/
          
                        if($produto_filial_outro->meli_id == "" || $produto_filial_outro->meli_id == null){
                            echo " - Produto duplicado ainda não criado";
                            fwrite($arquivo_log, ";".$produto_filial_outro->id.";Produto duplicado ainda não criado");
                            continue;
                        }
                        
                        $status_preco       = false;
                        $status_quantidade  = false;
                        
                        if(!$produtoFilial->atualizar_preco_mercado_livre){
                            echo " - Não Atualiza ML";
                            continue;
                        }
                        
                        if (is_null($produtoFilial->valorMaisRecente)) {
                            continue;
                        }
                        
                        $preco = round($produtoFilial->getValorMercadoLivre(), 2);
                        
                        if (ArrayHelper::keyExists($produtoFilial->meli_id, $produto_frete, false)){
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 120 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produtoFilial->meli_id)/2), 2);
                                $preco =  $preco-16;
                            }
                        }

			if (ArrayHelper::keyExists($produto_filial_outro->meli_id, $produto_frete, false)){
                            if($preco>=510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_outro->meli_id)/2), 2);
                                $preco = $preco-10;
                            }
                            elseif($preco > 120 && $preco < 510){
                                $preco += round((ArrayHelper::getValue($produto_frete, $produto_filial_outro->meli_id)/2), 2);
                                $preco =  $preco-16;
                            }
                        }

			/*if($preco >= 120){
				echo " - Preco maior que 120";
				continue;
			}*/

                        $status_quantidade  = false;
                        $status_preco       = false;
                        
                        //Aqui começa o código
                        //Atualização Quantidade
                        $body = ["available_quantity" => $produtoFilial->quantidade,];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            $status_quantidade  = true;
                            echo " - ERRO Quantidade";
                            fwrite($arquivo_log, ";Quantidade não alterada");
                        }
                        else {
                            echo " - OK Quantidade";
                            fwrite($arquivo_log, ";Quantidade alterada");
                        }
                        
                        //Atualização Preços
                        $body = ["price" => $preco,];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            $status_preco   = false;
                            echo " - ERRO Preço";
                            fwrite($arquivo_log, ";Preço não alterado");
                        }
                        else {
                            echo " - OK Preço";
                            fwrite($arquivo_log, ";Preço alterado");
                        }

			//Atualização Modo de Envio
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

			$condicao = "new";
                	if($produtoFilial->produto->e_usado){
                	        $condicao = "used";
                	}

                        $body = [
                            "shipping" => [
                                "mode" => $modo,
                                "local_pick_up" => true,
                                "free_shipping" => false,
                                "free_methods" => [],
                            ],
			    "condition" => $condicao,
                        ];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERRO Modo";
                            fwrite($arquivo_log, ";".$produto_filial_outro->id.";Modo não alterado");
                        }
                        else {
                            echo " - OK Modo";
                            fwrite($arquivo_log, ";".$produto_filial_outro->id.";Modo alterado");
                        }

                        //Aqui termina o código
                        
                        //Alteração SEM JUROS
                        /*if($produto_filial_outro->meli_id_sem_juros != null && $produto_filial_outro->meli_id_sem_juros != ""){
                            $body = [
                                "shipping" => [
                                    "mode" => $modo,
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                            ];
                            $response = $meli->put("items/{$produto_filial_outro->meli_id_sem_juros}?access_token=" . $meliAccessToken_outro, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - ERRO Modo SEM JUROS";
                                fwrite($arquivo_log, ";".$produto_filial_outro->id.";Modo não alterado SEM JUROS");
                            }
                            else {
                                echo " - OK Modo  SEM JUROS";
                                fwrite($arquivo_log, ";".$produto_filial_outro->id.";Modo alterado SEM JUROS");
                            }
                            
                            //Atualização Quantidade
                            $body = ["available_quantity" => $produtoFilial->quantidade,];
                            $response = $meli->put("items/{$produto_filial_outro->meli_id_sem_juros}?access_token=" . $meliAccessToken_outro, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                $status_quantidade  = true;
                                echo " - ERRO Quantidade SEM JUROS";
                                fwrite($arquivo_log, ";Quantidade não alterada SEM JUROS");
                            }
                            else {
                                echo " - OK Quantidade SEM JUROS";
                                fwrite($arquivo_log, ";Quantidade alterada SEM JUROS");
                            }
                            
                            //Atualização Preço
                            $body = ["price" => $preco,];
                            $response = $meli->put("items/{$produto_filial_outro->meli_id_sem_juros}?access_token=" . $meliAccessToken_outro, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                $status_preco   = false;
                                echo " - ERRO Preço SEM JUROS";
                                fwrite($arquivo_log, ";Preço não alterado SEM JUROS");
                            }
                            else {
                                echo " - OK Preço SEM JUROS";
                                fwrite($arquivo_log, ";Preço alterado SEM JUROS");
                            }
                        }*/
                        //Alteração SEM JUROS
                        
                        /*if($status_quantidade && $status_preco){
                            $produto_filial_outro->e_preco_alterado = false;
                            if($produto_filial_outro->save()){
                                echo " - OK Status";
                                fwrite($arquivo_log, ";Status alterado");
                            }
                            else{
                                echo " - ERROR Status";
                                fwrite($arquivo_log, ";Status não alterado");
                            }
                        }*/
                    }
                    else{
                        echo " - Produto não encontrado";
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


