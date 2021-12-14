<?php

namespace console\controllers\actions\omie;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use console\controllers\actions\omie\omie2;
use console\models\SkyhubClient;
use yii\helpers\Json;
use common\models\ValorProdutoFilial;

class CriaProdutoAction extends Action
{
    public function run($global_id)
    {
       
        echo "Criando produtos...\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['=', 'id', 96])->one();

        echo "\n entrou \n";
        
        $produtoFilials = $filial->getProdutoFilials()->andWhere(['=','id',$global_id])->all();
        
        foreach ($produtoFilials as $produtoFilial) {
            echo "\n for \n";
            
            $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não categoria",
                'error_yii');
                echo "1\n";
                return;
            }
            if (is_null($produtoFilial->valorMaisRecente)) {
                Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                'error_yii');
                echo "2\n";
                return;
            }
            $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',
                ['produto' => $produtoFilial]);
            
            $title = Yii::t('app', '{nome} ({cod})', [
                'cod' => $produtoFilial->produto->codigo_global,
                'nome' => $produtoFilial->produto->nome
            ]);
            
            $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produtoFilial->produto->id)->one();
            $menor_preco    = str_replace(".",",",$minValue->getValorFinal());
            echo "\n\n TTT: "; var_dump($menor_preco); echo "\n\n : TTT"; 
            
            $body = [
                "call" => "IncluirProduto",
                "app_key" => static::APP_KEY_OMIE,
                "app_secret" => static::APP_SECRET_OMIE,
                "param" => [
                       	"codigo_produto_integracao" => $produtoFilial->produto->codigo_global,
                        "codigo"                    => $produtoFilial->produto->codigo_global,
                        "descricao"                 => $produtoFilial->produto->nome.' ('.$produtoFilial->produto->codigo_global.')',
                        "ncm"                       => ($produtoFilial->produto->codigo_montadora=="" ? "0000.00.00" : substr($produtoFilial->produto->codigo_montadora,0,4).".".substr($produtoFilial->produto->codigo_montadora,4,2).".".substr($produtoFilial->produto->codigo_montadora,6,2)),
                        "valor_unitario"            => $menor_preco,
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => $produtoFilial->produto->peso,
                        "peso_bruto"                => $produtoFilial->produto->peso,
                        "altura"                    => $produtoFilial->produto->altura,
                        "largura"                   => $produtoFilial->produto->largura,
                        "profundidade"              => $produtoFilial->produto->profundidade,
                        "marca"                     => $produtoFilial->produto->fabricante->nome,
                        "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                        /*"codigo_produto_integracao" => "",
                        "codigo"                    => "6968200156,",
                        "descricao"                 => utf8_encode("LANTERNA DELIMITADORA SUPERIOR EXTERNA DIREITA ESQUERDA MB ATRON 2324 1620 912 (6968200156,)"),
                        "ncm"                       => "8512.20.29",
                        "valor_unitario"            => 1,
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => 0.2,
                        "peso_bruto"                => 0.2,
                        "altura"                    => 20,
                        "largura"                   => 10,
                        "profundidade"              => 10,
                        "marca"                     => utf8_encode("RB PEÇA AGORA"),
                        "recomendacoes_fiscais"     => [ "origem_mercadoria" => 0 ]*/
                ]                
            ];
            var_dump($body); echo "\n\n";
	    //die;
            
            $response = $meli->cria_produto("api/v1/geral/produtos/?JSON=",$body);
            echo "123::";var_dump($response);
        }
    //$omie = new omie2();
        //$products = $omie->products()->findAll(1, 10);
        //var_dump($products);
    }
}



