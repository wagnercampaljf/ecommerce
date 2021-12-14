<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateEAtivoAction extends Action
{
    public function run()
    {

        $nome_arquivo = "/var/tmp/log_update_numero_peca_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;meli_id;status;meli_id_sem_juros;status_juros");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [84]])
    	    ->andWhere(['<>','id', 43])
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
                                            ->andWhere(['produto_filial.meli_id_sem_juros' => ['MLB1451872859']])
                                            //->andWhere(['produto_filial.id' => []])
                    			    //->andWhere(['produto_filial.produto_id' => [236162]])
                    			    //->andWhere(['=','e_nome_alterado',true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id;
		    fwrite($arquivo_log, "\n".$produtoFilial->id.";".$produtoFilial->meli_id);

		    //if($produtoFilial->meli_id != 'MLB979128351'){die;}

       		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Atualiza EAtivo
			$e_ativo = 'paused';
			if($produtoFilial->produto->e_ativo){
				$e_ativo = 'active';
			}
			echo " - e_ativo:".$e_ativo;
			$body = ["status" => $e_ativo,];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR e_ativo";
                            fwrite($arquivo_log, ";Numero Peca nao alterado");
                        }
                        else {
                            echo " - OK e_ativo";
                            echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Numero Peca alterado");
                        }
                        //Aqui termina o código
                        
                        //Altera Numero Peca SEM JUROS
                        if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
			    fwrite($arquivo_log, ";".$produtoFilial->meli_id_sem_juros);
			    $e_ativo = 'paused';
                            if($produtoFilial->produto->e_ativo){
                                $e_ativo = 'active';
                            }
                            echo " - e_ativo:".$e_ativo;
                            $body = ["status" => $e_ativo,];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                            if ($response['httpCode'] >= 300) {
                                echo " - ERROR e_ativo SEM JUROS";
                                fwrite($arquivo_log, ";EAtivo nao alterado SEM JUROS");
                            }
                            else {
                                echo " - OK e_ativo SEM JUROS";
                                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";EAtivo alterado SEM JUROS");
                            }
			}
                        //Alteração SEM JUROS
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

