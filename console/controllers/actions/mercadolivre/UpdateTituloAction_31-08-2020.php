<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateTituloAction extends Action
{
    public function run()//$filial_id)
    {

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
            //->andWhere(['id' => [96]])
            ->andWhere(['<>','id',98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            
            $nome_arquivo = "/var/tmp/log_update_preco_ml_".str_replace(" ", "", $filial->nome)."_".date("Y-m-d_H-i-s").".csv";
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
                                            ->where(" meli_id is not null and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1528400982']])
                                            //->andWhere(['produto_filial.produto_id' => [4]])
                                            //->andWhere(['produto_filial.id' => [315656]])
                                            //->andWhere(['produto_filial.id' => []])
                                            //->joinWith('produto')
                                            //->andWhere(['like','produto.nome', 'CAPA PORCA'])
                                            ->andWhere(['=','e_nome_alterado',true])
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
                    
                    echo "\n ==> ".$k." - ".$produto_filial->id;

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
                    if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, "CONTA PRINCIPAL - SEM JUROS", $page, $titulo_novo, $produto_filial);
                    }
                    
                    //Atualizar produto FULL, conta principal
                    if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, "CONTA PRINCIPAL - FULL", $page, $titulo_novo, $produto_filial);
                    }
                    
                    if($produto_filial_conta_duplicada){

            			//DESATIVACAO DA ATUALIZACAO DA CONTA DUPLICADA 06-08-2020
            			continue;

            			$this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, "CONTA DUPLICADA - PRINCIPAL", $page, $titulo_novo, $produto_filial_conta_duplicada);
                        
                        //Atualizar produto SEM JUROS, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, "CONTA DUPLICADA - SEM JUROS", $titulo_novo, $title, $produto_filial_conta_duplicada);
                        }
                        
                        //Atualizar produto FULL, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_full, "CONTA DUPLICADA - FULL", $page, $titulo_novo, $produto_filial_conta_duplicada);
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
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id,$meli_origem, $page, $title, $produto_filial_e_nome_alterado){
        
    	echo "\nORIGEM: ".$meli_origem;
    	
    	$status_nome       = false;
    	$status_descricao  = true;

    	//Atualização Descrição
    	$body = ["plain_text" => $page];
    	$response = $meli->put("items/{$meli_id}/description?access_token=" . $token, $body, [] );
    	if ($response['httpCode'] >= 300) {
    	    echo " - ERROR Descricao";
    	    fwrite($arquivo_log, ";Descricao nao alterada");
    	}
    	else{
    	    echo " - OK Descrição";
    	    $status_descricao = true;
    	    fwrite($arquivo_log, ";Descricao alterada");
    	}
    	
    	$quantidade_caracteres = 60;
    	
    	$response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$meli_id.'?access_token='.$token);
   	if ($response_tipo_anuncio['httpCode'] < 300) {
    	    $response_categoria = $meli->get('https://api.mercadolibre.com/categories/'.ArrayHelper::getValue($response_tipo_anuncio, 'body.category_id'));
    	    if ($response_categoria['httpCode'] < 300) {
		//echo "\n\n\n quantidade: "; print_r(ArrayHelper::getValue($response_categoria, 'body.settings.max_title_length')); echo "\n\n\\n";
    	        $quantidade_caracteres = ArrayHelper::getValue($response_categoria, 'body.settings.max_title_length');
    	    }
    	}

	echo " - Quantidade Caracteres: ".$quantidade_caracteres." ";
    	
    	//Atualização Título
	$titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');

    	$body = [
		//"title" => ((strlen($title) <= $quantidade_caracteres) ? $title : substr($title, 0, $quantidade_caracteres))
		"title" => mb_substr($titulo_novo,0,60),
	];
    	$response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
    	if ($response['httpCode'] >= 300) {
    	    echo " - ERROR Título";
    	    fwrite($arquivo_log, ";Titulo não alterado");
    	}
    	else {
    	    echo " - OK Título";
    	    echo " - Link: ".ArrayHelper::getValue($response, 'body.permalink');
    	    $status_nome = true;
    	    fwrite($arquivo_log, ";Titulo alterado");
    	}
    	
    	if($status_descricao && $status_nome){
    	    $produto_filial_e_nome_alterado->e_nome_alterado = false;
    	    if($produto_filial_e_nome_alterado->save()){
    	        echo " - STATUS ALTERADO";
    	    }
    	    else{
    	        echo " - STATUS NÃO ALTERADO";
    	    }
    	}
    }
}
