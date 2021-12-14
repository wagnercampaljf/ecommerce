<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateImagemAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_imagem_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;preco;status;produto_filial_conta_duplicada;preco;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização de imagens no ML";
        // $date = date('Y-m-d H:i');
        // echo $date;

        $filials = Filial::find()
                                    ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                  //->andWhere(['id' => [84]])
    	                          //->andWhere(['<>','id', 43])
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
					    //->andWhere(['produto_filial.meli_id' => ["MLB878419550","MLB917990797","MLB883826762","MLB878433264","MLB864684145","MLB1014619236"]])
                                            //->andWhere(['produto_filial.produto_id' => [8720,8705,38154,40401,56053,6705,222350]])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {

                    if($k%5000==0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');

                        $meliAccessToken = $response->access_token;                        
                       
                        //echo "\nTOKEN :" .  $meliAccessToken;
                    }


                    if($k < 33665){continue;}

                    echo "\n ==> ".$produtoFilial->id;

        		    if ($produtoFilial->produto->fabricante_id != null) {

                        /////////////////////////////////////////////
                        //Aqui começa o código
                        /////////////////////////////////////////////

			    //Atualização Imagens
                //  $body = [
				// 	        "pictures" => $produtoFilial->produto->atualizarMLVideo()

                //         ];

                 $body =  $produtoFilial->produto->atualizarMLVideo();     

                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {

			                   echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR ";
				               fwrite($arquivo_log, "\n".$produtoFilial->id.";Imagem não alterada");

                            }
                            else {

                                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - OK";
    				                fwrite($arquivo_log, "\n".$produtoFilial->id.";Imagem alterada");
			    }
                        //Aqui termina o código
                    }
                }
            }
          echo "Fim da filial: " . $filial->nome . "\n";
        }

    	// fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	// fwrite($arquivo_log, "\nFim da rotina de atualização de imagens no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização de imagens no ML";
    }
}
