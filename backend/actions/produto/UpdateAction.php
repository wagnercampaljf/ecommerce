<?php

/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19/10/2015
 * Time: 13:39
 */

namespace backend\actions\produto;

use backend\controllers\ProdutoController;
use backend\functions\FunctionsML;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use console\controllers\actions\omie\Omie;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class UpdateAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->slugify($model);
            if ($model->save(false)) {
                $erros = '';
                $ml = '';

                //Atualização no Omie - São Paulo
                if (substr($model->codigo_global, 0, 3) != 'CX.') {

                    $erros = $this->cria_omie($model);
                    $erros = $this->alterarOmie($model);
                }
                $ml = FunctionsML::atualizarMercadoLivre($model);

                if (Yii::$app->request->post('submitButton') == 'expedicao') {

                    return $this->controller->redirect([
                        '/consulta-expedicao/busca', 'codigo_pa' => $model->id,
                        'mensagem' => '<div class="text-primary h4" style="font-size: 20px; color: red">Produto atualizado com sucesso!</div>',
                    ]);
                } else {

                    if ($erros !== '') {
                        $mensagem = '<div class="text-primary h4" style="font-size: 20px; color: red">' . $erros . '</div>';
                    } else {
                        $mensagem = '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF">Produto atualizado com sucesso!</div>';
                    }

                    return $this->controller->render('update', [
                        'model' => $model,
                        'mensagem' => $mensagem,
                        'mercadoLivre' => $ml
                    ]);
                }
            } else {
                return $this->controller->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
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

    public function alterarOmie($produto)
    {

        $erros = '';

        $omie = new Omie(1, 1);

        if (substr($produto->codigo_global, 0, 3) != 'CX.' || substr($produto->codigo_global, 0, 2) != 'P.' || substr($produto->codigo_global, 0, 2) != 'K.') {

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
                $erros .= $respOmie['body']['faultstring'] . "\n";
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
                        $erros .= $response['body']['faultstring'] . "\n";
                    }
                }
            }

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
                $erros .= $response['body']['faultstring'] . "\n";
            }
        }

        return $erros;
    }

    public function cria_omie($model)
    {
        $erros = '';
        $criar_omie = new Omie(1, 1);

        $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($model->id)->one();
        $valor_produto  = ($minValue == NULL) ? "1" : str_replace(".", ",", $minValue->getValorFinal());
        $ncm = $model->codigo_montadora == "" ? "0000.00.00" : $model->codigo_montadora;

        $body = [
            "call" => "IncluirProduto",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "codigo_produto_integracao" => "PA" . $model->id,
                "codigo"                    => "PA" . $model->id,
                "descricao"                 => substr("(" . $model->codigo_global . ") " . $model->nome, 0, 120),
                "ncm"                       => $ncm,
                "cst_pis"                   => $model->pis_cofins,
                "cst_cofins"        => $model->pis_cofins,
                "unidade"                   => "PC",
                "valor_unitario"            => round($valor_produto, 2),
                "tipoItem"                  => "99",
                "peso_liq"                  => 0.001, //round($model->peso, 2),
                "peso_bruto"                => 0.001, //round($model->peso, 2),
                "altura"                    => round($model->altura, 2),
                "largura"                   => round($model->largura, 2),
                "profundidade"              => round($model->profundidade, 2),
                "marca"                     => $model->fabricante->nome,
                "recomendacoes_fiscais"     =>  [
                    "origem_mercadoria" => 0,
                    "id_cest" => substr($model->cest, 0, 2) . '.' . substr($model->cest, 2, 3) . '.' . substr($model->cest, 5, 2)
                ]
            ]
        ];

        $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=", $body);
        if (ArrayHelper::getValue($response, 'httpCode') !== 200) {
            $erros .= $response['body']['faultstring'] . " (SP2) \n";
        }

        return $erros;
    }
}
