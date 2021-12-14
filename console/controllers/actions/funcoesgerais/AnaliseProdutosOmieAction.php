<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AnaliseProdutosOmieAction extends Action
{
    public function run(){
        
        $arquivo_log = fopen("/var/tmp/log_analise_omie_".date("Y-m-d_H-i-s").".csv", "a");
        
        //$file = fopen("/home/dev-pecaagora/Downloads/compras,_estoque_e_producao_289137802100957.csv", 'r'); //Abre arquivo com preços para subir
        $file = fopen("/var/tmp/compras,_estoque_e_producao_289137802100957.csv", 'r'); //Abre arquivo com preços para subir
        $x = 0;
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            
            $codigo     = $line[2];
            $preco      = (float) str_replace(",", ".",$line[7]);
            $estoque    = (int) str_replace(",", ".",$line[8]); 
            
            echo "\n".$x++." - ".$codigo." - ".$estoque." - ".$preco;
            
            if($estoque == 0){
                continue;
            }
            else{

                fwrite($arquivo_log, "\n".$codigo.";".$estoque.";".$preco);
                
                $produto = new Produto;
                
                $pos = strpos($codigo, "PA");
                                
                if ($pos === false) {
                    echo " - Global";
                    $produto = Produto::find()->andWhere(['=', 'codigo_global', $codigo])->one();
                }
                else{
                    echo " - PA";
                    $codigo_limpo = str_replace("PA", "", $codigo);
                    $produto = Produto::find()->andWhere(['=', 'id', $codigo_limpo])->one();
                }

                if(!$produto){
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, ";Produto não encontrado");
                    continue;
                }
                
                $produto_filial = ProdutoFilial::find() ->andWhere(["=", "produto_id", $produto->id])
                                                        ->andWhere(["filial_id" => [38, 43, 60, 72, 86, 97]])
                                                        ->one();
                
                if($produto_filial){
                    $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=', 'produto_filial_id', $produto_filial->id])
                                                                        ->orderBy(['dt_inicio' => SORT_DESC])
                                                                        ->one();
                    
                    if($valor_produto_filial){
                        echo " - ".$valor_produto_filial->valor_compra;
                        fwrite($arquivo_log, ";".$valor_produto_filial->valor_compra);
                    }
                    else{
                        echo " - Valor não encontrado";
                        fwrite($arquivo_log, ";Valor não encontrado");
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ";Estoque não encontrado");
                }
            }
        }
        
        fclose($file);
        fclose($arquivo_log);
    }
}