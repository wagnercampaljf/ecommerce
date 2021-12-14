<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\Produto;

class AlteraProdutosParaCodigoPAMGAction extends Action
{
    public function run()//$global_id)
    {
        
        echo "Alterando produtos... São Paulo\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        
        //Inicio teste Fred
            /*$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_SP.'","app_secret":"'.static::APP_SECRET_OMIE_SP.'","param":[{"codigo":"FTS161008LL"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            print_r($produtos_codigo); 
            die;*/
        //Fim teste Fred

        $produtos = Produto::find() //->andWhere(["=","id",249620])
                                    ->orderBy("id")
                                    ->all();
        
        foreach ($produtos as $k => $produto) {

            echo "\n".$k." - ".$produto->id." - ".$produto->codigo_global;

	    if($k<=42000){
		continue;
	    }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_MG.'","app_secret":"'.static::APP_SECRET_OMIE_MG.'","param":[{"codigo":"'.$produto->codigo_global.'"}]}');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produtos = curl_exec($ch);
            $produtos_codigo = json_decode($produtos);
            curl_close($ch);
            //print_r($produtos_codigo); 
            
            $codigo_produto = '';
            
            if(isset($produtos_codigo->codigo_produto)){
                echo " - Produto encontrado pelo código_global";
                $codigo_produto = $produtos_codigo->codigo_produto;
            }
            else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"'.static::APP_KEY_OMIE_MG.'","app_secret":"'.static::APP_SECRET_OMIE_MG.'","param":[{"codigo":"PA'.$produto->id.'"}]}');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $produtos = curl_exec($ch);
                $produtos_codigo = json_decode($produtos);
                curl_close($ch);
                //print_r($produtos_codigo); 
                
                if(isset($produtos_codigo->codigo_produto)){
                    echo " - Produto encontrado pelo código PA";
                    $codigo_produto = $produtos_codigo->codigo_produto;
                }
                else{
                    echo " - Produto não encontrado";
                    continue;
                }
            }
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_MG,
                "app_secret" => static::APP_SECRET_OMIE_MG,
                "param" => [
                    "codigo_produto"            => $codigo_produto,
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (MG)";
            }
            else{
                echo " - OK (MG)";
            }
        }
    }
}



