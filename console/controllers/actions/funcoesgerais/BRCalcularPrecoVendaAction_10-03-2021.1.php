<?php

namespace console\controllers\actions\funcoesgerais;

use frontend\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class BRCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";



        $LinhasArray = Array();
        //$arquivo_origem = '/var/tmp/br_preco_estoque_11-11-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_17-11-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_30-11-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_10-12-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_11-12-2020';
        // $arquivo_origem = '/var/tmp/br_preco_estoque_15-12-2020';
        //$arquivo_origem = '/var/tmp/br_preco_estoque_30-12-2020';
        //  $arquivo_origem = '/var/tmp/br_preco_estoque_08-01-2021';
        //   $arquivo_origem = '/var/tmp/br_preco_estoque_25-02-2021';

        $arquivo_origem = '/var/tmp/br_preco_estoque_09-03-2021';

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
               // fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";Preço Venda;novo_codigo_fabricante\n");

                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";Preço Venda;novo_codigo_fabricante\n");

                continue;
            }
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $linhaArray[1].".B"])->one();

            $preco_compra   = (float) $linhaArray[7];
            $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();
            $margem             = $markup_mestre->margem_padrao;
            $e_margem_absoluta  = $markup_mestre->e_margem_absoluta_padrao;


            $preco_venda = $this->calcular_preco_venda($preco_compra, $markup_mestre->id, $markup_mestre->margem_padrao, $markup_mestre->e_margem_absoluta_padrao);

            //$preco_compra   = $preco_compra * $produto->multiplicador;

            $this->existe_produto_caixa($linhaArray, $arquivo_destino);

            echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
            fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$preco_venda.";".$novo_codigo_fabricante."\n");


        }
        
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }


    public function existe_produto_caixa($linha, $arquivo_destino){




        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$linha[1].".B"])
            ->andWhere(["=","fabricante_id",52])
            ->one();


        if($produto){


            $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();
            $margem             = $markup_mestre->margem_padrao;
            $e_margem_absoluta  = $markup_mestre->e_margem_absoluta_padrao;

            $preco_compra   = (float) $linha[7];
            $preco_compra   = $preco_compra * $produto->multiplicador;

            $preco_venda = $this->calcular_preco_venda($preco_compra, $markup_mestre->id, $markup_mestre->margem_padrao, $markup_mestre->e_margem_absoluta_padrao);



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
                $produto_novo->codigo_global            = str_replace("CX.","",$produto->codigo_global);
                $produto_novo->codigo_montadora         = $produto->codigo_montadora;
                $produto_novo->codigo_fabricante        = str_replace("CX.","",$produto->codigo_fabricante);
                $produto_novo->fabricante_id            = $produto->fabricante_id;
                $produto_novo->slug                     = str_replace("kit-","",str_replace("caixa-","",$produto->slug));
                $produto_novo->micro_descricao          = $produto->micro_descricao;
                $produto_novo->subcategoria_id          = $produto->subcategoria_id;
                $produto_novo->aplicacao                = $produto->aplicacao;
                $produto_novo->texto_vetor              = $produto->texto_vetor;
                $produto_novo->codigo_similar           = $produto->codigo_similar;
                //$produto_novo->aplicacao_complementar   = $produto->aplicacao_complementar;
                $produto_novo->multiplicador            = 1;
                $produto_novo->video                    = $produto->video;
                $produto_novo->codigo_barras            = $produto->codigo_barras;
                $produto_novo->cest                     = $produto->cest;
                $produto_novo->ipi                      = $produto->ipi;
                if($produto_novo->save()){
                    echo " - produto salvo";

                    $produto_filial             = new ProdutoFilial;
                    $produto_filial->produto_id = $produto_novo->id;
                    $produto_filial->filial_id  = 72;
                    $produto_filial->quantidade = 781;
                    $produto_filial->envio      = 1;
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




            echo " - ".$preco_venda." - "."CX.".$linha[1].".B";
            fwrite($arquivo_destino, $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6].";".$linha[7].";".$linha[8].";".$linha[9].";".$linha[10].";".$linha[11].";".$linha[12].";".$linha[13].";".$preco_venda.";"."CX.".$linha[1].".B\n");


        }
    }


    public function calcular_preco_venda($preco_compra, $markup_mestre_id, $margem_padrao, $e_margem_absoluta_padrao){

        $markup_detalhe     = MarkupDetalhe::find()->where(" (".$preco_compra." BETWEEN valor_minimo AND valor_maximo) AND (markup_mestre_id = ".$markup_mestre_id.") ")->one();

        if($markup_detalhe){

            $margem             = $markup_detalhe->margem;

            $e_margem_absoluta  = $markup_detalhe->e_margem_absoluta;

        }else{

            $margem             = $margem_padrao;

            $e_margem_absoluta  = $e_margem_absoluta_padrao;

        }

        $preco_venda            = $preco_compra * $margem;

        if($e_margem_absoluta){

            $preco_venda = $margem;

        }

        return $preco_venda;


    }
}



