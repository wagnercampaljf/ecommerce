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

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

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
                                            ->andWhere(['produto_filial.produto_id' => [102067]])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n ==> ".$produtoFilial->id;
        		    if ($produtoFilial->produto->fabricante_id != null) {

			    /////////////////////////////////////////////
			    //Aqui começa o código
			    /////////////////////////////////////////////

			    //Atualização Imagens
                            $body = [
					"pictures" => $produtoFilial->produto->getUrlImagesML(),
                                    ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
			            echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR";
				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Imagem não alterado");
                            }
                            else {
                                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ok";
    				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Imagem alterado");
			    }
                        //Aqui termina o código
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
