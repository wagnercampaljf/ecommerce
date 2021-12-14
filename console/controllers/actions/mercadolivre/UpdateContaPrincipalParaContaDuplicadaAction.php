<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateContaPrincipalParaContaDuplicadaAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";
        
        $nome_arquivo = "/var/tmp/log_update_conta_principal_para_conta_duplicada_ml_".date("Y-m-d_H-i-s").".csv";
        
        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial_duplicada   = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro         = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
        $response_outro     = ArrayHelper::getValue($user_outro, 'body');
        
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
				                    //->andWhere(['=', 'id', 97])
                                    ->andWhere(['<>','id',98])
                            	    ->orderBy('id')
                                    ->all();
        
        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
            $meliAccessToken_outro = $response_outro->access_token;

            foreach ($filials as $filial) {
                $user               = $meli->refreshAccessToken($filial->refresh_token_meli);
                $response           = ArrayHelper::getValue($user, 'body');
                $meliAccessToken    = $response->access_token;
                
                echo "\n\nFilial: ".$filial->id." - ".$filial->nome."\n\n";

                $produtoFilials = $filial->getProdutoFilials()  ->andWhere(['IS NOT', 'meli_id', NULL])
                                                                //->andWhere(['id' => [139637]])
                                                        	->orderBy('id')
                                                                ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {

                    if($k%5000==0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                        
                        $user_conta_duplicada = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
                        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
                        $meliAccessToken_outro = $response_conta_duplicada->access_token;
                        
                        echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                        echo "\nTOKEN DUPLICADA:" . $meliAccessToken_outro;
                    }
                    
        		    echo "\n".$k." - Origem: ".$produtoFilial->id; //continue;
        		    
        		    fwrite($arquivo_log, "\n".$produtoFilial->meli_id);
        
        		    /*if($k <= 18121 && $produtoFilial->filial_id == 97){
            			echo " - pulou";
            			continue;
        		    }*/

                    $produto_filial_outro = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                    ->andWhere(['=', 'filial_id', 98])
                                                                    ->one();

        		    if($produto_filial_outro){
                        echo " - Destino: ".$produto_filial_outro->id." - ".$produto_filial_outro->meli_id;
                        
                        if($produto_filial_outro->meli_id == "" || $produto_filial_outro->meli_id == null){
                            echo " - Produto não criado na conta duplicada";
                            continue;
                        }
                        
                        $response_item = $meli->get("/items/".$produtoFilial->meli_id."?access_token=" . $meliAccessToken);
                        
                        if ($response_item['httpCode'] >= 300) {
                            echo " - Produto não encontrado na conta principal";
                            continue;
                        }
                        
                        $body = [
                            /*"title"                 => utf8_encode(ArrayHelper::getValue($response_item, 'body.title')),
                            "listing_type_id"       => ArrayHelper::getValue($response_item, 'body.listing_type_id'),
                            "currency_id"           => "BRL",
                            "price"                 => utf8_encode(ArrayHelper::getValue($response_item, 'body.price')),
                            "available_quantity"    => utf8_encode(ArrayHelper::getValue($response_item, 'body.available_quantity')),
                            "seller_custom_field"   => utf8_encode("SJ.".$produto_filial->id),
                            "condition"             => utf8_encode(ArrayHelper::getValue($response_item, 'body.condition')),
                            "description"           => ["plain_text" => $page],
                            "pictures"              => ArrayHelper::getValue($response_item, 'body.pictures'),
                            "shipping"              => ArrayHelper::getValue($response_item, 'body.shipping'),
                            "sale_terms"            => ArrayHelper::getValue($response_item, 'body.sale_terms'),*/
                            "category_id"           => utf8_encode(ArrayHelper::getValue($response_item, 'body.category_id')),
                            //"attributes"            => ArrayHelper::getValue($response_item, 'body.attributes'),
                        ];
                        
                        $response_outro = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response_outro['httpCode'] >= 300) {
                            echo " - ERROR - Não Publicado \n";
                            fwrite($arquivo_log, ";".$produto_filial_outro->id.";;".$produto_filial_outro->produto->nome.";Produto não alterado no ML");
                        }
                        else {
                            echo " - OK - ";print_r(ArrayHelper::getValue($response_outro, 'body.permalink'));
                            fwrite($arquivo_log, ";".$produto_filial_outro->id.";".ArrayHelper::getValue($response_outro, 'body.id').";".$produto_filial_outro->produto->nome.";Produto alterado no ML");
                        }
                        
                        $body = [
                            "attributes"            => ArrayHelper::getValue($response_item, 'body.attributes'),
                        ];
                        
                        $response_outro = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response_outro['httpCode'] >= 300) {
                            echo " - ERROR - Não Publicado \n";
                            fwrite($arquivo_log, ";Atributos não alterado no ML;");
                        }
                        else {
                            echo " - OK - ";print_r(ArrayHelper::getValue($response_outro, 'body.permalink'));
                            fwrite($arquivo_log, ";Atributos alterado no ML;".ArrayHelper::getValue($response_outro, 'body.permalink'));
                        }
        		    }
        		    else{
        		        echo " - Produto não encontrado";
        		    }
	            }
            }
        }
        
        fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
        fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
        fclose($arquivo_log);
    }
}


