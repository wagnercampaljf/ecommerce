<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateTituloAction extends Action
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
					    //->andWhere(['produto_filial.meli_id' => ['MLB1336188418']])
                                            //->andWhere(['produto_filial.id' => [317345]])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n ==> ".$produtoFilial->id;
        		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Aqui começa o código
                            if (is_null($produtoFilial->valorMaisRecente)) {
                                continue;
                            }

                            $title = Yii::t('app', '{nome} (cod. {cod})', [
        	                        'cod' => $produtoFilial->produto->codigo_global,
        	                        'nome' => $produtoFilial->produto->nome
        	                   ]);

			    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);

			    //Atualização Descrição
                            $body = [
                                        "plain_text" => $page,
                                    ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
			    if ($response['httpCode'] >= 300) {
                                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR";
                                    fwrite($arquivo_log, "\n".$produtoFilial->id.";Descrição não alterado");
                            }

                            //Atualização Título
                            $body = [
					"title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                    ];
                            $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
			            echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ERROR";
				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Titulo não alterado");
				    print_r($response);
                            }
                            else {
                                    echo "\n".$k ." - ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ok";
    				    fwrite($arquivo_log, "\n".$produtoFilial->id.";Titulo alterado");

				    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();

                                    foreach ($produtos_filiais_outros as $produto_filial_outro){
                                            $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                                            $response_outro = ArrayHelper::getValue($user_outro, 'body');
                                            if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                                                $meliAccessToken_outro = $response_outro->access_token;
                                                if($produto_filial_outro->meli_id != null){
                                                    $body = [
							"title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                                    ];
                                                    $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                                                    if ($response['httpCode'] >= 300) {
                                                        fwrite($arquivo_log, $produto_filial_outro->id.";Produto duplicado, titulo não alterado");
                                                    }else {
                                                        fwrite($arquivo_log, $produto_filial_outro->id.";Produto duplicado, titulo alterado");
                                                    }

						    $body = [
							"plain_text" => $page,
                                                    ];
                                                    $response = $meli->put("items/{$produto_filial_outro->meli_id}/description?access_token=" . $meliAccessToken_outro, $body, []);
                                                    if ($response['httpCode'] >= 300) {
                                                        fwrite($arquivo_log, $produto_filial_outro->id.";Produto duplicado, descricao não alterado");
                                                    }else {
                                                        fwrite($arquivo_log, $produto_filial_outro->id.";Produto duplicado, descricao alterado");
                                                    }
                                                }
				    	    }
				    }
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
