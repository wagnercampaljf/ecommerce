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
        
        echo "\n\nComeço da rotina de atualização do título dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [$filial_id]])
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
                echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;

                $produto_filiais = $filial  ->getProdutoFilials()
                                            //->andWhere(['is not','meli_id',null])
                                            ->where(" meli_id is not null and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                    			            //->andWhere(['produto_filial.meli_id' => ['MLB864681632']])
                                            ->andWhere(['produto_filial.produto_id' => [4]])
                    					    //->andWhere(['produto_filial.id' => [315656]])
                    					    //->andWhere(['produto_filial.id' => []])
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
        		    
                    //Atualizar produto principal, conta principal
                    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, "CONTA PRINCIPAL - PRINCIPAL", $page, $title);

                    //Atualizar produto SEM JUROS, conta principal
                    if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, "CONTA PRINCIPAL - SEM JUROS", $page, $title);
                    }
                    
                    //Atualizar produto FULL, conta principal
                    if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, "CONTA PRINCIPAL - FULL", $page, $title);
                    }
                    
                    if($produto_filial_conta_duplicada){
                        
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, "CONTA DUPLICADA - PRINCIPAL", $page, $title);
                        
                        //Atualizar produto SEM JUROS, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, "CONTA DUPLICADA - SEM JUROS", $page, $title);
                        }
                        
                        //Atualizar produto FULL, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_full, "CONTA DUPLICADA - FULL", $page, $title);
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
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id,$meli_origem, $page, $title){
        
    	echo "\nORIGEM: ".$meli_origem;

    	//Atualização Descrição
    	$body = ["plain_text" => $page];
    	$response = $meli->put("items/{$meli_id}/description?access_token=" . $token, $body, [] );
    	if ($response['httpCode'] >= 300) {
    	    echo " - ERROR Descricao";
    	    fwrite($arquivo_log, ";Descricao nao alterada");
    	}
    	else{
    	    echo " - OK Descrição";
    	    fwrite($arquivo_log, ";Descricao alterada");
    	}
    	
    	//Atualização Título
    	$body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
    	$response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, [] );
    	if ($response['httpCode'] >= 300) {
    	    echo " - ERROR Título";
    	    fwrite($arquivo_log, ";Titulo não alterado");
    	}
    	else {
    	    echo " - OK Título";
    	    echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
    	    fwrite($arquivo_log, ";Titulo alterado");
    	}
    }
}
