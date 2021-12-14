<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateCategoriaAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_titulo_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [97]])
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
					    ->joinWith('produto')
					    ->andWhere(['=', 'subcategoria_id',216])
			                    //->andWhere(['like','upper(produto.nome)','MANGUEIRA'])
                                            //->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1442476414']])
                                            //->andWhere(['produto_filial.id' => [34637]])
					    //->andWhere(['produto_filial.produto_id' => [236162]])
					    //->andWhere(['=','e_nome_alterado',true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ".$produtoFilial->produto->nome;
		    //continue;

       		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Aqui começa o código

			//Atualizar Categoria
                        $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                        //if (!isset($subcategoriaMeli)) {
                        //        continue;
                        //} else {
                        //        if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
                        //               $subcategoriaMeli = "MLB191833";
                        //        }
                        //}
                        $body = ["category_id" => utf8_encode($subcategoriaMeli),];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                        if ($response['httpCode'] >= 300) {
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }
                        else{
                                echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');
                        }
                        //Aqui termina o código
                        
                        //Alteração SEM JUROS
                        if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
			    //Atualizar Categoria
                            $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                            //if (!isset($subcategoriaMeli)) {
                            //    continue;
                            //} else {
                            //    if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
                            //            $subcategoriaMeli = "MLB191833";
                            //    }
                            //}
                            $body = ["category_id" => utf8_encode($subcategoriaMeli),];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                            if ($response['httpCode'] >= 300) {
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }
                            else{
                                echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');
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
