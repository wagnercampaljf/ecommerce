<?php

namespace console\controllers\actions\omie;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\helpers\ArrayHelper;

class VannucciAtualizarEstoquePeloOmieAction extends Action
{
    
    const APP_ID                    = '3029992417140266';
    const SECRET_KEY                = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA     = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $arquivo_log = fopen("/var/tmp/vannucci_atualizar_estoque_pelo_omie".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;quantidade;status\n");
        
        $produtos_filial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 96])->all();
        
        foreach ($produtos_filial as $k => $produto_filial){

            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->produto_id." - ";
            
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto_filial->produto->id,
                ]
            ];
            $response_produto = $meli->consulta("/api/v1/geral/produtos/?JSON=",$body);
            //print_r($response_produto);
            
            print_r(ArrayHelper::getValue($response_produto, 'body.quantidade_estoque'));
            
            $quantidade = 0;
            if(ArrayHelper::getValue($response_produto, 'body.quantidade_estoque') != ""){
                $quantidade = ArrayHelper::getValue($response_produto, 'body.quantidade_estoque');
            }
            
            echo " - ".$quantidade;
            
            $produto_filial->quantidade = $quantidade;
            if($produto_filial->save()){
                echo " - Quantidade alterada";
                fwrite($arquivo_log, $produto_filial->id.";".$quantidade.";Quantidade alterada\n");
            }
            else{
                echo " - Quantidade não alterada";
                fwrite($arquivo_log, $produto_filial->id.";".$quantidade.";Quantidade não alterada\n");
            }
            /*$body = [
                "call" => "MovimentoEstoque",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "cod_int"       => ArrayHelper::getValue($response_produto, 'body.codigo_produto_integracao'),
                    "datainicial"   => "01/01/2010",
                    "dataFinal"     => "31/12/2020",
                ]
            ];
            $response_omie = $meli->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            print_r($response_omie);
            die;*/
        }
      
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







