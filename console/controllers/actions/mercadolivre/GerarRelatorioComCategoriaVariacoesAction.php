<?php

namespace console\controllers\actions\mercadolivre;

use common\models\ProdutoFilialVariacao;
use common\models\Subcategoria;
use common\models\Variacao;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ValorProdutoFilial;
use common\models\ProdutoFilial;



class GerarRelatorioComCategoriaVariacoesAction extends Action
{
    public function run()
    {

        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', 513356])->one();


        /*
        echo "INÍCIO\n\n";

        // Escreve no log
        $arquivo_log = fopen("/var/tmp/subcategoria_variations.csv", "a");
        fwrite($arquivo_log, "id;nome;meli_cat_nome;meli_id;modo_envio\n");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $subcategorias = Subcategoria::find()->all();

        foreach ($subcategorias as $k => $subcategoria){
            if ($k <= 0){


                continue;

            }
            echo "\n".$k." - ".$subcategoria->meli_id;
            $response_categoria = $meli->get("/categories/".$subcategoria->meli_id);

            $variacao = "variations";

            if (ArrayHelper::getValue($response_categoria, 'httpCode') < 400) {
               if(ArrayHelper::getValue($response_categoria, 'body.attribute_types') == $variacao){
                   echo ' - variacao';
               }
            }else{
                echo 'erro categoria';
                continue;
            }

            fwrite($arquivo_log, $subcategoria->id.";".$subcategoria->nome.";".$subcategoria->meli_cat_nome.";".$subcategoria->meli_id.";".ArrayHelper::getValue($response_categoria, 'body.attribute_types')."\n");
        }

        fclose($arquivo_log);

        echo "\n\nFIM!\n\n";
        */

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andwhere(['=', 'id', 98])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;

        //$response_shipping = $meli->get("/shipments/40284148741?access_token=" . $meliAccessToken);



        $body = [
            "variations" => []
        ];



        $variacao= ProdutoFilialVariacao::find()->andWhere(['=', 'produto_filial_id',513356])->all();

        if ($variacao){



            foreach ($response["body"]->variations as $variacao) {
                print_r($variacao);die;
                echo "\n";
                print_r($variacao->id);
                echo " - ";
                print_r($variacao->available_quantity);
                echo " - ";print_r($variacao->price);

            $body["variations"][] = [
                "id" => $variacao->meli_id,
                "available_quantity" => $produto_filial->quantidade,
                "price" => utf8_encode(round($produto_filial->getValorMercadoLivre(), 2)),
            ];
        }


            $response = $meli->post("items?access_token=" . $meliAccessToken, $body);

            //$response = $meli->put("items/MLB1283634519?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                echo " - ERROR";
            } else {
                echo " - OK";
            }
        }



        echo "\n\nFIM TESTE";



    }

}
 