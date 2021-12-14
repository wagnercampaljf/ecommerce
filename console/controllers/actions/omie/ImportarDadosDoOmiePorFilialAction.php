<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use common\models\Filial;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\ProdutoFilial;

class ImportarDadosDoOmiePorFilialAction extends Action
{
    public function run($filial_id)
    {
        
        echo "Sincronizar estoque omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $filial = Filial::find()->andwhere(['=', 'id', $filial_id])->one();
        
        $log = fopen("/var/tmp/importar_dados_do_omie_".str_replace(" ", "_",$filial->nome)."_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($log, "produto_filial_id;codigo_pa;quantidade_anterio;quantidade_atual;status");
        
        $produtos_filial = ProdutoFilial::find()->andWhere(['=', 'filial_id', $filial_id])->orderBy(['id'=>SORT_ASC])->all();
        
        foreach($produtos_filial as $k => $produto_filial){
            
            echo "\n".$k." - "."PA".$produto_filial->produto_id;
            
            
            $body = [
                "call" => "MovimentoEstoque",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    //"nPagina"       => 1,
                    "cod_int"       => "PA".$produto_filial->produto_id,
                    "datainicial"   => "01/01/2008",
                    "dataFinal"     => date("d/m/Y"),
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            //print_r($response_omie);
            
            $movimentacoes = ArrayHelper::getValue($response_omie, 'body.movProduto');
            
            if($movimentacoes){
                foreach(ArrayHelper::getValue($movimentacoes[count($movimentacoes)-1], 'movPeriodo') as $movimentacao){
                    $pos = strpos(ArrayHelper::getValue($movimentacao, 'tipo'), "Atual");
                    if (!($pos === false)) {
                        
                        $salto_atual = ArrayHelper::getValue($movimentacao, 'qtde');
                        echo "\nSaldo atual: ".$salto_atual;
                        
                        echo " - ".$produto_filial->quantidade." - ".$salto_atual;
                        fwrite($log, "\n".$produto_filial->id.";"."PA".$produto_filial->produto_id.";".$produto_filial->quantidade.";".$salto_atual);
                        
                        $quantidade = ($salto_atual >=0) ? $salto_atual : 0;
                        
                        $produto_filial->quantidade = $quantidade;
                        if($produto_filial->save()){
                            echo " - Quantidade alterada";
                            fwrite($log,";Quantidade alterada");
                        }
                        else{
                            echo " - Quantidade alterada";
                            fwrite($log,";Quantidade não alterada");
                        }
                    }
                }
            }
                    
                    
            /*$body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    //"codigo_produto_integracao" => "PA46642"
                    "codigo" => "PA".$produto_filial->produto_id
                ]
            ];
            $response_sp = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
            //print_r($response_sp);
            
            if($response_sp["httpCode"] < 300){
                echo " - ".$produto_filial->quantidade." - ".$response_sp["body"]["quantidade_estoque"];
                fwrite($log, "\n".$produto_filial->id.";"."PA".$produto_filial->produto_id.";".$produto_filial->quantidade.";".$response_sp["body"]["quantidade_estoque"]);
                
                $produto_filial->quantidade = $response_sp["body"]["quantidade_estoque"];
                if($produto_filial->save()){
                    echo " - Quantidade alterada";
                    fwrite($log,";Quantidade alterada");
                }
                else{
                    echo " - Quantidade alterada";
                    fwrite($log,";Quantidade não alterada");
                }
            }
            else{
                fwrite($log, "\n".$produto_filial->id.";"."PA".$produto_filial->produto_id.";;;Produto não encontrado no Omie");
            }*/
        }
        
        fclose($log);
    }
}

