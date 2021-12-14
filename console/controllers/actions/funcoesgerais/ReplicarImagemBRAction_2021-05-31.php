<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ReplicarImagemBRAction extends Action
{
    public function run(){

        //Inicio Segunda maneira

        echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/replicar_imagens_gauss.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $k => &$linhaArray ){

            echo $k." - ";

            if($k<0){
                continue;

            }

            $codigo_fabricante   = str_replace("CX.","",str_replace("-10","",str_replace("-20","",str_replace("-30","",str_replace("-40","",str_replace("-50","",str_replace("-60","",str_replace("-70","",str_replace("-80","",str_replace("-90","",str_replace("-100","",str_replace("-150","",str_replace("-12","",$linhaArray[1])))))))))))));


            $id_produto = $linhaArray[2];


            $produto_filial = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                ->where("filial_id = 8 and (codigo_fabricante like '".$codigo_fabricante."-100"."' or codigo_fabricante like '0".$codigo_fabricante."-100"."')") ->orderBy("produto.id ASC")
                ->one();

            echo " - ".$codigo_fabricante." - ";
            if($produto_filial){

                // print_r($produto_filial);

                //if ($produto_filial->quantidade >=1) {

                echo "Produto ENCONTRADO ";
                $caminhoImagemComLogo = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_1.webp";
                $caminhoImagemSemLogo = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_1.webp";


                $caminhoImagemComLogo1 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_2.webp";
                $caminhoImagemSemLogo1 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_2.webp";


                $caminhoImagemComLogo2 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_3.webp";
                $caminhoImagemSemLogo2 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_3.webp";

                $caminhoImagemComLogo3 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_4.webp";
                $caminhoImagemSemLogo3 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_4.webp";


                $caminhoImagemComLogo4 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_5.webp";
                $caminhoImagemSemLogo4 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_5.webp";


                $caminhoImagemComLogo5 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_6.webp";
                $caminhoImagemSemLogo5 = "/var/www/imagens_produto/produto_" . $id_produto . "/" . $id_produto . "_6.webp";


                if (file_exists($caminhoImagemComLogo)) {

                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                        $imagem->ordem = 1;
                        $imagem->save();

                    }


                }
                elseif ((file_exists($caminhoImagemComLogo1))) {
                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo1 . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo1));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo1) ? base64_encode(file_get_contents($caminhoImagemSemLogo1)) : null);
                        $imagem->ordem = 2;
                        $imagem->save();
                    }


                }
                elseif ((file_exists($caminhoImagemComLogo2))) {
                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo2 . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo2));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo2) ? base64_encode(file_get_contents($caminhoImagemSemLogo2)) : null);
                        $imagem->ordem = 3;
                        $imagem->save();
                    }


                }
                elseif ((file_exists($caminhoImagemComLogo3))) {
                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo2 . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo3));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo3) ? base64_encode(file_get_contents($caminhoImagemSemLogo3)) : null);
                        $imagem->ordem = 4;
                        $imagem->save();
                    }


                }
                elseif ((file_exists($caminhoImagemComLogo4))) {
                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo4 . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo4));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo4) ? base64_encode(file_get_contents($caminhoImagemSemLogo4)) : null);
                        $imagem->ordem = 5;
                        $imagem->save();
                    }


                }
                elseif ((file_exists($caminhoImagemComLogo5))) {
                    $caminhoImagemComLogoNovo = "/var/www/imagens_produto/produto_" . $produto_filial->produto->id . "/" . $produto_filial->produto->id . "_1.webp";

                    if (file_exists($caminhoImagemComLogoNovo)) {
                        echo " - produto ja com imagem \n ";
                    } else {
                        echo $caminhoImagemComLogo5 . " - EXISTE\n";
                        $imagem = new Imagens();
                        $imagem->produto_id = $produto_filial->produto->id;
                        $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo5));
                        $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo5) ? base64_encode(file_get_contents($caminhoImagemSemLogo5)) : null);
                        $imagem->ordem = 6;
                        $imagem->save();
                    }


                }
                else {
                    echo " - NÃO EXISTE \n";
                }
                //}
            }
            else{
                echo "Produto NÃO ENCONTRADO";
            }
        }

        echo "\n\nFIM da rotina de criação preço!";

    }
}