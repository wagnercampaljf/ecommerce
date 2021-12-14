<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use yii\base\Action;
use console\controllers\actions\omie\Omie;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;


class InclusaoCodigoIntegracaoProdutoAction extends Action
{
    public function run()
    {
        echo "INÍCIO update de produtos: \n\n";

        $model = Produto::find()->where("nome like 'BEBEDOURO INDUSTRIAL%'")->orderBy(['id' => SORT_DESC])->all();

        $count = 1;

        foreach ($model as $produto) {

            if (substr($produto->codigo_global, 0, 3) != 'CX.' || substr($produto->codigo_global, 0, 2) != 'P.' || substr($produto->codigo_global, 0, 2) != 'K.') {

                echo $count . ' ';

                $erros = $this->alterarOmie($produto);

                if ($erros !== '') {
                    echo $erros;
                    die;
                }
                $count++;
            }
        }
    }

    public function alterarOmie($produto)
    {

        $erros = '';
        $omie = new Omie(1, 1);

        $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
        $valor_produto  = ($minValue == NULL) ? "1" : $minValue->getValorFinal();
        $descricao = substr($produto->codigo_global . " " . $produto->nome, 0, 120);
        $descricao = str_replace(" ", "%20", $descricao);

        //echo "Alterando produtos...SP\n\n";
        $body = [
            "call" => "AlterarProduto",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "codigo_produto_integracao" => "PA" . $produto->id,
                "codigo"                    => "PA" . $produto->id,
                "descricao"                 => $descricao,
                "ncm"                       => $produto->codigo_montadora == "" ? "0000.00.00" : $produto->codigo_montadora,
                "cst_pis"                   => $produto->pis_cofins,
                "cst_cofins"                => $produto->pis_cofins,
                "unidade"                   => "PC",
                "valor_unitario"            => round($valor_produto, 2),
                "tipoItem"                  => "99",
                "peso_liq"                  => 0.001, //round($produto->peso, 2),
                "peso_bruto"                => 0.001, //round($produto->peso, 2),
                "altura"                    => round($produto->altura, 2),
                "largura"                   => round($produto->largura, 2),
                "profundidade"              => round($produto->profundidade, 2),
                "marca"                     => ($produto->fabricante_id == null) ? "Peça Agora" : $produto->fabricante->nome,
                "recomendacoes_fiscais"     =>  [
                    "origem_mercadoria" => 0,
                    "cupom_fiscal"      => "S",
                    "id_cest" => substr($produto->cest, 0, 2) . '.' . substr($produto->cest, 2, 3) . '.' . substr($produto->cest, 5, 2)
                ]
            ]
        ];
        $response = $omie->altera_produto("api/v1/geral/produtos/?JSON=", $body);

        if (ArrayHelper::getValue($response, 'httpCode') !== 200) {

            $body = [
                "call" => "ConsultarProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    "codigo" => "PA$produto->id",
                ]
            ];
            $respOmie = $omie->consulta("/api/v1/geral/produtos/?JSON=", $body);

            if (ArrayHelper::getValue($respOmie, 'httpCode') !== 200) {
                $erros .= $this->cria_omie($produto);
            } else {

                if ($respOmie['body']["codigo_produto_integracao"] == '' || $respOmie['body']["codigo_produto_integracao"] !== 'PA' . $produto->id) {

                    $codigoOmie = $respOmie['body']["codigo_produto"];
                    $body = [
                        "call" => "AssociarCodIntProduto",
                        "app_key" => '468080198586',
                        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                        "param" => [
                            "codigo_produto" => $codigoOmie,
                            "codigo_produto_integracao" => "PA$produto->id",
                        ]
                    ];

                    $response = $omie->altera_produto("api/v1/geral/produtos/?JSON=", $body);
                    if (ArrayHelper::getValue($response, 'httpCode') !== 200) {
                        $erros .= $produto->nome . ' - ' . $response['body']['faultstring'] . "\n";
                    } else {
                        $this->alterarOmie($produto);
                    }
                }
            }
        } else {
            echo $produto->id . ' - ' . $produto->nome . ' - ' . 'Produto Alterado com Sucesso' . "\n";
        }

        return $erros;
    }

    public function cria_omie($produto)
    {
        $erros = '';
        $criar_omie = new Omie(1, 1);

        $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
        $valor_produto  = ($minValue == NULL) ? "1" : str_replace(".", ",", $minValue->getValorFinal());
        $ncm = $produto->codigo_montadora == "" ? "0000.00.00" : $produto->codigo_montadora;
        $descricao = substr($produto->codigo_global . " " . $produto->nome, 0, 120);
        $descricao = str_replace(" ", "%20", $descricao);

        $body = [
            "call" => "IncluirProduto",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "codigo_produto_integracao" => "PA" . $produto->id,
                "codigo"                    => "PA" . $produto->id,
                "descricao"                 => $descricao,
                "ncm"                       => $produto->codigo_montadora == "" ? "0000.00.00" : $produto->codigo_montadora,
                "cst_pis"                   => $produto->pis_cofins,
                "cst_cofins"                => $produto->pis_cofins,
                "unidade"                   => "PC",
                "valor_unitario"            => round($valor_produto, 2),
                "tipoItem"                  => "99",
                "peso_liq"                  => 0.001, //round($produto->peso, 2),
                "peso_bruto"                => 0.001, //round($produto->peso, 2),
                "altura"                    => round($produto->altura, 2),
                "largura"                   => round($produto->largura, 2),
                "profundidade"              => round($produto->profundidade, 2),
                "marca"                     => ($produto->fabricante_id == null) ? "Peça Agora" : $produto->fabricante->nome,
                "recomendacoes_fiscais"     =>  [
                    "origem_mercadoria" => 0,
                    "cupom_fiscal"      => "S",
                    "id_cest" => substr($produto->cest, 0, 2) . '.' . substr($produto->cest, 2, 3) . '.' . substr($produto->cest, 5, 2)
                ]
            ]
        ];

        $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=", $body);
        if (ArrayHelper::getValue($response, 'httpCode') !== 200) {
            $erros .= $response['body']['faultstring'] . " (SP2) \n";
        } else {
            echo $produto->id . ' - ' . $produto->nome . ' - ' . 'Produto Criado com Sucesso' . "\n";
        }

        return $erros;
    }
}
