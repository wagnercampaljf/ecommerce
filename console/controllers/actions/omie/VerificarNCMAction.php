<?php

namespace console\controllers\actions\omie;

//use common\models\Produto;
//use common\models\ValorProdutoFilial;
//use console\controllers\actions\omie\Omie;
//use Yii;
//use yii\base\Action;
use common\models\Produto;
use yii\helpers\ArrayHelper;


class VerificarNCMAction extends Action
{
    public function run()
    {
       
        echo "Verificar e corrigir TODAS as contas no Omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        //TESTE
        

        
        //TESTE

        echo "\nInicio \n";
        
        
        //$file_log = fopen("/var/tmp/log_omie_verificar_ncm_principal.csv", 'a');
        //$file_log = fopen("/var/tmp/log_omie_verificar_ncm_minas_gerais.csv", 'a');
        //$file_log = fopen("/var/tmp/log_thairini_ncm_20-10-2020_01.csv", 'a');
        $file_log = fopen("/var/tmp/log_thairini_ncm_20-10-2020_02.csv", 'a');
        
        
        //PRODUTOS CONTA MINAS GERAIS
        $produtos = Array();
        //$file = fopen("/var/tmp/omie_verificar_ncm_01.csv", 'r');
        //$file = fopen("/var/tmp/omie_verificar_ncm_minas_gerais.csv", 'r');
        //$file = fopen("/var/tmp/thairini_ncm_20-10-2020_01.csv", 'r');
        $file = fopen("/var/tmp/thairini_ncm_20-10-2020_02.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos[] = $line;
        }
        fclose($file);
        
        echo "\n\n arrays preenchidos";
        
        $x = 0;
        
        //print_r($produtos_conta_minas_gerais);die;
        
        foreach ($produtos as $k => $produto){
            
            echo "\n".$k." - ".$produto[9]." - ".$produto[12];
            //continue;
            
            fwrite($file_log, $produto[9].";".$produto[12]);
            
            //CONTA PRICNIPAL
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    //"codigo_produto" => $produto_minas_gerais[2],
                    "codigo" => $produto[9],
                    //"codigo_integracao" => $produto_minas_gerais[2],
                ]
            ];
            
            $response = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
            //print_r($response_produto_minas_gerais);
            
            $pa_limpo = (int) substr(str_replace("PA", "", $produto[9]),0,9);
            $produto_pecaagora = Produto::find()->andWhere(['=','id', $pa_limpo])->one();
            $ncm_pecaagora = "";
            if($produto_pecaagora){
                $ncm_pecaagora = $produto_pecaagora->codigo_montadora;
            }
            
            fwrite($file_log, ";".$ncm_pecaagora);
            
            if($response["httpCode"] < 300){
                echo " - Produto encontrado (Omie SP)";
                fwrite($file_log, ";".$response["body"]["ncm"].";Produto encontrado (Omie - SP)");
                
                if(str_replace(".","",$response["body"]["ncm"]) == str_replace(".","",$produto[12])){
                    fwrite($file_log, ";NCMs iguais");
                }
                else{
                    fwrite($file_log, ";NCMs diferentes");
                }
            }
            else{
                echo " - Produto não encontrado (Omie - SP)";
                fwrite($file_log, ";;Produto não encontrado (Omie - SP)");
            }
            
            
            
            
            //CONTA MINAS GERAIS
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_MG,
                "app_secret" => $APP_SECRET_OMIE_MG,
                "param" => [
                    //"codigo_produto" => $produto_minas_gerais[2],
                    "codigo" => $produto[9],
                    //"codigo_integracao" => $produto_minas_gerais[2],
                ]
            ];
            
            $response = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
            //print_r($response_produto_minas_gerais);
            
            if($response["httpCode"] < 300){
                echo " - Produto encontrado (Omie MG)";
                fwrite($file_log, ";".$response["body"]["ncm"].";Produto encontrado (Omie MG)");
                
                if(str_replace(".","",$response["body"]["ncm"]) == str_replace(".","",$produto[12])){
                    fwrite($file_log, ";NCMs iguais");
                }
                else{
                    fwrite($file_log, ";NCMs diferentes");
                }
            }
            else{
                echo " - Produto não encontrado (Omie MG)";
                fwrite($file_log, ";;Produto não encontrado (Omie MG)");
            }
            
            
            
            
            //CONTA DUPLICADA
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                "param" => [
                    //"codigo_produto" => $produto_minas_gerais[2],
                    "codigo" => $produto[9],
                    //"codigo_integracao" => $produto_minas_gerais[2],
                ]
            ];
            
            $response = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
            //print_r($response_produto_minas_gerais);
            
            if($response["httpCode"] < 300){
                echo " - Produto encontrado (Omie CD)";
                fwrite($file_log, ";".$response["body"]["ncm"].";Produto encontrado (Omie CD)");
                
                if(str_replace(".","",$response["body"]["ncm"]) == str_replace(".","",$produto[12])){
                    fwrite($file_log, ";NCMs iguais\n");
                }
                else{
                    fwrite($file_log, ";NCMs diferentes\n");
                }
            }
            else{
                echo " - Produto não encontrado (Omie CD)";
                fwrite($file_log, ";;Produto não encontrado (Omie CD)\n");
            }
            
            //die;
        }
    }
}



