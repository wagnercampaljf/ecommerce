<?php

namespace console\controllers\actions\omie;

use common\models\Transportadora;
use yii\base\Action;
use console\controllers\actions\omie\Omie;


class ImportacaoTrasportadoraOmieAction extends Action
{
    public function run()
    {
        $APP_KEY_OMIE              = '1758907907757';
        $APP_SECRET_OMIE           = '0a69c9b49e5a188e5f43d5505f2752bc';

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
                        "tag" => "transportadora"
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
                            "tag" => "transportadora"
                        ]
                    ]
                ]
            ];
            $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
            $response = (object) $responseOmie;

            $transportadora = null;

            if ($response->body) {

                if ($response->body["clientes_cadastro"]) {

                    foreach ($response->body["clientes_cadastro"] as $dadosNF) {

                        $transportadora = Transportadora::findOne(['codigo_omie' => $dadosNF['codigo_cliente_omie']]);
                        if (!$transportadora) {
                            $transportadora = new Transportadora();

                            $transportadora->nome = $dadosNF['nome_fantasia'];
                            $transportadora->codigo_omie = $dadosNF['codigo_cliente_omie'];
                            $transportadora->filial_id = 93;
                            $transportadora->razao_social = $dadosNF['razao_social'];
                            $transportadora->email = isset($dadosNF['email']) ? substr($dadosNF['email'], 0, 99) : '';
                            $transportadora->cnpj = str_replace('.', '', $dadosNF['cnpj_cpf']);
                            $transportadora->cnpj = str_replace('/', '', $transportadora->cnpj);
                            $transportadora->cnpj = str_replace('-', '', $transportadora->cnpj);
                            $transportadora->save(false);
                            echo $transportadora->nome . ' - ' . $transportadora->codigo_omie . "\n";
                        } else {
                            continue;
                        }
                    }
                } else {
                    echo "não existe dados de transportadora";
                }
            }
        }
    }
}
