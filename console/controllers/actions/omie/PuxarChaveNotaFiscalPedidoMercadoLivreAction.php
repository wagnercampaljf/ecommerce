<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\PedidoMercadoLivre;
use backend\models\NotaFiscal;

class PuxarChaveNotaFiscalPedidoMercadoLivreAction extends Action
{
    public function run()
    {
        
        //Função deve rodar de segunda a sexta
       
        $arquivo_log = fopen("/var/tmp/log_puxar_chave_nota_fiscal_ml_".date("Y-m-d_H-i-s").".csv", "a");
        
        echo "Puxar chave nota fiscal...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';

        $pedidos_mercado_livre = PedidoMercadoLivre::find() ->andWhere(["=", "e_pedido_autorizado", true])
                                                            ->andWhere(["=", "e_xml_subido", true])
                                                            ->orderBy(["id"=> SORT_DESC])
                                                            ->all();
        
        foreach($pedidos_mercado_livre as $k => $pedido_mercado_livre){
            echo "\n".$k." - pedido_mercado_livre_id:".$pedido_mercado_livre->id;
            
            if($k < 0){
                echo " - pular";
                continue;
            }
            
            $app_key    = $APP_KEY_OMIE_SP;
            $app_secret = $APP_SECRET_OMIE_SP;
            if($pedido_mercado_livre->user_id == "435343067"){
                $app_key    = $APP_KEY_OMIE_CONTA_DUPLICADA;
                $app_secret = $APP_SECRET_OMIE_CONTA_DUPLICADA;
            }
            
            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $app_key,
                "app_secret" => $app_secret,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            
            $response_pedido = $omie->consulta("/api/v1/produtos/pedido/",$body);
            //print_r($response_pedido);  
            
            if($response_pedido["httpCode"] < 300){
                
                echo " - pedido_encontrado_omie:".$response_pedido["body"]["pedido_venda_produto"]["cabecalho"]["codigo_pedido"];
                
                $body = [
                    "call" => "ConsultarNF",
                    "app_key" => $app_key,
                    "app_secret" => $app_secret,
                    "param" => [
                        "nIdPedido" => $response_pedido["body"]["pedido_venda_produto"]["cabecalho"]["codigo_pedido"]
                    ]
                ];
                
                $response_nota_fiscal = $omie->consulta("/api/v1/produtos/nfconsultar/",$body);
                //print_r($response_nota_fiscal);
                if($response_nota_fiscal["httpCode"] < 300){
                //if(array_key_exists("compl", $response_nota_fiacal["body"])){
                    //print_r($response_nota_fiacal["body"]["compl"]["cChaveNFe"]);
                    echo " - nota_fiscal_omie:".$response_nota_fiscal["body"]["compl"]["cChaveNFe"];
                    
                    $nota_fiscal = NotaFiscal::find()->andWhere(["=", "chave_nf", $response_nota_fiscal["body"]["compl"]["cChaveNFe"]])->one();
                    if($nota_fiscal){
                        echo " - nota_fiscal_id:".$nota_fiscal->id;
                        $pedido_mercado_livre->nota_fiscal_id = $nota_fiscal->id;
                        if($pedido_mercado_livre->save()){
                            echo "Pedido alterado";
                        }
                        else{
                            echo "Pedido não alterado";
                        }
                    }
                    else{
                        echo " - nota não encontrada no sistema";
                    }
                }
                else{
                    echo " - nota não encontrada";
                }
            }
            
            //die;
            
        }
        
        fclose($arquivo_log);
 
    }
}

