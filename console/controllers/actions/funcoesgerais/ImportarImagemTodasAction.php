<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class ImportarImagemTodasAction extends Action
{
    public function run(){
        
        //Inicio Segunda maneira
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/morelate_max_parts_19_05_2021.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
       foreach ($LinhasArray as $k => &$linhaArray ) {

           echo $k . " - ";

           if ($k < 0) {
               continue;
           }
           while($arquivo = $diretorio -> read()) {


               if ($arquivo == "." || $arquivo == "..") {
                   continue;
               }
               $codigo_fabricante_planilha = $linhaArray[0];

               $codigo_fabricante = str_replace(".webp", "", $arquivo) . ".M";

               $produto_filial = ProdutoFilial::find()->joinWith('produto', true, 'INNER JOIN')
                   ->where("filial_id = 72 and (codigo_fabricante like '" . $codigo_fabricante . "' or codigo_fabricante like '0" . $codigo_fabricante . "')")->orderBy("produto.id ASC")
                   ->one();


               echo " - " . $codigo_fabricante . " - ";
           if ($produto_filial) {



               echo "Produto ENCONTRADO ";
               $caminhoImagemComLogo = $path . $arquivo;

               $caminhoImagemSemLogo = "/var/www/morelate_imagens/" . $arquivo;


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

               } elseif ($codigo_fabricante_planilha == $codigo_fabricante){
                   echo $caminhoImagemComLogo . " - EXISTE\n";
                   $imagem = new Imagens();
                   $imagem->produto_id = $produto_filial->produto->id;
                   $imagem->imagem = base64_encode(file_get_contents($caminhoImagemComLogo));
                   $imagem->imagem_sem_logo = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                   $imagem->ordem = 1;
                   $imagem->save();
                   
               }

               else {
                   echo " - NÃO EXISTE \n";
               }

           } else {
               echo "Produto NÃO ENCONTRADO";
           }
       }
        }

        echo "\n\nFIM da rotina de criação preço!";
        
    }
}