<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class CompararPlanilhasMorelateAction extends Action
{
    public function run(){

        echo "INÍCIO da função de comparação de planilhas Vannucci: \n\n";

        $produtos_novos = Array();
        $file = fopen('/var/tmp/more_antiga.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_novos[] = $line;
        }
        fclose($file);

        $produtos_dados = Array();
        $file = fopen('/var/tmp/Produtos_more_fora_da_base_Dez.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_dados[] = $line;
        }
        fclose($file);

        $arquivo_para_criacao = fopen("/var/tmp/morelate_fora_base_teste".date("Y-m-d").".csv", "a");
        // fwrite($arquivo_para_criacao, "codigo_fabricante;codigo_global;codigo_montadora;codigo_barras;estoque;peso;altura;largura;profundidade;nome;aplicacao");
        fwrite($arquivo_para_criacao, "Código;Estoque;");
        
        /*foreach ($produtos_novos as $i => &$produto_novo){

            if($i <=0){continue;}

            
            

           // echo "\n".$i." - ".$produto_novo[0];
            $codigo_fabricante_novo  =  $produto_novo[0];
            
           // $linha = "\n".$codigo_fabricante_novo.";;;;;;;;;;;";
                        
            foreach ($produtos_dados as $k => &$produto_dados ){

                // echo "<prev>";
                // print_r($produto_dados);
                // echo "</prev>";
                // die;
                

                $codigo_fabricante_dados  =  $produto_dados[0];
                
                if($codigo_fabricante_novo != $codigo_fabricante_dados){

                    echo "\n".$k." - ".$produto_dados[0]." - Não encontrado na planilha";

                    // $linha = "\n".'"'.$codigo_fabricante_dados.'";"'.$produto_dados[1].'";"'; 
                    fwrite($arquivo_para_criacao, "\n ;".$codigo_fabricante_dados.'";"'.$produto_dados[1].'";"'." -  Não encontrado na planilha");
                     break;  
                     //continue;               
                }
            
        }*/
            
        $produtos_ausentes = [];

        foreach ($produtos_dados as $k => &$produto_dados ){
       

           // if($k <=0){continue;}

            //fwrite($arquivo_para_criacao, "\n ;".$produto_dados[0].'";"'.$produto_dados[1].'";"');

            $e_produto_encontrado = false;


            // $codigo_fabricante_novo  =  $produto_novo[0];
            $codigo_fabricante_dados  =  $produto_dados[0];

                        
            foreach ($produtos_novos as $i => &$produto_novo){

                if($k <=0){continue;}

                // $codigo_fabricante_dados  =  $produto_dados[0];
                $codigo_fabricante_novo  =  $produto_novo[0];
                
                // if($codigo_fabricante_novo == $codigo_fabricante_dados){
                    
                   
                if($codigo_fabricante_novo === $codigo_fabricante_dados){

                    echo "\n".$k." - ".$produto_dados[0]." - Encontrado na planilha";
                    $e_produto_encontrado = true;
                    //fwrite($arquivo_para_criacao," - Encontrado na planilha");
                    break;
             
                }else{
                    echo "\n".$k." - ".$produto_dados[0]. " - Não encontrado \n";   
                    $e_produto_encontrado = false;                 
                    break;
                }           
            
            }

            if(!$e_produto_encontrado){
                $produtos_ausentes[] =  $codigo_fabricante_dados;
                //fwrite($arquivo_para_criacao,'";"'." - Não Encontrado na planilha");
            }
            
        }

        //print_r($produtos_ausentes);

        //fclose($arquivo_para_criacao);
        

        echo "\n\nFIM da função de comparação de planilhas Morelate!";
    }
}


