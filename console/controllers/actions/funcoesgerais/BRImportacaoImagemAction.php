<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;

class BRImportacaoImagemAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação de imagens da BR: \n\n";

         if (file_exists("/var/tmp/log_br_importar_imagem.csv")){
            unlink("/var/tmp/log_importar_imagem.csv");
        }

        //Escreve no log
        $arquivo_log = fopen("/var/tmp/log_importar_imagem_br.csv", "a");
        fwrite($arquivo_log, "arquivo;status\n");

	$path = "/var/tmp/br_fotos_subir_23-04-2020_webp/com_logo/";
	//$path = "/var/tmp/imagens_br_editado/com-logo/";
        //$path = "/var/tmp/BR/com_logo/";
        //$path = "/var/tmp/BR/br_442X367_com_logo/";
        //$path = "/var/tmp/BR/br_442X367_com_logo_2/";
        //$path = "/var/tmp/BR/br_442X367_com_logo_3/";

	$x = 0;

        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){

            if($arquivo == "." || $arquivo == ".."){
                continue;
            }

            fwrite($arquivo_log, $arquivo);

            $caminhoImagemComLogo       = $path.$arquivo;
            $caminhoImagemSemLogo       = "/var/tmp/br_fotos_subir_23-04-2020_webp/sem_logo/".$arquivo;
	    //$caminhoImagemSemLogo       = "/var/tmp/imagens_br_editado/sem-logo/".$arquivo;
            //$caminhoImagemSemLogo       = "/var/tmp/BR/br_442X367_sem_logo/".str_replace(" cópia","",$arquivo);
            //$caminhoImagemSemLogo       = "/var/tmp/BR/br_442X367_sem_logo_2/".str_replace(" cópia","",$arquivo);
            //$caminhoImagemSemLogo       = "/var/tmp/BR/br_442X367_sem_logo_3/".str_replace(" cópia","",$arquivo);

            $codigo_global              = str_replace(".webp","",$arquivo);
	    //$codigo_fabricante              = str_replace(".webp","",$arquivo);
            echo "\n".$x++." - ";
	    print_r($codigo_global);

	    $produto_filiais            = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                                //->where(" filial_id = 72 and (codigo_global like '".$codigo_global."' or codigo_global like '".$codigo_global.".')")
								//->where(" filial_id = 72 and codigo_fabricante like '".$codigo_fabricante.".B'")
								->where(" filial_id = 72 and codigo_global like '".$codigo_global."%' ")
                                                                ->all();

	    echo " - ".$arquivo;
            if ($produto_filiais){
  		    foreach($produto_filiais as $k => $produto_filial){
			    echo " - PRODUTO ENCONTRADO(".$produto_filial->id.")";

	                    $imagem = Imagens::find()->andWhere(['=','id', $produto_filial->produto_id])->one();
	                    if($imagem){
	                        echo " - Produto ja possui imagens";
	                    }

	                    //continue;
	                    $imagem                     = new Imagens;
	                    $imagem->produto_id         = $produto_filial->produto_id;
	                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
	                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagemSemLogo));
	                    $imagem->ordem              = 1;
	                    echo "==>";var_dump($imagem->save());echo "<==";
	            }
            }
            else {
                    echo " - PRODUTO NAO ENCONTRADO";
            }
	}
        $diretorio -> close();

        echo "\n\n quantidade não encontrada = ".$quantidade_nao_encontrado;

        fclose($arquivo_log);

        echo "\n\nFIM da rotina de importação de imagens da Universal!";

        //Inicio Segunda maneira

        /*echo "INÍCIO da rotina de criação preço: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/PRODUTOS_BR_QUE_NAO_ESTAO_NO_SITE_20-05-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

       foreach ($LinhasArray as $k => &$linhaArray ){

            echo $k." - ";

            if($k==0){
                continue;
            }

            $produto_filial = ProdutoFilial::find() ->joinWith('produto', true, 'INNER JOIN')
                                                    ->where("filial_id = 72 and (codigo_fabricante like '".$linhaArray[1]."' or codigo_fabricante like '0".$linhaArray[1]."')")
                                                    ->one();
            echo " - ".$linhaArray[0]." - ";
            if($produto_filial){
                echo "Produto ENCONTRADO";
                $caminhoImagemComLogo   = "/var/tmp/BR/com_logo/".$linhaArray[0].".jpg";
                $caminhoImagemSemLogo   = "/var/tmp/BR/sem_logo/".$linhaArray[0].".jpg";

                if (file_exists($caminhoImagemComLogo)) {
                    echo $caminhoImagemComLogo." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produto_filial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    $imagem->save();
                }
                else{
                    echo " - NÃO EXISTE \n";
                }
            }
            else{
                echo "Produto NÃO ENCONTRADO";
            }
        }*/

        echo "\n\nFIM da rotina de criação preço!";

    }
}
