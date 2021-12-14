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

class LanternasCriarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();


        $file = fopen("/var/tmp/produtos_marca_pxa-19-03-2021.csv", 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_produtos_marca_pxa-19-03-2021.csv")){
            unlink("/var/tmp/log_produtos_marca_pxa-19-03-2021.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_produtos_marca_pxa-19-03-2021.csv", "a");

        foreach ($LinhasArray as $i => &$linhaArray){

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";');
            
            if ($i <= 2){
                fwrite($arquivo_log, "status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[1]." - ".$linhaArray[0]." - ".$linhaArray[7]." - ".$linhaArray[8];

            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[1]])
                                        ->andWhere(['=','fabricante_id', 123])
                                        ->one(); //Procura produto pelo código do fabricante "VA"



            if (!$produto){ //

                //print_r($produto);

                //if($linhaArray[14]==""){continue;}
                
                $produto_novo = new Produto;
                
                //$subcategoria = Subcategoria::find()->andWhere(['=','meli_id',1957])->one();

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

                $produto_novo->codigo_global        = $linhaArray[0];
                $produto_novo->codigo_fabricante    = $linhaArray[1];
                $produto_novo->nome                 = $linhaArray[2] ;
                $produto_novo->peso                 = 0.04;
                $produto_novo->altura               = 2;
                $produto_novo->largura              = 10;
                $produto_novo->profundidade         = 10;
                $produto_novo->subcategoria_id      = 1957;

                //$produto_novo->subcategoria_id      = 1977;

                $produto_novo->produto_condicao_id  = 1;
                $produto_novo->aplicacao            = $linhaArray[3];
                $produto_novo->multiplicador        = $linhaArray[8];
               $produto_novo->marca_produto_id     = 1167;



                //$produto_novo->codigo_montadora     = $linhaArray[6];
                //$produto_novo->codigo_barras        = $linhaArray[12];
                $produto_novo->descricao            = $linhaArray[2];
                $produto_novo->fabricante_id        = 123;
                $this->slugify($produto_novo);
                if($produto_novo->save()){
                    echo " - produto_criado";
                    fwrite($arquivo_log, " - produto_criado");
                }

                //print_r($produto_novo);
                /*if($produto_novo->save()){
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
                }*/
                
                   
                $preco_venda = $linhaArray[5];
                $preco_compra = $linhaArray[6];
                
                $quantidade = $linhaArray[7];


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








