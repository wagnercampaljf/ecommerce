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
        
         if (file_exists("/var/tmp/log_importar_imagem_vannucci.csv")){
            unlink("/var/tmp/log_importar_imagem_vannucci.csv");
        }
        
        //Escreve no log
        $arquivo_log = fopen("/var/tmp/log_importar_imagem_vannucci.csv", "a");
        fwrite($arquivo_log, "arquivo;status\n");
        
        $path = "/var/tmp/vannucci/fotos_com_logo/";

        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
           
            if($arquivo == "." || $arquivo == ".."){
                continue;
            }
            
            var_dump($arquivo); echo "\n";
            
            fwrite($arquivo_log, $arquivo);
            
            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/vannucci/fotos_sem_logo/".$arquivo;

            $codigo_fabricante          = str_replace(".jpg","",$arquivo);
            $produto_filial             = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                                ->where("filial_id = 96 and fabricante_id = 91 and (codigo_fabricante like '".$codigo_fabricante."')")
                                                                ->one();

            if(!isset($produto_filial)){
                continue;
            }
            
            $imagem_teste = Imagens::find()->andWhere(["=","produto_id",$produto_filial->produto_id])->one();
            if(isset($imagem_teste)){
                continue;
            }
            
            echo "\n".$arquivo;
            $imagem                     = new Imagens;
            $imagem->produto_id         = $produto_filial->produto_id;
            $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
            $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
            $imagem->ordem              = 1;
            var_dump($imagem->save());
        }
        $diretorio -> close();
        
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}
