<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\Subcategoria;
use common\models\MarcaProduto;
use common\models\Imagens;

class ForrosCriarProdutosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();

        $file = fopen("/var/tmp/EMBLEMAS_emate2011_2021-08-03.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        if (file_exists("/var/tmp/log_EMBLEMAS_emate2011_2021-08-03.csv")){
            unlink("/var/tmp/log_EMBLEMAS_emate2011_2021-08-03.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_EMBLEMAS_emate2011_2021-08-03.csv", "a");
        //Escreve no log

        foreach ($LinhasArray as $i => &$linhaArray){


            //fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";');



            if ($i <= 1){
                fwrite($arquivo_log, "status");
                continue;
            }

            echo "\n".$i." - ".$linhaArray[2]." - ".$linhaArray[0]." - ".$linhaArray[6]." - ".$linhaArray[7];



            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', 'D.'.$linhaArray[1]])
                                        ->one();

            if (!$produto){ //

                $produto_novo = new Produto;




                $produto_novo->codigo_global        = 'D.'.$linhaArray[0];
                $produto_novo->codigo_fabricante    = $linhaArray[1];
                $produto_novo->nome                 = strtoupper ( $linhaArray[2].' 2012 EM DIANTE' );
               
                $produto_novo->peso                 = 1;
                $produto_novo->altura               = 30;
                $produto_novo->largura              = 30;
                $produto_novo->profundidade         = 30;
                $produto_novo->subcategoria_id      = 803;
                $produto_novo->marca_produto_id     = 1170;
                $produto_novo->produto_condicao_id  = 1;
                $produto_novo->aplicacao            = $linhaArray[2];
               // $produto_novo->codigo_similar       = $linhaArray[2];
                //$produto_novo->codigo_montadora     = $linhaArray[5];
                $produto_novo->descricao            = $linhaArray[2];
                $produto_novo->fabricante_id        = 33;
                $this->slugify($produto_novo);


               // print_r($produto_novo);die;

                //print_r($produto_novo);
                if($produto_novo->save()){
                    echo " - produto_criado";
                    fwrite($arquivo_log, " - produto_criado");
                }

                $preco_compra = 0;

                $preco_venda = $linhaArray[5];

                $quantidade = 9999;


                echo " - Quantidade: ".$quantidade;
                $produto_filial_novo    = new ProdutoFilial;
                $produto_filial_novo->produto_id                    = $produto_novo->id;
                $produto_filial_novo->filial_id                     = 8;
                $produto_filial_novo->quantidade                    = $quantidade;
                $produto_filial_novo->envio                         = 1;
                $produto_filial_novo->atualizar_preco_mercado_livre = true;
                if($produto_filial_novo->save()){
                    echo " - estoque_criado";
                    fwrite($arquivo_log, " - estoque_criado");
                }
                else{
                    // print_r($produto_filial_novo);
                    echo " - estoque_nao_criado";
                    fwrite($arquivo_log, " - estoque_nao_criado");
                }

                $valor_produto_filial = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produto_filial_novo->id;
                $valor_produto_filial->valor                = $preco_venda;
                $valor_produto_filial->valor_cnpj           = $preco_venda;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->promocao             = false;
                $valor_produto_filial->valor_compra         = $preco_compra;
                if($valor_produto_filial->save()){
                    echo " - Preço criado";
                    fwrite($arquivo_log, ' - Preço criado');
                }
                else{
                    echo " - preco_nao_criado";
                    fwrite($arquivo_log, ' - preco_nao_criado');
                }
            }
            else{
                echo " - Produto Já criado";
                fwrite($arquivo_log, 'Produto Não encontrado');
            }
        }


        // Fecha o arquivo log
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de atualizacao do preço!";
    }

    private function slugify(&$produto_slugfy)
    {
        $text = $produto_slugfy->nome . ' ' . $produto_slugfy->codigo_global;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        $produto_slugfy->slug = $text;
    }

    private function slugify_subcategoria(&$subcategoria)
    {
        $text = $subcategoria->nome;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        $subcategoria->slug = $text;
    }
}







