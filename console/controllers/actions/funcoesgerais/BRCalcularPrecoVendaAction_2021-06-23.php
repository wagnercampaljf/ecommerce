<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class BRCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();



        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }



        $LinhasArray = Array();


        //$arquivo_origem = '/var/tmp/br_preco_estoque_04-05-2021';

        //$arquivo_origem = '/var/tmp/br_preco_estoque_07-05-2021';

       // $arquivo_origem = '/var/tmp/br_preco_estoque_11-05-2021';

        //$arquivo_origem = '/var/tmp/br_preco_estoque_19-05-2021';

        //$arquivo_origem = '/var/tmp/br_preco_estoque_25-05-2021';

        //$arquivo_origem = '/var/tmp/br_preco_estoque_01-06-2021';

        //$arquivo_origem = '/var/tmp/br_preco_estoque_09-06-2021';

        $arquivo_origem = '/var/tmp/br_preco_estoque_15-06-2021';


        $file = fopen($arquivo_origem.".csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $destino = $arquivo_origem."_precificado.csv";
        if (file_exists($destino)){
            unlink($destino);
        }
        
        $arquivo_destino = fopen($destino, "a");

        foreach ($LinhasArray as $i => &$linhaArray){
            
            echo "\n".$i." - ".$linhaArray[1]. " - ".$linhaArray[7];
            
            $novo_codigo_fabricante = $linhaArray[1].".B";
            
            if ($i <= 1){
                //fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";Preço Venda;novo_codigo_fabricante\n");

                //fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";Preço Venda;novo_codigo_fabricante\n");

                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";Preço Venda;novo_codigo_fabricante\n");

                continue;
            }
            
            /*if ($i >= 50){
                die;
            }*/
         
            //print_r($linhaArray);
            $preco_compra   = (float) $linhaArray[7];

            //$preco_compra   = $preco_compra * $linhaArray[13];

            $this->existe_produto_caixa($linhaArray, $arquivo_destino);

	        //continue;

            //$preco_compra = $preco_compra * 0.65;

            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }

                    echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
                    fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$preco_venda.";".$novo_codigo_fabricante."\n");
                    //fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$preco_venda.";".$novo_codigo_fabricante."\n");

                    break;
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
    
    public function existe_produto_caixa($linha, $arquivo_destino){
        
        $faixas = array();

        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$linha[1].".B"])
                                    ->andWhere(["=","fabricante_id",52])
                                    ->one();

        if($produto){

            $produto_sem_caixa  = Produto::find()   ->andWhere(["=","codigo_fabricante",$linha[1].".B"])
                                                    ->andWhere(["=","fabricante_id",52])
                                                    ->one();


            if(!$produto_sem_caixa){
                $produto_novo                           = new Produto;
                $produto_novo->nome                     = str_replace("CAIXA ", "", $produto->nome);
                $produto_novo->descricao                = $produto->descricao;
                $produto_novo->peso                     = $produto->peso;
                $produto_novo->altura                   = $produto->altura;
                $produto_novo->profundidade             = $produto->profundidade;
                $produto_novo->largura                  = $produto->largura;
                $produto_novo->codigo_global            = str_replace("CX.","",$produto->codigo_global)."#";
                $produto_novo->codigo_montadora         = $produto->codigo_montadora;
                $produto_novo->codigo_fabricante        = str_replace("CX.","",$produto->codigo_fabricante);
                $produto_novo->fabricante_id            = $produto->fabricante_id;
                $produto_novo->slug                     = str_replace("kit-","",str_replace("caixa-","",$produto->slug));
                $produto_novo->micro_descricao          = $produto->micro_descricao;
                $produto_novo->subcategoria_id          = $produto->subcategoria_id;
                $produto_novo->aplicacao                = $produto->aplicacao;
                $produto_novo->texto_vetor              = $produto->texto_vetor;
                $produto_novo->codigo_similar           = $produto->codigo_similar;
                $produto_novo->produto_condicao_id      = $produto->produto_condicao_id;
                //$produto_novo->aplicacao_complementar   = $produto->aplicacao_complementar;
                $produto_novo->multiplicador            = 1;
                $produto_novo->video                    = $produto->video;
                $produto_novo->codigo_barras            = $produto->codigo_barras;
                $produto_novo->cest                     = $produto->cest;
                $produto_novo->ipi                      = $produto->ipi;

                //print_r($produto_novo);

                if($produto_novo->save()){

                    echo " - produto salvo";

                    $produto_filial             = new ProdutoFilial;
                    $produto_filial->produto_id = $produto_novo->id;
                    $produto_filial->filial_id  = 72;
                    $produto_filial->quantidade = 781;
                    $produto_filial->envio      = 1;


                    //print_r($produto_filial);

                    if($produto_filial->save()){
                        echo " - produto_filial salvo";
                    }
                    else{
                        echo " - produto_filial não salvo";
                    }
                }
                else{
                    echo " - produto não salvo";
                }
            }

            $preco_compra   = (float) $linha[7];
            $preco_compra   = $preco_compra * $produto->multiplicador;
            
            foreach ($faixas as $i => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }
                    //fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$preco_venda.";"."CX.".$linha[1].".B\n");
		    echo " - ".$preco_venda." - "."CX.".$linha[1].".B";
                    fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$linha[8].";".$linha[9].";".$linha[10].";".$linha[11].";".$linha[12].";".$linha[13].";".$preco_venda.";"."CX.".$linha[1].".B\n");

                    //fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$linha[8].";".$linha[9].";".$linha[10].";".$linha[11].";".$linha[12].";".$preco_venda.";"."CX.".$linha[1].".B\n");


                    break;
                }
            }
        }
    }
}



