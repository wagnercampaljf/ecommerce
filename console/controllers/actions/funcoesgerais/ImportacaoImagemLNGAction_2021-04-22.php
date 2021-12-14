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

        //$path = "/var/tmp/lng_foto/fotos_com_logo/";
	$path = "/var/tmp/fotos_lng_12-12-2019/com-logo/";
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){

            if($arquivo == "." || $arquivo == ".."){
                continue;
            }

            $caminhoImagemComLogo       = $path.$arquivo;
            //$caminhoImagemSemLogo       = "/var/tmp/lng_foto/fotos_sem_logo/".$arquivo;
            //$codigo_fabricante          = "L".str_replace("-","", str_replace(".jpg","",$arquivo));
	    $caminhoImagemSemLogo       = "/var/tmp/fotos_lng_12-12-2019/sem-logo/".$arquivo;
            $codigo_fabricante          = "L".str_replace("-","", str_replace(".webp","",$arquivo));
            $produto_filial             = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                          ->andWhere(['=','codigo_fabricante',$codigo_fabricante])
                                                          ->andWhere(['=','filial_id',60])
                                                          ->one();
            //echo "\n".$caminhoImagemComLogo;
           // echo "\n".$caminhoImagemSemLogo;
            echo "\n".$codigo_fabricante;
            if (isset($produto_filial)){
		$imagem_teste = Imagens::find()->andWhere(['=','produto_id',$produto_filial->produto_id])->one();
                echo "\n".$produto_filial->id." - ".$produto_filial->produto->id;

                if($imagem_teste){
                   echo " - produto com imagem cadastrada";
                }
                else{
                    echo " - produto SEM imagem cadastrada";

                    $imagem                     = new Imagens;
                    $imagem->produto_id         = $produto_filial->produto_id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
                    $imagem->ordem              = 1;
                    echo " == ";var_dump($imagem->save());
                }
            }
        }

        $diretorio -> close();

        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}
