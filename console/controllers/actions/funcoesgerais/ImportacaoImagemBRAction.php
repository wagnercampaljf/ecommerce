<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemBRAction extends Action
{
    public function run(){

      
 
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/EMBLEMAS_emate2011_2021-08-03.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
       foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo $k." - ";
            
            if($k==0){
                continue;
            }
            
            $produto_filial = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                    ->where("filial_id = 8 and (codigo_fabricante like '".$linhaArray[1]."' or codigo_fabricante like '0".$linhaArray[1]."')")
                                                    ->one();
            echo " - ".$linhaArray[0]." - ";
            if($produto_filial){
                echo "Produto ENCONTRADO";
                $caminhoImagemComLogo   = "/var/tmp/imagens_produto/".$linhaArray[4].".webp";
                $caminhoImagemSemLogo   = "/var/tmp/imagens_produto/".$linhaArray[4].".webp";
                
                if (file_exists($caminhoImagemComLogo)) {
                    echo $caminhoImagemComLogo." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produto_filial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    $imagem->save();
                }
                else{
                    echo " - NÃO EXISTE \n";
                }
            }
            else{
                echo "Produto NÃO ENCONTRADO";
            }
        }

        echo "\n\nFIM da rotina de criação preço!";
        
    }
}