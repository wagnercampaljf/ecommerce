<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Imagens;

class CriarCaixasDibAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $date = date('Y-m-d H:i');
        echo "\n\nInicio: ";echo $date;

        $produtos  = ProdutoFilial::find()  ->where("produto_filial.filial_id = 97 and (upper(produto.nome) like upper('CAPA PORCA RODA%') or upper(produto.nome) like upper('PORCA RODA%'))")
                                            ->joinWith('produto')
                                            ->all();

        foreach ($produtos as $k => &$produto_corrente){

            echo "\n".$k." - ".$produto_corrente->id;

            $produto = new Produto();
            $produto->codigo_fabricante = "CX.".$produto_corrente->produto->codigo_fabricante;
            $produto->codigo_global     = "CX.".$produto_corrente->produto->codigo_global;
            $produto->nome              = "CAIXA COM 10 ".$produto_corrente->produto->nome;
            $produto->aplicacao         = $produto_corrente->produto->aplicacao;
            $produto->peso              = $produto_corrente->produto->peso;
            $produto->altura            = $produto_corrente->produto->altura;
            $produto->largura           = $produto_corrente->produto->largura;
            $produto->profundidade      = $produto_corrente->produto->profundidade;
            $produto->subcategoria_id   = $produto_corrente->produto->subcategoria_id; 
            $produto->fabricante_id     = $produto_corrente->produto->fabricante_id;
            $produto->multiplicador     = 10;
            $produto->codigo_similar    = $produto_corrente->produto->codigo_similar;
            $this->slugify($produto);
            if ($produto->save()){
                echo " - Produto CRIADO";
            } else{
                echo " - Produto NÃO CRIADO";
                continue;
            }

            $imagens = Imagens::find()->andWhere(['=', 'produto_id', $produto_corrente->produto->id])->orderBy('ordem')->all();
            foreach ($imagens as $i => $imagem_corrente){
                echo " - Imagem ".$i;
                $imagem = new Imagens();
                $imagem->produto_id         = $produto->id;
                $imagem->imagem             = $imagem_corrente->imagem;
                $imagem->imagem_sem_logo    = $imagem_corrente->imagem_sem_logo;
                $imagem->ordem              = $i;
                if($imagem->save()){
                    echo "(imagem criada)";
                }
                else{
                    echo "(imagem não criada)";
                }
            }

            $produtoFilial              = new ProdutoFilial();
            $produtoFilial->produto_id  = $produto->id;
            $produtoFilial->filial_id   = $produto_corrente->filial_id;
            $produtoFilial->quantidade  = $produto_corrente->quantidade;
            $produtoFilial->envio       = $produto_corrente->envio;
            if ($produtoFilial->save()){
                echo(" - ProdutoFilial CRIADO");
            } else{
                echo(" - ProdutoFilial NAO CRIADO");
                continue;
            }

	    $valor_unitario = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_corrente->id])
                                                        ->orderBy(["dt_inicio" => SORT_DESC])
                                                        ->one();

            $valorProdutoFilial                     = New ValorProdutoFilial;
            $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
            $valorProdutoFilial->valor              = 10 * $valor_unitario->valor;
            $valorProdutoFilial->valor_cnpj         = 10 * $valor_unitario->valor_cnpj;
            $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
            if ($valorProdutoFilial->save()){
                echo " - ValorProdutoFilial CRIADO";
            } else{
                echo " - ValorProdutoFilial NAO CRIADO";
                continue;
            }
        }

        $date = date('Y-m-d H:i');
        echo "\n\nTermino: ";echo $date;

        echo "\n\nFIM da rotina de criação preço!";
    }

    private function slugify(&$model)
    {
        $text = $model->nome . ' ' . $model->codigo_global;
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
        $model->slug = $text;
    }
}
