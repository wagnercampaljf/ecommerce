<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\Produto;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class AtualizaCategoriaMEBRAction extends Action
{

    public function run($filial_id = 1){

        echo "INÍCIO\n\n";


        $filial= Filial::find()->andWhere(["=","id",$filial_id])->one();

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            //print_r($meliAccessToken);

            $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'filial_id', $filial->id])->all();

            foreach ($produtos_filiais as $i => &$produto_filial) {

                if(($i <= 26410) ){
                    continue;
                }

                echo "\n".$i." - ".$produto_filial->id." - ".$produto_filial->produto->nome;
                $nome =  str_replace(" ","%20",$produto_filial->produto->nome);
                $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q='".$nome."'");

                $e_encontrou_categeoria_me = false;

                if($response_categoria_recomendada["httpCode"] < 300){
                    //print_r($response_categoria_recomendada);

                    if (!empty($response_categoria_recomendada["body"])) {

                        foreach(ArrayHelper::getValue($response_categoria_recomendada, 'body') as $categoria){
                            $response_categoria = $meli->get("categories/".ArrayHelper::getValue($categoria, 'category_id'));
                            $shipping_modes = ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes');
                            if(in_array("me2", $shipping_modes)){
                                echo " - ".ArrayHelper::getValue($categoria, 'category_id')." - Categoria ME";

                                $body = [
                                    "shipping"      => [
                                        "mode"          => "me2",
                                        "methods"       => [],
                                        "local_pick_up" => true,
                                        "free_shipping" => false,
                                        "logistic_type" => "cross_docking",
                                    ],
                                    "category_id" => ArrayHelper::getValue($categoria, 'category_id')
                                ];

                                $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                                if($response["httpCode"] >= 300){
                                    echo " - Erro";
                                }
                                else{
                                    echo " - OK"." - ".$response["body"]->permalink;
                                }

                                $e_encontrou_categeoria_me = true;

                                break;
                            }

                        }
                    }else{
                        echo " - body vazia";
                    }

                }

                if(!$e_encontrou_categeoria_me){
                    $body = [
                        "shipping"      => [
                            "mode"          => "me2",
                            "methods"       => [],
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "logistic_type" => "cross_docking",
                        ],
                        "category_id" => "MLB192954"
                    ];

                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    if($response["httpCode"] >= 300){
                        echo " - Erro (Padrão)";
                    }
                    else{
                        echo " - OK (Padrão)"." - ".$response["body"]->permalink;
                    }
                }
            }
        }
    }
}