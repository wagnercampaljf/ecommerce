<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemVannucciAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da Vannucci: \n\n";
        
         if (file_exists("/var/tmp/fotos_vannucci_27000.csv")){
            unlink("/var/tmp/fotos_vannucci_27000.csv");
        }
        
        //Escreve no log
        $arquivo_log = fopen("/var/tmp/fotos_vannucci_27000.csv", "a");
        fwrite($arquivo_log, "arquivo;status\n");
        
        $path = "/var/tmp/fotos_vannucci_27000/com-logo/";
        
        $x = 0;

        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
            
            echo "\n".$x;
           
            if($arquivo == "." || $arquivo == ".."){
                continue;
            }
            
            //var_dump($arquivo); echo "\n";
            
            fwrite($arquivo_log, $arquivo);
            
            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/fotos_vannucci_27000/sem-logo/".$arquivo;

            $codigo_fabricante          = str_replace(".webp","",$arquivo);
            
            //echo " - ".$caminhoImagemComLogo;
            //echo " - ".$caminhoImagemSemLogo;
            echo " - ".$codigo_fabricante;
            $codigo_fabricante_reduzido_array   = explode("-",$codigo_fabricante);
            $codigo_fabricante_reduzido         = $codigo_fabricante_reduzido_array[0];
            echo " - ".$codigo_fabricante_reduzido;
            
            $produto_filiais = ProdutoFilial::find()->joinWith('produto', true, 'INNER JOIN')
                                                    ->where("filial_id = 38 and codigo_fabricante like '".$codigo_fabricante_reduzido."%'")
                                                    ->all();

            foreach($produto_filiais as $i => $produto_filial){
                
                $codigo_fabricante_array = explode("-",$produto_filial->produto->codigo_fabricante);
                $codigo_fabricante_peca          = $codigo_fabricante_array[0];
                echo " - ".$codigo_fabricante_peca;
                
                if($codigo_fabricante_reduzido != $codigo_fabricante_peca){
                    //echo " - Produto Diferente";
                    continue;
                }
                
                $imagem_teste = Imagens::find()->andWhere(["=","produto_id",$produto_filial->produto_id])->one();
                                
                if($imagem_teste){
                    " - Imagem já cadastrado";
                    continue;
                }
                
                echo " - ".$produto_filial->produto->codigo_fabricante;
                $imagem                     = new Imagens;
                $imagem->produto_id         = $produto_filial->produto_id;
                $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
                $imagem->ordem              = 1;
                var_dump($imagem->save());
            }
            
            $x ++;
        }
        $diretorio -> close();
        
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}
