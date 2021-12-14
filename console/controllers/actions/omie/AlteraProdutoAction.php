<?php

namespace console\controllers\actions\omie;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use console\models\SkyhubClient;
use yii\helpers\Json;
use common\models\ValorProdutoFilial;

class AlteraProdutoAction extends Action
{
    public function run($global_id)
    {
       
        echo "Criando produtos...\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);

            echo "\n for \n";

	    $produtoFilial = ProdutoFilial::find()->andWhere(['=','id',$global_id])->one();
            
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
            //echo "\n\n TTT: "; var_dump($menor_preco); echo "\n\n : TTT"; 
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE,
                "app_secret" => static::APP_SECRET_OMIE,
                "param" => [
			"codigo_produto_integracao" => "0000006",
                        "codigo"                    => "0000006",
                        "descricao"                 => "Produto Peça de Teste API",
                        "ncm"                       => "0000.00.00",
                        "valor_unitario"            => 40,
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => 10,
                        "peso_bruto"                => 10,
                        "altura"                    => 87,
                        "largura"                   => 88,
                        "profundidade"              => 89,
                        "marca"                     => "Peca Agora",
                        "recomendacoes_fiscais"     => [ "origem_mercadoria" => "0" ]
                ]                
            ];
            
            
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            var_dump($response);
    }
}




