<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class VannucciCriarCaixaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço!";
        
        $produtos = Produto::find()  ->andWhere(["like","codigo_fabricante","-482"])
                                    //->andWhere(["=","fabricante_id",52])
                                    ->all();
       
        foreach($produtos as $k => $produto){
            
            $nome = str_replace('LONA', '2 JOGOS LONA', str_replace('JOGO LONA', 'LONA', str_replace('JOGO DE LONA', 'LONA', str_replace('JG LONA', 'LONA', str_replace( 'JG DE LONA', 'LONA', $produto->nome)))));
            echo "\n".$k." - ".$nome;
            //continue;
            
            $produto_caixa  = Produto::find()   ->andWhere(["=","codigo_fabricante","CX.".$produto->codigo_fabricante])
                                                //->andWhere(["=","fabricante_id",52])
                                                ->one();
            
            if(!$produto_caixa){
                echo " - Criar produto";
                
                $produto_novo                           = new Produto;
                $produto_novo->nome                     = $nome;
                $produto_novo->descricao                = $produto->descricao;
                $produto_novo->peso                     = $produto->peso;
                $produto_novo->altura                   = $produto->altura;
                $produto_novo->profundidade             = $produto->profundidade;
                $produto_novo->largura                  = $produto->largura;
                $produto_novo->codigo_global            = "CX.".$produto->codigo_global;
                $produto_novo->codigo_montadora         = $produto->codigo_montadora;
                $produto_novo->codigo_fabricante        = "CX.".$produto->codigo_fabricante;
                $produto_novo->fabricante_id            = $produto->fabricante_id;
                $produto_novo->slug                     = $produto->slug;
                $produto_novo->micro_descricao          = $produto->micro_descricao;
                $produto_novo->subcategoria_id          = $produto->subcategoria_id;
                $produto_novo->aplicacao                = $produto->aplicacao;
                $produto_novo->texto_vetor              = $produto->texto_vetor;
                $produto_novo->codigo_similar           = $produto->codigo_similar;
                //$produto_novo->aplicacao_complementar   = $produto->aplicacao_complementar;
                $produto_novo->multiplicador            = 2;
                $produto_novo->video                    = $produto->video;
                $produto_novo->codigo_barras            = $produto->codigo_barras;
                $produto_novo->cest                     = $produto->cest;
                $produto_novo->ipi                      = $produto->ipi;
                if($produto_novo->save()){
                    echo " - produto salvo";

                    $quantidade = 1;
                    $produto_filial = ProdutoFilial::find()->andWhere(["=", "produto_id", $produto->id])->one();
                    if($produto_filial){
                        $quantidade = $produto_filial->quantidade;
                    }
                    
                    $produto_filial_novo             = new ProdutoFilial;
                    $produto_filial_novo->produto_id = $produto_novo->id;
                    $produto_filial_novo->filial_id  = 38;
                    $produto_filial_novo->quantidade = $quantidade;
                    $produto_filial_novo->envio      = 1;
                    if($produto_filial_novo->save()){
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
            else{
                echo " - Não criar produto";
            }
        }
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}



