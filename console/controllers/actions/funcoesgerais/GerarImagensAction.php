<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\Imagens;
use common\models\Produto;

class GerarImagensAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de geração de imagens: \n\n";

        /*$produto_filiais = ProdutoFilial::find()->andWhere(['=','filial_id',96])->all();

        foreach ($produto_filiais as $k => &$produto_filial){
	    echo "\n".$k." - ".$produto_filial->id;
            if($produto_filial->produto->fabricante_id == 109){
                echo " - Universal";
                $imagens = Imagens::find()->andWhere(['=','produto_id',$produto_filial->produto_id])->all();
                foreach ($imagens as $imagem){
                    echo " - ".$imagem->ordem;
                    copy("https://www.pecaagora.com/site/get-link?produto_id=" . $produto_filial->produto_id . "&ordem=".$imagem->ordem, '/var/tmp/ImagensUniversalGeradas/com_logo/'.$produto_filial->produto->id."_".$imagem->ordem.".jpg" );
                    copy("https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $produto_filial->produto_id . "&ordem=".$imagem->ordem, '/var/tmp/ImagensUniversalGeradas/sem_logo/'.$produto_filial->produto->id."_".$imagem->ordem.".jpg" );
                }
            }

            //if($k == 10){ break; }
        }*/

	$produtos = Produto::find()	->andWhere(['=','id',5002])
					->orderBy('id')
					->all();
        foreach ($produtos as $k => &$produto){
            echo "\n".$k." - ".$produto->id;
            $imagens = Imagens::find()->andWhere(['=','produto_id',$produto->id])->all();
            foreach ($imagens as $imagem){
                echo " - ".$imagem->ordem;
                copy("https://www.pecaagora.com/site/get-link?produto_id=" . $produto->id . "&ordem=".$imagem->ordem, '/var/tmp/'.$produto->id."_".$imagem->ordem."_".$imagem->id.".jpg" );
                copy("https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $produto->id . "&ordem=".$imagem->ordem, '/var/tmp/'.$produto->id."_".$imagem->ordem."_".$imagem->id.".jpg" );
            }
	    die;

        }

        echo "\n\nFIM da rotina de geração de imagens!";
    }
}
