<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportacaoImagemNovas2Action extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da BR: \n\n";
        
        $path = "/var/www/bago_2_com_logo/";
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()) {

            if ($arquivo == "." || $arquivo == "..") {
                continue;
            }

            //if($arquivo == "005783.webp"){
            /*if($arquivo == "096598.webp"){
                continue;
            }*/

            $caminhoImagemComLogo = $path . $arquivo;

            $caminhoImagemSemLogo = "/var/www/bago_2_sem_logo/" . $arquivo;



            $codigo_fabricante   = str_replace("_2","",str_replace(".webp","",$arquivo));



            $produto_filial = ProdutoFilial::find()->joinWith('produto', true, 'INNER JOIN')
                ->andWhere(['=', 'codigo_fabricante', $codigo_fabricante])
                ->andWhere(['=', 'filial_id', 87])
                ->one();

            //if ($produto_filial) {

            echo "\n" . $caminhoImagemComLogo;
            echo "\n" . $caminhoImagemSemLogo;
            echo "\n" . $codigo_fabricante;

            if (isset($produto_filial)) {


                //$caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";


                //if (file_exists($caminhoImagemComLogoNovo)) {
                   // echo " - produto ja com imagem \n ";
                //} else {
                    echo $caminhoImagemComLogo . " - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id = $produto_filial->produto->id;
                    $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo));
                    $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem = 2;
                var_dump($imagem->save());

                //}
            }
            //}
           // }
        }
        
        $diretorio -> close();
        
        echo "\n\nFIM da rotina de importação de imagens da Universal!";
    }
}