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
        $file = fopen("/var/tmp/morelate_para_subir_completa_precificado_dimensoes.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_morelate_para_subir_completa_precificado_dimensoes.csv")){
            unlink("/var/tmp/log_morelate_para_subir_completa_precificado_dimensoes.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_morelate_para_subir_completa_precificado_dimensoes.csv", "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){

            if ($i == 0){
                continue;
            }
            
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";"'.$linhaArray[13].'";"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";');
            
            
            if ($i == 1){
                fwrite($arquivo_log, ";18");
                continue;
            }
            elseif ($i == 2){
                fwrite($arquivo_log, ";status");
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[4]." - ".$linhaArray[7]." - ".$linhaArray[17];
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[0].".M"])
                                        ->andWhere(['=','fabricante_id', 130])
                                        ->one(); //Procura produto pelo código do fabricante "VA"
            
            if (!$produto){ //Se encontrar o produto, processa o preço
                
                /*$peso = $linhaArray[18]/1000;
                if($peso > 0){
                    echo " - peso ok";
                    continue;
                }*/
                
                if($linhaArray[11]==""){
                    echo " - sem subcategoria recomendade ML";
                    fwrite($arquivo_log, "sem_categoria_recomendada");
                    continue;
                }
                
                $produto_novo = new Produto;
                
                $subcategoria = Subcategoria::find()->andWhere(['=','meli_id',$linhaArray[11]])->one();
                if(!$subcategoria){
                    $subcategoria                   = new Subcategoria;
                    $subcategoria->nome             = $linhaArray[12];
                    $subcategoria->categoria_id     = 10;
                    $subcategoria->ativo            = true;
                    $subcategoria->meli_id          = $linhaArray[11];
                    $subcategoria->meli_cat_nome   = $linhaArray[12];
                    $this->slugify_subcategoria($subcategoria);
                    if($subcategoria->save()){
                        echo " - Subcategoria criada";
                    }
                    else{
                        echo " - Subcategoria não criada";
                    }
                }
                
                $codigo_global        = $linhaArray[3];
                $peso                 = $linhaArray[18]/1000;
                $altura               = $linhaArray[19];
                $largura              = $linhaArray[20];
                $profundidade         = $linhaArray[21];
                
                if($peso==0){
                    $peso                 = 2;
                    $altura               = 30;
                    $largura              = 30;
                    $profundidade         = 30;
                }
                
                $produto_novo->codigo_global        = $codigo_global;
                $produto_novo->codigo_fabricante    = $linhaArray[0].".M";
                $produto_novo->nome                 = $linhaArray[4];
                $produto_novo->peso                 = $peso;
                $produto_novo->altura               = $altura;
                $produto_novo->largura              = $largura;
                $produto_novo->profundidade         = $profundidade;
                $produto_novo->subcategoria_id      = $subcategoria->id;
                $produto_novo->aplicacao            = $linhaArray[6];
                $produto_novo->codigo_montadora     = $linhaArray[2];
                $produto_novo->fabricante_id        = 130;
                $this->slugify($produto_novo);
                //print_r($produto_novo); var_dump($produto_novo->save()); die;
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
                
                $imagem_sem_logo = "/var/tmp/morelate_fotos/sem-logo/".$linhaArray[0].".webp";
                $imagem_com_logo = "/var/tmp/morelate_fotos/com-logo/".$linhaArray[0].".webp";
                
                if (file_exists($imagem_sem_logo)){
                    echo " - imagem encontrada";
                    
                    $imagem                     = new Imagens;
                    $imagem->produto_id         = $produto_novo->id;
                    $imagem->imagem             = base64_encode(file_get_contents($imagem_com_logo));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($imagem_sem_logo));
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
                }
                   
                $preco_venda = $linhaArray[17];
                $preco_compra = $linhaArray[7];
                $quantidade = $linhaArray[8];
                
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
                
                $imagem_sem_logo = "/var/tmp/morelate_fotos/sem-logo/".$linhaArray[0].".webp";
                $imagem_com_logo = "/var/tmp/morelate_fotos/com-logo/".$linhaArray[0].".webp";
                
                if (file_exists($imagem_sem_logo)){
                    echo " - imagem encontrada";
                
                    $imagem                     = new Imagens;
                    $imagem->produto_id         = $produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($imagem_com_logo));
                    $imagem->imagem_sem_logo    = base64_encode(file_get_contents($imagem_sem_logo));
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
                }
            }
            
            //die;
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







