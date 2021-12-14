<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class CorrigirCategoriaPlanilhaAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_corrigir_categoria_planilha_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        // $filial = Filial::find()->andWhere(['=', 'id', 72])->one(); //CONTA PRINCIPAL
        $filial = Filial::find()->andWhere(['=', 'id', 98])->one(); //CONTA DUPLICADA
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);


        $response = ArrayHelper::getValue($user, 'body');



        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

            $meliAccessToken = $response->access_token;

            $LinhasArray = Array();
            //$file = fopen("/var/tmp/produtos_under_review.csv", 'r');
            //$file = fopen("/var/tmp/ml_produtos_inativos_categoria_21-09-2020.csv", 'r');
            // $file = fopen("/var/tmp/ml_produtos_inativos_categoria_22-09-2020.csv", 'r');
            //$file = fopen("/var/tmp/ml2_produtos_inativos_categoria_22-09-2020.csv", 'r');

            $file = fopen("/var/tmp/ml2_produtos_inativos_categoria_06-10-2020.csv", 'r');






            while (($line = fgetcsv($file,null,';')) !== false)
            {
                $LinhasArray[] = $line;
            }
            fclose($file);
            
            foreach ($LinhasArray as $i => &$linhaArray){

                echo "\n".$i ." - ". $linhaArray[0] ." - ".$linhaArray[3]." - ".$linhaArray[4];
                fwrite($arquivo_log, "\n".$linhaArray[0].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'"');
                //continue;

                if($i <= 0){
                    fwrite($arquivo_log, ';"status";permalink');
                    continue;
                }
                
                $meli_id = $linhaArray[0];
                
                //echo "\n\n\n".'https://api.mercadolibre.com/items/'.$meli_id.'?access_token='.$meliAccessToken."\n\n\n";
                
                $response_item = $meli->get('https://api.mercadolibre.com/items/'.$meli_id.'?access_token='.$meliAccessToken);
                //print_r($response_item); die;
                
                if ($response_item['httpCode'] < 300) {
                    
                    echo " - Produto encontrado";
                    
                    $nome = str_replace(" ","%20",ArrayHelper::getValue($response_item, 'body.title'));

                    $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

                    $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');

                    $nome = str_replace($comAcentos, $semAcentos, $nome);
                    
                    $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                    //print_r($response_categoria_recomendada); die;
                    
                    if ($response_categoria_recomendada['httpCode'] >= 300) {
                        //print_r($response_categoria_recomendada); die;

                        echo " - ERRO Categoria Recomendada";
                    }
                    else {
                        echo " - OK Categoria Recomendada";
                        
                        $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                        $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                        echo " - ".$categoria_meli_id." - ".$categoria_meli_nome;
                        
                        $body = ["category_id" => utf8_encode(ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id'))];
                        $response = $meli->put("items/{$meli_id}?access_token=" . $meliAccessToken, $body, [] );

                        if ($response['httpCode'] >= 300) {
                            //print_r($response);
                            //print_r($response_item); die;

                            echo " - ERROR Categoria";
                            fwrite($arquivo_log, ";Categoria nao alterada");
                        }
                        else{
                            echo " - OK Categoria";
                            fwrite($arquivo_log, ";Categoria alterada");
                        }
                    }
                }
                else{
                    echo " - Produto não encontrado";
                }
            }
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
