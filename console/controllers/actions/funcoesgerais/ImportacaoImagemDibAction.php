<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemDibAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da Vannucci: \n\n";

        if (file_exists("/var/tmp/log_importar_imagem_dib.csv")){
            unlink("/var/tmp/log_importar_imagem_dib.csv");
        }

        //Escreve no log
        $arquivo_log = fopen("/var/tmp/log_importar_imagem_dib.csv", "a");
        fwrite($arquivo_log, "arquivo;status\n");

        //$path = "/var/tmp/DIB_FOTOS/com_logo/";
	$path = "/var/tmp/DIB_FOTOS_02/com_logo/";

        $k = 0;

        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){

            echo $k . " - ";
            $k++;

            if($arquivo == "." || $arquivo == ".."){
                continue;
            }

            var_dump($arquivo); echo " - ";

            fwrite($arquivo_log, $arquivo);

            $caminhoImagemComLogo       = $path.$arquivo;
            //$caminhoImagemSemLogo       = "/var/tmp/DIB_FOTOS/sem_logo/".$arquivo;
	    $caminhoImagemSemLogo       = "/var/tmp/DIB_FOTOS_02/sem_logo/".$arquivo;

            $codigo_global              = str_replace(".jpg","",$arquivo);
            $produto_filial             = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                                ->where("filial_id = 97 and (codigo_global like '".$codigo_global."')")
                                                                ->one();

            if(!isset($produto_filial)){
                echo " - Produto_Filial não encontrado";
                echo "\n";
                continue;
            }

            $imagem_teste = Imagens::find()->andWhere(["=","produto_id",$produto_filial->produto_id])->one();
            if(isset($imagem_teste)){
                echo " - Imagem já encontrada \n";
                echo "\n";
                continue;
            }

            $imagem                     = new Imagens;
            $imagem->produto_id         = $produto_filial->produto_id;
            $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
            $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
            $imagem->ordem              = 1;
            var_dump($imagem->save());
            echo "\n";
        }
        $diretorio -> close();

        fclose($arquivo_log);

        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}
