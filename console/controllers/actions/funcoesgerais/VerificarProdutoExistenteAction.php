<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class VerificarProdutoExistenteAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";


        $codigos_produtos = array();

        $LinhasArray = Array();

        $file = fopen("/var/tmp/produtos_morelate_11-03-2021.csv", 'r');



        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
            //Cria um array com dados da planilha e indice sendo o codigo_fabricante
            $codigos_produtos[$line[0]] = $line[0];

        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_produtos_morelate_11-03-2021.csv")){
            unlink("/var/tmp/log_produtos_morelate_11-03-2021.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_produtos_morelate_11-03-2021.csv", "a");
        //Escreve no log

        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];

            // if($i<27944){continue;}

            $preco_compra = $linhaArray[2];

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9].';');

            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])->one();

            if ($produto){

                echo  " - Produto encontrado - ".$produto->id." - MarcaID: ".$produto->marca_produto_id;

                fwrite($arquivo_log, 'Produto encotrado');



                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])

                    ->andWhere(['=','filial_id',43])

                    ->one();

                if($produto_filial){

                    echo " - Estoque  encontrado";

                    fwrite($arquivo_log, ' - Estoque  encontrado');
                }

                else{

                    echo " - Estoque não encontrado";

                    fwrite($arquivo_log, ' - Estoque não encontrado');

                }

            }

            else{

                echo ' - Produto não encontrado';

                fwrite($arquivo_log, 'Produto não encontrado');

            }

        }



        // Fecha o arquivo log
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de atualizacao do preço!";

    }
}








