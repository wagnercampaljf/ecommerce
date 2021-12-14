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

class MorelateCriarProdutosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();
        //$file = fopen("/var/tmp/br_produtos_ausentes_04-02-2020_completa -mari- atualizada.csv", 'r'); //Abre arquivo com preços para subir

        $file = fopen("/var/www/html/backend/web/uploads/morelatti_fora_da_base_atualizado.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);


        // if (file_exists("/var/tmp/Produtos_novos_morelate".date("Y-m-d_H-i-s").".csv","a")){
        //     unlink("/var/tmp/log_produtos_marts_parts_subir_precificado.csv");
        // }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/Produtos_novos_morelate".date("Y-m-d_H-i-s").".csv","a");
        //Escreve no log

        // var_dump($LinhasArray);
        // die;

        foreach ($LinhasArray as $i => &$linhaArray){


            if($i < 917){
                continue;
            }


            $codigo_fabricante                  = $linhaArray[0];
            $codigo_fornecedor                  = $linhaArray[2];
            $codigo_global                      = $linhaArray[1];
            $nome                               = $linhaArray[3]." ".$linhaArray[7];            
            $descricao                          = $linhaArray[3];
            $aplicacao                          = $linhaArray[3]." ".$linhaArray[7];
            $estoque                            = $linhaArray[4];

            $nome                               = utf8_encode($nome);
            $aplicacao                          = utf8_encode($aplicacao);
            $descricao                          = utf8_encode($descricao);

            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante.'";"'.$codigo_fornecedor.'";"'.$codigo_global.'";"'.$nome.'";"'.$descricao.'";"'.$estoque.'";"'.$aplicacao.'";"');



            if ($i <= 2){
                fwrite($arquivo_log, "status");
                continue;
            }

            echo "\n".$i." - ".$codigo_fabricante."\n";



            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $codigo_fabricante])
                ->andWhere(['=','fabricante_id', 72])
                ->one();




            if (!$produto){ //


                //if($linhaArray[14]==""){continue;}

                $produto_novo = new Produto;

               // $subcategoria = Subcategoria::find()->andWhere(['=','meli_id',446])->one();
                /*if(!$subcategoria){
                    $subcategoria                   = new Subcategoria;
                    $subcategoria->nome             = $linhaArray[15];
                    $subcategoria->categoria_id     = 10;
                    $subcategoria->ativo            = true;
                    $subcategoria->meli_id          = $linhaArray[14];
                    $subcategoria->meli_cat_nome   = $linhaArray[15];
                    $this->slugify_subcategoria($subcategoria);
                    if($subcategoria->save()){
                        echo " - Subcategoria criada";
                    }
                    else{
                        echo " - Subcategoria não criada";
                    }
                }*/

                $produto_novo->codigo_global        = $codigo_global;
                $produto_novo->codigo_fabricante    = $codigo_fabricante;
                $produto_novo->nome                 = strtoupper ( substr($nome,0,150 ) ) ;
                /* if ($linhaArray[14] == null) {
                 $linhaArray[14]= 5;
             }else{
                 $produto_novo->peso                 = (float) str_replace(",",".",$linhaArray[14]);
             }


             if ($linhaArray[11] == null) {
                 $linhaArray[11]= 30;
             }else{
                 $produto_novo->altura               = $linhaArray[11];
             }


             if ($linhaArray[12] == null) {
                 $linhaArray[12]= 30;
             }else{
                 $produto_novo->largura              = $linhaArray[12];
             }



             if ($linhaArray[13] == null) {
                 $linhaArray[13]= 30;
             }else{
                 $produto_novo->profundidade         = $linhaArray[13];
             }*/
                $produto_novo->peso                 = 1;
                $produto_novo->altura               = 30;
                $produto_novo->largura              = 30;
                $produto_novo->profundidade         = 30;
                $produto_novo->subcategoria_id      = 446;
                $produto_novo->produto_condicao_id  = 1;
                //$produto_novo->marca_produto_id     = 396;
                $produto_novo->aplicacao            = $aplicacao;
                $produto_novo->codigo_similar       = $codigo_fornecedor;
                //$produto_novo->ipi                  = $linhaArray[7];
                $produto_novo->codigo_montadora     = $codigo_fornecedor;
               // $produto_novo->codigo_barras        = $linhaArray[1];
                $produto_novo->descricao            = $descricao;
                $produto_novo->fabricante_id        = 130;
                $this->slugify($produto_novo);

                //print_r($produto_novo);
                if($produto_novo->save()){
                    echo " - produto_criado";
                    fwrite($arquivo_log, " - produto_criado");
                }
                else{
                    echo " - produto_nao_criado";
                    $produto_novo->codigo_global .= ".";
                    if($produto_novo->save()){
                        echo " - produto_criado";
                        fwrite($arquivo_log, " - produto_criado");
                    }
                    else{
                        echo " - produto_nao_criado";
                        $produto_novo->codigo_global .= ";";

                        fwrite($arquivo_log, " - produto_nao_criado");
                    }
                }


                $preco_compra = $linhaArray[5];

                $preco_venda = "";

                $quantidade = $estoque;

                /*if($quantidade == 0){
                    $quantidade = 90;
                    $preco_venda = $preco_venda * 1.1;
                }
                else{
                    $quantidade = 781;
                }

                if($linhaArray[6] == "0.00" || $linhaArray[6] == "0" || $linhaArray[6] == "0,00"){
                    $quantidade = 0;
                }*/

                echo " - Quantidade: ".$quantidade;
                $produto_filial_novo    = new ProdutoFilial;
                $produto_filial_novo->produto_id                    = $produto_novo->id;
                $produto_filial_novo->filial_id                     = 43;
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







