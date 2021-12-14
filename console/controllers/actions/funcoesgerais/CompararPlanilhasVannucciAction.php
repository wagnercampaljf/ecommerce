<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class CompararPlanilhasVannucciAction extends Action
{
    public function run(){

        echo "INÍCIO da função de comparação de planilhas Vannucci: \n\n";

        $produtos_novos = Array();
        $file = fopen('/var/tmp/vannucci_produtos_ausentes_2021-10-19.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_novos[] = $line;
        }
        fclose($file);

        $produtos_dados = Array();
        $file = fopen('/var/tmp/vannucci_estoque_2021-10-19.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_dados[] = $line;
        }
        fclose($file);

        $arquivo_para_criacao = fopen("/var/tmp/vannucci_produtos_novos_".date("Y-m-d").".csv", "a");
        fwrite($arquivo_para_criacao, "codigo_fabricante;codigo_global;codigo_montadora;codigo_barras;estoque;peso;altura;largura;profundidade;nome;aplicacao");
        
        foreach ($produtos_novos as $i => &$produto_novo){
            echo "\n".$i." - ".$produto_novo[0];
            $codigo_fabricante_novo  =  $produto_novo[0];
            
            $linha = "\n".$codigo_fabricante_novo.";;;;;;;;;;;";
                        
            foreach ($produtos_dados as $k => &$produto_dados ){
                $codigo_fabricante_dados  =  $produto_dados[4];
                
                if($codigo_fabricante_novo == $codigo_fabricante_dados){
                    $linha = "\n".'"'.$codigo_fabricante_novo.'";"'.$produto_dados[2].'";"'.$produto_dados[3].'";"'.$produto_dados[6].'";'.$produto_dados[7].";2;30;30;30;".'"'.$produto_dados[1]." ".$produto_dados[5].'";"'.$produto_dados[1]." ".$produto_dados[5].'"';
                    break;
                }
            }
            fwrite($arquivo_para_criacao, $linha);
        }

        fclose($arquivo_para_criacao);

        echo "\n\nFIM da função de comparação de planilhas Vannucci!";
    }
}


