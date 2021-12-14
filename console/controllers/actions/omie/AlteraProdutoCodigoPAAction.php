<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;
use common\models\Produto;

class AlteraProdutoCodigoPAAction extends Action
{
    public function run()//$produto_id)
    {

        echo "Criando produtos... São Paulo\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);

        $produtos = Produto::find() //->andWhere(["=","id",$produto_id])
                                    ->orderBy("id")
                                    ->all();

        foreach ($produtos as $k => $produto) {

            echo "\n".$k." - ".$produto->id." - ".$produto->codigo_global;

            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_SP,
                "app_secret" => static::APP_SECRET_OMIE_SP,
                "param" => [
                        "codigo_produto_integracao" => $produto->codigo_global,
                        "codigo"                    => "PA".$produto->id,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (SP)";
            }
            else{
                echo " - OK (SP)";
            }

            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_MG,
                "app_secret" => static::APP_SECRET_OMIE_MG,
                "param" => [
                    "codigo_produto_integracao" => $produto->codigo_global,
                    "codigo"                    => $produto->codigo_global,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (MG)";
            }
            else{
                echo " - OK (MG)";
            }
            //die;
        }
    }
}
