<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemLNGAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da BR: \n\n";
        
        $path = "/var/tmp/imgs_morelate/com_logo/";
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
           
            if($arquivo == "." || $arquivo == ".."){
                continue;
            }
            
            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/imgs_morelate/com_logo/".$arquivo;




            //$codigo_fabricante          = "L".str_replace("-","", str_replace(".jpg","",$arquivo));


            $codigo_fabricante          =  str_replace(".jpg","",$arquivo).".M";

            $produto_filial             = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                          ->andWhere(['=','codigo_fabricante',$codigo_fabricante])
                                                          ->andWhere(['=','filial_id',43])
                                                          ->one();
            echo "\n".$caminhoImagemComLogo;
            echo "\n".$caminhoImagemSemLogo;
            echo "\n".$codigo_fabricante;
            if (isset($produto_filial)){
                echo "\n".$produto_filial->id." - ".$produto_filial->produto->id;
                
                $imagem                     = new Imagens;
                $imagem->produto_id         = $produto_filial->produto_id;
                $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
                $imagem->ordem              = 1;
                echo "==>";var_dump($imagem->save());echo "<==";
            }
        }
        
        $diretorio -> close();
        
        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}