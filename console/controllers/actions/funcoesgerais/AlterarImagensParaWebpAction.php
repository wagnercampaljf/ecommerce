<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;

class AlterarImagensParaWebpAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de alteração de imagens WEBP: \n\n";
        
        //Escreve no log
        $arquivo_log = fopen("/var/tmp/log_imagens_para_webp_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "arquivo;produto_id;imagem_id;ordem;possui_arquivo_sem_logo;imagem_encontrada;imagem_alterada");
        
        $path_com_logo = "/var/tmp/webp29/com_logo/";
        $path_sem_logo = "/var/tmp/webp29/sem_logo/";
        
        $x = 0;
        
        $diretorio = dir($path_com_logo);
        while($arquivo = $diretorio -> read()){
            
            if($arquivo == ".." || $arquivo == "."){
                continue;
            }
            
            echo "\n".$x++." - ";
            print_r($arquivo);
            fwrite($arquivo_log, "\n".$arquivo);
            
            $caminhoImagemComLogo       = $path_com_logo.$arquivo;
            $caminhoImagemSemLogo       = $path_sem_logo.$arquivo;
            
            if (file_exists($caminhoImagemSemLogo)){
                
                echo " - Possui imagem sem logo";
                
                $nome_arquivo   = str_replace(".webp","",$arquivo);
                $dados          = explode("_",$nome_arquivo);
                $produto_id     = $dados[0];
                $ordem          = $dados[1];
                $imagem_id      = $dados[2];
                
                fwrite($arquivo_log, ";".$produto_id.";".$imagem_id.";".$ordem.";Sim");
                
                $imagem = Imagens::find()->andWhere(["=","id",$imagem_id])->one();
                //$imagem = Imagens::find()->andWhere(["=","produto_id",$produto_id])->andWhere(["=","ordem",$ordem])->one();
                                
                if($imagem){
                    echo " - Imagem encontrada";
                    fwrite($arquivo_log, ";Sim");
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
                    if($imagem->save()){
                        fwrite($arquivo_log, ";Sim");
                    }
                    else{
                        fwrite($arquivo_log, ";Não");
                    }
                }
                else{
                    echo " - Imagem não encontrada";
                    fwrite($arquivo_log, ";Não");
                }
            }
            else{
                echo " - Sem imagem sem logo";
                fwrite($arquivo_log, ";Não");
            }
        }
        $diretorio -> close();
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de alteração de imagens WEBP!";
    }
}
