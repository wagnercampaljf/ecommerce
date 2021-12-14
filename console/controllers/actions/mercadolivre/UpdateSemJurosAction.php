<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateSemJurosAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_sem_juros_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;meli_id, meli_id_sem_juros;Tipo;Tipo SEM JUROS;status;status_sem_juros");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [84]])
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
                                            ->andWhere(['is not','meli_id_sem_juros',null])
                                            ->andWhere(['<>','meli_id_sem_juros',''])
                                            ->andWhere(['=','meli_id','MLB864690170'])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id;
                    fwrite($arquivo_log,"\n".$produtoFilial->id.";".$produtoFilial->meli_id.";".$produtoFilial->meli_id_sem_juros);
                    
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produtoFilial->meli_id.'?access_token='.$meliAccessToken);
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio";
                        fwrite($arquivo_log,";Produto não encontrado");
                    }
                    else {
                        echo " - ".ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id');
                        fwrite($arquivo_log,";".ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id'));
                    }
                    
       		        $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produtoFilial->meli_id_sem_juros.'?access_token='.$meliAccessToken);
       		        if ($response_tipo_anuncio['httpCode'] >= 300) {
       		            echo " - produto nao encontrado no ML, tipo de anuncio";
       		            fwrite($arquivo_log,";Produto não encontrado");
       		        }
       		        else {
       		            echo " - ".ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id');
       		            fwrite($arquivo_log,";".ArrayHelper::getValue($response_tipo_anuncio, 'body.listing_type_id'));
       		        }
       		        
       		        //Atualização
       		        $body = ["listing_type_id" => "gold_special"];
       		        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
       		        //print_r($response);
       		        if ($response['httpCode'] >= 300) {
       		            echo " - ERROR Tipo";
       		            fwrite($arquivo_log, ";".$produtoFilial->id.";Tipo não alterado");
       		        }
       		        else{
       		            echo " - OK Tipo";
       		            fwrite($arquivo_log, ";".$produtoFilial->id.";Tipo alterada");
       		        }
       		        
                    //Atualização SEM JUROS
                    $body = ["listing_type_id" => "gold_pro"];
                    $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                    //print_r($response);
                    if ($response['httpCode'] >= 300) {
                        echo " - ERROR Tipo SEM JUROS";
                        fwrite($arquivo_log, ";".$produtoFilial->id.";Tipo SEM JUROS não alterado");
                    }
                    else{
                        echo " - OK TIPO SEM JUROS";
                        fwrite($arquivo_log, ";".$produtoFilial->id.";Tipo SEM JUROS alterada");
                    }
                    die;
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
