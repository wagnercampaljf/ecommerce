<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;

class EustaquioAtualizaPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";


        $LinhasArray = Array();




        $file = fopen("/var/tmp/produtos_eustaquio-26-03-2020.csv", 'r');





        ///////////////////////////////////////////////////////
        //ARQUIVO COM DADOS
        ///////////////////////////////////////////////////////

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        $log = "/var/tmp/log_produtos_eustaquio-26-03-2020.csv";
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");

        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2];

            // if($i<28582){continue;}

            $preco_compra = $linhaArray[5];

            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';');

            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])->one();

            if ($produto){

                echo  " - Produto encontrado - ".$produto->id." - MarcaID: ".$produto->marca_produto_id;
                fwrite($arquivo_log, 'Produto encotrado');

                $preco_venda = $linhaArray[4];

               // $preco_venda= $preco_venda * 1.20;



                echo " - Preço venda: ".$preco_venda;

                fwrite($arquivo_log, ";".$preco_venda.';');

                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$produto->id])
                    ->andWhere(['=','filial_id',42])
                    ->one();


                if($produto_filial){

                    $produto_filial->quantidade = 0;
                    if($produto_filial->save()){
                        echo  " - Estoque alterado";
                        fwrite($arquivo_log, ' - Estoque alterado');
                    }
                    else{
                        echo " - Estoque não alterado";
                        fwrite($arquivo_log, ' - Estoque não alterado');
                    }

                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();
                    if($preco_venda == $valor_produto_filial->valor){
                        echo " - mesmo valor";
                        continue;
                    }

                    $valor_produto_filial                       = new ValorProdutoFilial();
                    $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->valor_compra         = $preco_compra;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    if($valor_produto_filial->save()){
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ' - Preço alterado');
                    }
                    else{
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ' - Preço não alterado');
                    }
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
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }



}









