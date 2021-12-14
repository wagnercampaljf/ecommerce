<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use common\models\Filial;
use common\models\Imagens;

class InseririmagensAction extends Action
{
    public function run(){
        echo Yii::getAlias('@yii')." \n ";
        
        $filial     = Filial::find()->andWhere(['=','id',60])->one();
        
        $produtoFilials = $filial->getProdutoFilials()->orderBy(['id'=>SORT_ASC])->all();
        
        //$produtos = Produto::find()->andwhere(['>','id',9100])->orderBy('id')->all();
        echo "INÍCIO!\n\n";
        $x = 0;
        foreach ($produtoFilials as $produtoFilial){
            $quantidadeCaracteres = strlen(str_replace("L","",$produtoFilial->produto->codigo_fabricante));
            $caminhoImagem          = "";
            $caminhoImagemSemLogo   = "";
            switch ($quantidadeCaracteres) {
                case 5:
                    $caminhoImagem          = "/var/data/pecaagora/fotos_lng/teste_lote_destino/".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),0,2)."-".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),-3)."logojpg.";
                    $caminhoImagemSemLogo   = "/var/data/pecaagora/fotos_lng/fotos_peca_agora/".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),0,2)."-".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),-3).".";
                    break;
                case 6:
                    $caminhoImagem          = "/var/data/pecaagora/fotos_lng/teste_lote_destino/".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),0,2)."-".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),-4)."logojpg.";
                    $caminhoImagemSemLogo   = "/var/data/pecaagora/fotos_lng/fotos_peca_agora/".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),0,2)."-".substr(str_replace("L","",$produtoFilial->produto->codigo_fabricante),-4).".";
                    break;
                default:
                    $caminhoImagem          = "/var/data/pecaagora/fotos_lng/teste_lote_destino/".str_replace("L","",$produtoFilial->produto->codigo_fabricante)."logojpg.";
                    $caminhoImagemSemLogo   = "/var/data/pecaagora/fotos_lng/fotos_peca_agora/".str_replace("L","",$produtoFilial->produto->codigo_fabricante).".";
                    break;
            }
            //echo $x." - ".$quantidadeCaracteres." - ".$produtoFilial->produto->id." - ".$caminhoImagemSemLogo."\n";
            if (file_exists($caminhoImagem."jpg")) {
                $caminhoImagem  = $caminhoImagem."jpg";
            }
            elseif (file_exists($caminhoImagem."JPG")){
                 $caminhoImagem  = $caminhoImagem."JPG";
            }
            else{
                continue;
            }
            
            if (file_exists($caminhoImagemSemLogo."jpg")) {
                $caminhoImagemSemLogo   = $caminhoImagemSemLogo."jpg";
            }
            elseif (file_exists($caminhoImagemSemLogo."JPG")){
                $caminhoImagemSemLogo   = $caminhoImagemSemLogo."JPG";
            }
            else{
                continue;
            }
            
            echo "\n".$x." - ".$quantidadeCaracteres." - ".$produtoFilial->produto->id." - ".$caminhoImagem." = ";
            $imagem = new Imagens();
            $imagem->produto_id         = $produtoFilial->produto->id;
            $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
            $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
            $imagem->ordem              = 1;
            var_dump($imagem->save());
            $x++;
        }
        echo "\n\nFIM!";
    }
}
