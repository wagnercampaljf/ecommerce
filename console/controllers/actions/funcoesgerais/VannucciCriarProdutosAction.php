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

class VannucciCriarProdutosAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criar novos produtos: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/vannucci_novos_produtos-03-05-2021_precificado.csv", 'r'); //Abre arquivo com preços para subir

        $file = fopen('/var/www/html/backend/web/uploads/vannu_fora_da _base_2021-12-09.csv','r'); //Abre arquivo com preços para subir


        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);        

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_vannucci_novos_produtos_codFabricante".date("Y-m-d_H-i-s").".csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");

           
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir


            $codigo_fabricante     =  $linhaArray[0];
            $codigo_global         =  $linhaArray[1];
            $nome                  =  $linhaArray[2];
            $estoque               =  $linhaArray[3];



            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante.'";"'.$codigo_global.'";"'.$nome.'";"'.$estoque.'";');
            
            // if ($i <= 2){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
            //     fwrite($arquivo_log, ";status");
            //     continue;
            // }
           // if($i < 122){continue;}
            
            echo "\n".$i." - ".$codigo_fabricante." - ".$codigo_global." - ".$nome." - ".$estoque; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $codigo_fabricante])
                                        ->andWhere(['=','fabricante_id', 91])
                                        ->one(); //Procura produto pelo código do fabricante "VA"


            if (!$produto){ //Se encontrar o produto, processa o preço
                
                /*if($linhaArray[14]==""){
                    echo " - sem subcategoria recomendade ML";
                    fwrite($arquivo_log, "sem_categoria_recomendada");
                    continue;
                }*/
                
                $produto_novo = new Produto;
                
                /*$subcategoria = Subcategoria::find()->andWhere(['=','meli_id',$linhaArray[8]])->one();
                if(!$subcategoria){
                    $subcategoria                   = new Subcategoria;
                    $subcategoria->nome             = $linhaArray[9];
                    $subcategoria->categoria_id     = 10;
                    $subcategoria->ativo            = true;
                    $subcategoria->meli_id          = $linhaArray[8];
                    $subcategoria->meli_cat_nome   = $linhaArray[9];
                    $this->slugify_subcategoria($subcategoria);
                    if($subcategoria->save()){
                        echo " - Subcategoria criada";
                    }
                    else{
                        echo " - Subcategoria não criada";
                    }
                }*/
                
                $codigo_global                      = $codigo_global;
                $produto_novo->codigo_global        = $codigo_global;
                $produto_novo->codigo_fabricante    = $codigo_fabricante;
                $produto_novo->nome                 = strtoupper ( $nome);
                //$produto_novo->peso                 = 5;
                $produto_novo->peso                 = 2;
                $produto_novo->altura               = 30;
                $produto_novo->largura              = 30;
                $produto_novo->profundidade         = 30;
                $produto_novo->subcategoria_id      = 446;
                $produto_novo->produto_condicao_id   = 1;
                $produto_novo->aplicacao            = "";//$linhaArray[10];
                $produto_novo->codigo_montadora     = "";//$linhaArray[2];
                $produto_novo->codigo_barras        = "";//$linhaArray[3];
                $produto_novo->descricao            = $nome;
                $produto_novo->fabricante_id        = 91;
                $this->slugify($produto_novo);
               // print_r($produto_novo); var_dump($produto_novo->save()); die;
                if($produto_novo->save()){
                    echo " - produto_criado";
                    fwrite($arquivo_log, " - produto_criado");
                }
                else{
                    echo " - produto_nao_criado";
                    $produto_novo->codigo_global = $codigo_global.".";
                    if($produto_novo->save()){
                        echo " - produto_criado";
                        fwrite($arquivo_log, " - produto_criado");
                    }
                    else{
                        echo " - produto_nao_criado";
                        $produto_novo->codigo_global = $codigo_global.",";
                        if($produto_novo->save()){
                            echo " - produto_criado";
                            fwrite($arquivo_log, " - produto_criado");
                        }
                        else{
                            echo " - produto_nao_criado";
                            $produto_novo->codigo_global = $codigo_global. "_";
                            if($produto_novo->save()){
                                echo " - produto_criado";
                                fwrite($arquivo_log, " - produto_criado");
                            }
                            else{
                                echo " - produto_nao_criado";
                                $produto_novo->codigo_global = $codigo_global."|";
                                if($produto_novo->save()){
                                    echo " - produto_criado";
                                    fwrite($arquivo_log, " - produto_criado");
                                }
                                else{
                                    echo " - produto_nao_criado";
                                    $produto_novo->codigo_global = $codigo_global."-";
                                    if($produto_novo->save()){
                                        echo " - produto_criado";
                                        fwrite($arquivo_log, " - produto_criado");
                                    }
                                    else{
                                        echo " - produto_nao_criado";
                                        $produto_novo->codigo_global = $codigo_global."*";
                                        if($produto_novo->save()){
                                            echo " - produto_criado";
                                            fwrite($arquivo_log, " - produto_criado");
                                        }
                                        else{
                                            echo " - produto_nao_criado";
                                            $produto_novo->codigo_global = $codigo_global.")";
                                            if($produto_novo->save()){
                                                echo " - produto_criado";
                                                fwrite($arquivo_log, " - produto_criado");
                                            }
                                            else{
                                                echo " - produto_não_criado";
                                                fwrite($arquivo_log, " - produto_nao_criado");
                                                continue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }                    
                }
                
                /*$path_sem_logo = "/var/tmp/vannucci_produto_ausentes_16-04-2020/sem-logo/";
                $path_com_logo = "/var/tmp/vannucci_produto_ausentes_16-04-2020/com-logo/";
                
                if (file_exists($path_sem_logo.$produto_novo->codigo_fabricante.".webp")){
                    echo " - imagem encontrada";
                    
                    $imagem                     = new Imagens;
                    $imagem->produto_id         = $produto_novo->id;
                    $imagem->imagem             = base64_encode(file_get_contents($path_sem_logo.$produto_novo->codigo_fabricante.".webp"));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($path_com_logo.$produto_novo->codigo_fabricante.".webp"));
                    $imagem->ordem              = 1;
                    if($imagem->save()){
                        echo " - Imagem criada";
                    }
                    else{
                        echo " - Imagem não criada";
                    }
                }
                else{
                    echo " - imagem não encontrada";
                }*/
                   
                $preco_venda = "";//$linhaArray[9];
                $preco_compra = "";//$linhaArray[8];
                $quantidade =0;
                
                echo " - Quantidade: ".$quantidade;
                $produto_filial_novo    = new ProdutoFilial;
                $produto_filial_novo->produto_id                    = $produto_novo->id;
                $produto_filial_novo->filial_id                     = 38;
                $produto_filial_novo->quantidade                    = $quantidade;
                $produto_filial_novo->envio                         = 1;
                $produto_filial_novo->atualizar_preco_mercado_livre = true;
                if($produto_filial_novo->save()){
                    echo " - estoque_criado";
                    fwrite($arquivo_log, " - estoque_criado");
                }
                else{
                    print_r($produto_filial_novo);
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
                fwrite($arquivo_log, 'Produto já encontrado');
                
                /*$path_sem_logo = "/var/tmp/vannucci_produto_ausentes_16-04-2020/sem-logo/";
                $path_com_logo = "/var/tmp/vannucci_produto_ausentes_16-04-2020/com-logo/";
                
                if (file_exists($path_sem_logo.$produto->codigo_fabricante.".webp")){
                    echo " - imagem encontrada";
                
                    $imagem                     = new Imagens;
                    $imagem->produto_id         = $produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($path_sem_logo.$produto->codigo_fabricante.".webp"));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($path_com_logo.$produto->codigo_fabricante.".webp"));
                    $imagem->ordem              = 1;
                    if($imagem->save()){
                        echo " - Imagem criada";
                    }
                    else{
                        echo " - Imagem não criada";
                    }
                }
                else{
                    echo " - imagem não encontrada";
                }*/
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de criação de produtos!";
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
