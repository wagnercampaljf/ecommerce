<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class ImportacaoCodigoBarrasBRAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação EAN: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/codigo_barras_br.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_importacao_codigo_barras_br.csv")){
            unlink("/var/tmp/log_importacao_codigo_barras_br.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_importacao_codigo_barras_br.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_id;codigo_fabricante;codigo_barras;status\n");
        
        foreach ($LinhasArray as $k => &$linhaArray){
            $codigo_fabricante      = str_replace('-','',str_replace(' ','',$linhaArray[1]));
            $codigo_fabricante_l    = 'L'.$codigo_fabricante;
            
            if ($codigo_fabricante == null or $codigo_fabricante == ""){
                // Escreve no log
                fwrite($arquivo_log, ';;;codigo_fabricante vazio\n');
            }
            else {
                $produto_filial = ProdutoFilial::find()->joinWith(['produto'])
                                                //->andWhere(['=','filial_id',60])
                                                ->where("filial_id = 72 and (produto.codigo_fabricante = '".$codigo_fabricante."' or produto.codigo_fabricante = '".$codigo_fabricante_l."')")
                                                ->one();

                if (isset($produto_filial)){
                    
                    $produto = $produto_filial->produto;
                    
                    echo $k." - Produto encontrado ID - "; echo $produto->id; echo "\n";
       
                    $produto->codigo_barras = $linhaArray[12];
                    $produto->save();
                    
                    // Escreve no log
                    fwrite($arquivo_log, $produto->id.';'.$produto->codigo_fabricante.';'.$produto->codigo_barras.';OK\n');
                }
                else{
                    fwrite($arquivo_log, ';;;Produto Não encontrado\n');
                }
            }
            
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação EAN!";
    }
}
