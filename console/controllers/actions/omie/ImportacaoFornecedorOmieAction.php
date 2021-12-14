<?php

namespace console\controllers\actions\omie;

use common\models\Empresa;
use common\models\EnderecoEmpresa;
use common\models\Fornecedor;
use yii\base\Action;
use console\controllers\actions\omie\Omie;


class ImportacaoFornecedorOmieAction extends Action
{
    public function run()
    {
        echo "INÍCIO da rotina de importação de Fornecedores: \n\n";

        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc',
        ];
        foreach ($acessoOmie as $key => $value) {

            $APP_KEY_OMIE              = $key;
            $APP_SECRET_OMIE           = $value;

            $omie = new Omie(1, 1);

            $body = [
                "call" => "ListarClientes",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "pagina" => 1,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "clientesFiltro" => [
                        "tags" => [
                            "tag" => "Fornecedor"
                        ]
                    ]
                ]
            ];

            $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
            $response = (object) $responseOmie;

            $total_de_paginas = $response->body["total_de_paginas"];

            for ($x = 1; $x <= $total_de_paginas; $x++) {
                $body = [
                    "call" => "ListarClientes",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "pagina" => $x,
                        "registros_por_pagina" => 500,
                        "apenas_importado_api" => "N",
                        "ordenar_por" => "CODIGO",
                        "clientesFiltro" => [
                            "tags" => [
                                "tag" => "Fornecedor"
                            ]
                        ]

                    ]
                ];
                $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
                $response = (object) $responseOmie;

                if ($response->body) {

                    if ($response->body["clientes_cadastro"]) {

                        foreach ($response->body["clientes_cadastro"] as $dadosFornecedor) {

                            $codigo_cliente_omie = $dadosFornecedor['codigo_cliente_omie'];

                            $fornecedor = Fornecedor::findOne(['codigo_fornecedor_omie' => $codigo_cliente_omie]);
                            if ($fornecedor) {
                                continue;
                            }

                            $fornecedor = new Fornecedor();

                            $fornecedor->nome = $dadosFornecedor['nome_fantasia'];
                            $fornecedor->razao_social = $dadosFornecedor['razao_social'];
                            $fornecedor->codigo_fornecedor_omie = $dadosFornecedor['codigo_cliente_omie'];
                            $fornecedor->cpf_cnpj = $dadosFornecedor['cnpj_cpf'];
                            $fornecedor->cpf_cnpj = str_replace('.', '', $fornecedor->cpf_cnpj);
                            $fornecedor->cpf_cnpj = str_replace('/', '', $fornecedor->cpf_cnpj);
                            $fornecedor->cpf_cnpj = str_replace('-', '', $fornecedor->cpf_cnpj);
                            if (isset($dadosFornecedor['email'])) {
                                $fornecedor->email = $dadosFornecedor['email'];
                                $fornecedor->email = substr($fornecedor->email, 0, 148);
                            } else {
                                $fornecedor->email = '';
                            }
                            $fornecedor->save(false);

                            echo $fornecedor->nome . ' - ' . $fornecedor->cpf_cnpj . "\n";
                        }
                    } else {
                        echo "não existe dados de Fornecedor";
                    }
                }
            }
        }
    }
}
