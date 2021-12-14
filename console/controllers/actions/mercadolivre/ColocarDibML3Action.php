<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class ColocarDibML3Action extends Action
{
    public function run($cliente = 1){

        echo "INÍCIO\n\n";

        //LOG da Vericação
        if (file_exists("/var/tmp/log_colocar_dib_ML_3.csv")){
            unlink("/var/tmp/log_colocar_dib_ML_3.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_colocar_dib_ML_3.csv", "a");
        fwrite($arquivo_log, "MELI_ID;STATUS");
        //LOG da Vericação

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //$user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $user = $meli->refreshAccessToken('TG-5cb495b0eefc400006279a24-390464083');
        $response = ArrayHelper::getValue($user, 'body');

        $filial_ml3 = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro     = $meli->refreshAccessToken($filial_ml3->refresh_token_meli);
        $response_outro = ArrayHelper::getValue($user_outro, 'body');

            if ((is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) && (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400)){
            $meliAccessToken = $response->access_token;
            $meliAccessToken_outro = $response_outro->access_token;

            $produtos_filial_dib = ProdutoFilial::find()->andWhere(['=','filial_id',97])->addOrderBy('id')->all();

            foreach($produtos_filial_dib as $k => $produto_filial_dib){
                echo "\n".$k." - ".$produto_filial_dib->id;

		if($produto_filial_dib->quantidade <= 0){
                	continue;
            	}

		if($k <= 19887){//4447){
                        continue;
                }

                $produtos_filial_dib_duplicada = ProdutoFilial::find()  ->andWhere(['=','produto_filial_origem_id',$produto_filial_dib->id])
                                                                        ->andWhere(['=','filial_id',43])
                                                                        ->one();
                if($produtos_filial_dib_duplicada){
                    echo " - Produto encontrado - ".$produtos_filial_dib_duplicada->id." - ".$produtos_filial_dib_duplicada->meli_id;

                    if($produtos_filial_dib_duplicada->meli_id != "" and $produtos_filial_dib_duplicada->meli_id != null){
                        $response_item = $meli->get("/items/".$produtos_filial_dib_duplicada->meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item); die;
                        echo " - Produto no ML - Categoria: ".ArrayHelper::getValue($response_item, 'body.category_id');

                        $produto_filial_dib_ml3_duplicada = ProdutoFilial::find()  ->andWhere(['=','produto_filial_origem_id',$produto_filial_dib->id])
                                                                                    ->andWhere(['=','filial_id',98])
										    ->andWhere(['is','meli_id',null])
                                                                                    ->one();
                        if($produto_filial_dib_ml3_duplicada ){
                            echo " - Produto da conta nova ML 3 - ".$produto_filial_dib_ml3_duplicada->id;
                            $body = [
                                "title" => ArrayHelper::getValue($response_item, 'body.title'),
                                "category_id" => ArrayHelper::getValue($response_item, 'body.category_id'),
                                "listing_type_id" => ArrayHelper::getValue($response_item, 'body.listing_type_id'),
                                "currency_id" => ArrayHelper::getValue($response_item, 'body.currency_id'),
                                "price" => ArrayHelper::getValue($response_item, 'body.price'),
                                "available_quantity" => utf8_encode($produto_filial_dib->quantidade),
                                "condition" => "new",
                                "pictures" => $produto_filial_dib->produto->getUrlImagesML(),
                                "shipping" => [
                                    "mode" => "me2",
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                                "sale_terms" => [
                                    [       "id" => "WARRANTY_TYPE",
                                        "value_id" => "2230280"
                                    ],
                                    [       "id" => "WARRANTY_TIME",
                                        "value_name" => "3 meses"
                                    ]
                                ]
                            ];
                            //print_r($body);
                            $response_copia = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
			    //print_r($response_copia);
                            if ($response_copia['httpCode'] < 300) {
                                echo " - Produto Criado no ML3";
				$produto_filial_dib_ml3_duplicada->meli_id = $response_copia['body']->id;
                                if($produto_filial_dib_ml3_duplicada->save()){
					echo " - Meli_id salvo";
				}
				else{
					echo " - Meli_id Não salvo";
				}
				fwrite($arquivo_log, $produto_filial_dib_ml3_duplicada->id.";Produto duplicado criado");
                            }
                            else {
				echo " - Produto Não Criado no ML3";
                                fwrite($arquivo_log, $produto_filial_dib_ml3_duplicada->id.";Produto duplicado não criado");
                            }
                            //die;
                        }
                        else{
                            echo " - Produto AUSENTE na conta nova ML 3";
                        }
                    }
                    else{
                        echo " - Produto FORA ML";
                    }
                }
                else{
                    echo " - Produto Não encontrado";
                }
            }
        }

        //LOG Fecha o arquivo
        fclose($arquivo_log); 

        echo "\n\nFIM!\n\n";
    }
}
