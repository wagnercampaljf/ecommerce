<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class SubirImagensTesteWebpAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de renomeação de arquivos: \n\n";
        
        $path = "/var/tmp//imagens_tela_principal_webp/";
        $arquivos = array();        
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
            if($arquivo == "." or $arquivo == ".."){continue;}
            $arquivos[] = $path.$arquivo;
        }
        $diretorio -> close();

        foreach ($arquivos as $k => $arquivo_origem){
            if (strpos($arquivo_origem, "sem-logo") != false){
                continue;
            }
            $produto_id = str_replace(".webp", "", str_replace("/var/tmp//imagens_tela_principal_webp/","",$arquivo_origem));
            echo "\n".$k." - ".$arquivo_origem;
            
            
            $imagem                     = Imagens::find()->andWhere(['=', 'produto_id', $produto_id])->one();
            $imagem->imagem             = base64_encode(file_get_contents($arquivo_origem));
            $imagem->imagem_sem_logo    = base64_encode(file_get_contents(str_replace(".webp", "-sem-logo.webp", $arquivo_origem)));
            //$imagem->ordem              = 7;
            var_dump($imagem->save());
            //rename($arquivo_origem, $arquivo_destino);
        }
        
        echo "\n\nFIM da rotina de renomeação de arquivos!";
    }
}
