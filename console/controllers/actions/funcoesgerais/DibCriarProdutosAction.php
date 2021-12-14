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

class DibCriarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/br_produtos_ausentes_04-02-2020_completa -mari- atualizada.csv", 'r'); //Abre arquivo com preços para subir

        $file = fopen("/var/tmp/produtos_mastra_2021-07-09_precificado.csv", 'r'); //Abre arquivo com preços para subir

        while (($line = fgetcsv($file,null,';')) !== false)
        {

            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento

        }

        fclose($file);

        if (file_exists("/var/tmp/log_produtos_mastra_2021-07-09_precificado.csv")){
            unlink("/var/tmp/log_produtos_mastra_2021-07-09_precificado.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_produtos_mastra_2021-07-09_precificado.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir


            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"');


            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[4]." - ".$linhaArray[6]; //Exibe no console(Terminal) as informações dos preços durante o processamento

            //$codigo_global =  str_replace("","",$linhaArray[0]);

            //$codigo_global   = str_replace("P.","",str_replace("PA","",$linhaArray[0]));


            $produto = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[4]])
                                        //->andWhere(['=','fabricante_id', 72])
                                        ->one(); //Procura produto pelo código do fabricante "VA"

            if (!$produto){ //



                //$codigo_fabricante = str_replace("PA","",$linhaArray[1]);

                $produto_novo = new Produto;


               $codigo_global  = $linhaArray[4];

                $codigo_fabricante  = $linhaArray[0];

                $produto_novo->codigo_global        = $codigo_global;
                $produto_novo->codigo_fabricante    = $codigo_fabricante;
                $produto_novo->nome                 = strtoupper ( $linhaArray[1]);
                $produto_novo->peso                 = 5;
                $produto_novo->altura               = 30;
                $produto_novo->largura              = 30;
                $produto_novo->profundidade         = 30;
                $produto_novo->subcategoria_id      = 878;
                $produto_novo->produto_condicao_id  = 1;
                $produto_novo->multiplicador        = 1;
                $produto_novo->aplicacao            = $linhaArray[1];
                //$produto_novo->codigo_montadora     = $linhaArray[6];
                //$produto_novo->codigo_barras        = $linhaArray[12];
                $produto_novo->descricao            = $linhaArray[1];
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
                        $produto_novo->codigo_global .= ",";
                        if($produto_novo->save()){
                            echo " - produto_criado";
                            fwrite($arquivo_log, " - produto_criado");
                        }
                        else{
                            echo " - produto_nao_criado";
                            $produto_novo->codigo_global .= "_";
                            if($produto_novo->save()){
                                echo " - produto_criado";
                                fwrite($arquivo_log, " - produto_criado");
                            }
                            else{
                                echo " - produto_nao_criado";
                                $produto_novo->codigo_global .= "|";
                                if($produto_novo->save()){
                                    echo " - produto_criado";
                                    fwrite($arquivo_log, " - produto_criado");
                                }
                                else{
                                    echo " - produto_nao_criado";
                                    $produto_novo->codigo_global .= "-";
                                    if($produto_novo->save()){
                                        echo " - produto_criado";
                                        fwrite($arquivo_log, " - produto_criado");
                                    }
                                    else{
                                        echo " - produto_nao_criado";
                                        $produto_novo->codigo_global .= "*";
                                        if($produto_novo->save()){
                                            echo " - produto_criado";
                                            fwrite($arquivo_log, " - produto_criado");
                                        }
                                        else{
                                            echo " - produto_nao_criado";
                                            $produto_novo->codigo_global .= ")";
                                            if($produto_novo->save()){
                                                echo " - produto_criado";
                                                fwrite($arquivo_log, " - produto_criado");
                                            }
                                            else{
                                                echo " - produto_nao_criado";
                                                $produto_novo->codigo_global .= "]";

                                                fwrite($arquivo_log, " - produto_nao_criado");
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                $preco_venda = $linhaArray[7];
                $preco_compra = $linhaArray[3];

                if ( $preco_compra<=200){
                    $preco_venda = $linhaArray[7] +30;
                }else{
                    $preco_venda = $linhaArray[7];
                }


                $quantidade = 999;


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

               // print_r($valor_produto_filial);


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







