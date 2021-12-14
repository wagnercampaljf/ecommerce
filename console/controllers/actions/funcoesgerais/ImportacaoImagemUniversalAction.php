<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;

class ImportacaoImagemUniversalAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da Universal: \n\n";

         if (file_exists("/var/tmp/log_importar_imagem_universal.csv")){
            unlink("/var/tmp/log_importar_imagem_universal.csv");
        }

        //Escreve no log
        $arquivo_log = fopen("/var/tmp/log_importar_imagem_universal.csv", "a");
        fwrite($arquivo_log, "arquivo;status\n");

        $path = "/var/tmp/ImagensUniversalGeradas/com_logo_reduzido_universal/";
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){

            if($arquivo == "." || $arquivo == ".."){
                continue;
            }

            var_dump($arquivo); echo "\n";

            fwrite($arquivo_log, $arquivo);

            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/ImagensUniversalGeradas/sem_logo_reduzido_universal/".$arquivo;

            $produto                    = explode("_", str_replace(".jpg","",$arquivo));
            $produto_id                 = $produto[0];
            $ordem                      = $produto[1];

            $imagem                     = Imagens::find()->andWhere(['=','produto_id',$produto_id])->andWhere(['=','ordem',$ordem])->one();
            $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
            $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
            $imagem->ordem              = $ordem;
            $imagem->save(); 
        }
        $diretorio -> close();

        fclose($arquivo_log); 

        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}
