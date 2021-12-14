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

class VannucciAtualizarNomesAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_ausentes_completo_27-04-2020_02.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_atualizar_nomes.csv")){
            unlink("/var/tmp/log_atualizar_nomes.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_atualizar_nomes.csv", "a");
        //Escreve no log
        //fwrite($arquivo_log, "coidgo_fabricante;NCM;codigo_global;valor;valor_compra;valor_venda;produto_filial_id;status_produto;status_estoque;status_preco\n");
        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            echo "\n".$i." - ".$linhaArray[4]." - ".$linhaArray[1];
            
            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[4].'";"'.$linhaArray[2].'";"'.$linhaArray[1].'";');
            
            if ($i <= 2){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "produto_id;produto_origem_id;status");
                continue;
            }
            
            $global_limpo = str_replace(".", "", str_replace(")", "", str_replace("*", "", str_replace("-", "", str_replace("|", "", str_replace("_", "", str_replace(",", "", $linhaArray[2])))))));
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[4]])
                                        ->andWhere(['=','fabricante_id', 91])
                                        ->one(); //Procura produto pelo código do fabricante "VA"
            
            if ($produto){ //Se encontrar o produto, processa
                
                echo "\n".$produto->codigo_fabricante;
                
                //echo " - Produto encontrado";
                fwrite($arquivo_log, $produto->id.';"'.$produto->nome.'";');
                
                $produto_origem  = Produto::find()->andWhere(['like', 'codigo_global', $global_limpo])
                                                  ->andWhere(['<>', 'id', $produto->id])
                                                  ->andWhere(['=','fabricante_id', 91])
                                                  ->one();
                
                if($produto_origem){
                    
                    echo "\n".$produto_origem->codigo_fabricante;
                    
                    echo " - Produto Origem encontrado";
                    fwrite($arquivo_log, $produto_origem->id.';"'.$produto_origem->nome.'"');
                    
                    $produto->nome = $produto_origem->nome;
                    $this->slugify($produto);
                    
                    if($produto->save()){
                         echo " - produto_alterado";
                         fwrite($arquivo_log, ";produto_alterado");
                    }
                    else{
                        echo " - produto_não_alterado";
                        fwrite($arquivo_log, ";produto_nao_alterado");
                        continue;
                    }
                }
                else{
                    echo " - Produto Origem não encontrado";
                    fwrite($arquivo_log, ";Produto Origem não encontrado");
                }
            }
            else{
                echo " - Produto não encontrado";
                fwrite($arquivo_log, 'Produto não encontrado');
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
}







