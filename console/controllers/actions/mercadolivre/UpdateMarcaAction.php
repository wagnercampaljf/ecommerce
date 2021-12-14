<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\MarcaProduto;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateMarcaAction extends Action
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
            
            $nome_arquivo = "/var/tmp/log_update_marca_ml_".str_replace(" ", "", $filial->nome)."_".date("Y-m-d_H-i-s").".csv";
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
                    			            //->andWhere(['produto_filial.meli_id' => ['MLB864681632']])
                                            //->andWhere(['produto_filial.produto_id' => [4]])
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

                    //Atualizar produto principal, conta principal
                    $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id, "CONTA PRINCIPAL - PRINCIPAL", $produto_filial->produto->marca_produto_id);

                    //Atualizar produto SEM JUROS, conta principal
                    if(!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_sem_juros, "CONTA PRINCIPAL - SEM JUROS", $produto_filial->produto->marca_produto_id);
                    }
                    
                    //Atualizar produto FULL, conta principal
                    if(!is_null($produto_filial->meli_id_full)  && $produto_filial->meli_id_full <> ""){
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken, $produto_filial->meli_id_full, "CONTA PRINCIPAL - FULL", $produto_filial->produto->marca_produto_id);
                    }
                    
                    if($produto_filial_conta_duplicada){
                        
                        $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id, "CONTA DUPLICADA - PRINCIPAL", $produto_filial_conta_duplicada->produto->marca_produto_id);
                        
                        //Atualizar produto SEM JUROS, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_sem_juros) && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_sem_juros, "CONTA DUPLICADA - SEM JUROS",$produto_filial_conta_duplicada->produto->marca_produto_id);
                        }
                        
                        //Atualizar produto FULL, conta duṕlicada
                        if(!is_null($produto_filial_conta_duplicada->meli_id_full)  && $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $this->atualizarProdutoML($arquivo_log, $meli, $meliAccessToken_conta_duplicada, $produto_filial_conta_duplicada->meli_id_full, "CONTA DUPLICADA - FULL", $produto_filial_conta_duplicada->produto->marca_produto_id);
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
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id,$meli_origem, $marca_produto_id){
        
    	echo "\nORIGEM: ".$meli_origem;

    	//Atualização da Marca
    	$marca_produto = MarcaProduto::find()->andWhere(['=','id',$marca_produto_id])->one();
    	if($marca_produto){
    	    $body = [
    	        'attributes' =>[
    	            [
    	                "id"=> "BRAND",
    	                "name"=> "Marca",
    	                "value_id"=> null,
    	                "value_name"=> $marca_produto->nome."/CONSULTAR",
    	                "value_struct"=> null,
    	                "attribute_group_id"=> "OTHERS",
    	                "attribute_group_name"=> "Outros"
    	            ],
    	        ]
    	    ];
    	    $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
    	    if ($response['httpCode'] >= 300) {
    	        echo " - Marca não alterada";
    	        fwrite($arquivo_log, ";Marca não alterada");
    	    }
    	    else {
    	        echo " - Marca alterada";
    	        echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
    	        fwrite($arquivo_log, ";Marca alterada");
    	    }
    	}
    	else{
    	    $body = [
    	        'attributes' =>[
    	            [
    	                "id"=> "BRAND",
    	                "name"=> "Marca",
    	                "value_id"=> null,
    	                "value_name"=> "OPT",
    	                "value_struct"=> null,
    	                "attribute_group_id"=> "OTHERS",
    	                "attribute_group_name"=> "Outros"
    	            ],
    	        ]
    	    ];
    	    $response = $meli->put("items/{$meli_id}?access_token=" . $token, $body, []);
    	    if ($response['httpCode'] >= 300) {
    	        echo " - Marca não alterada OPT";
    	        fwrite($arquivo_log, ";Marca não alterada OPT");
    	    }
    	    else {
    	        echo " - Marca alterada OPT";
    	        echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
    	        fwrite($arquivo_log, ";Marca alterada OPT");
    	    }
    	}
    	
    }
}
