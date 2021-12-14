<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemVannucciPAAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da Vannucci: \n\n";
        
        $path = "/var/tmp/vannucci_2021-11-18/com_logo/";
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
           
            if($arquivo == "." || $arquivo == ".."){
                continue;

            }
            echo "\n".$arquivo;
            //continue;
            
            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/vannucci_2021-11-18/sem_logo/".$arquivo;




            //$codigo_fabricante          = "L".str_replace("-","", str_replace(".jpg","",$arquivo));


            $codigo_pa          =  str_replace(".webp","",$arquivo);

            // $produto_filial             = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
            //                                               ->andWhere(['=','codigo_fabricante',$codigo_pa])
            //                                               ->andWhere(['=','filial_id',43])
            //                                               ->one();

            $produto           = Produto::find()    ->andWhere(['=','produto.id',$codigo_pa])                                                    
                                                    ->one();

            echo "\n".$caminhoImagemComLogo;
            echo "\n".$caminhoImagemSemLogo;
           // echo "\n".$codigo_pa;
            if (isset($produto)){
                echo "\n".$produto->id;
                
                $imagem                     = new Imagens;
                $imagem->produto_id         = $produto->id;
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