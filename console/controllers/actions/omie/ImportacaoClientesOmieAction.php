<?php

namespace console\controllers\actions\omie;

use common\models\Empresa;
use common\models\EnderecoEmpresa;
use yii\base\Action;
use console\controllers\actions\omie\Omie;


class ImportacaoClientesOmieAction extends Action
{
    public function run()
    {
        echo "INÍCIO da rotina de importação de Clientes: \n\n";

        $acessoOmie = [
            // '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            // '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291'
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
                ]
            ];

            $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
            $response = (object) $responseOmie;

            $total_de_paginas = $response->body["total_de_paginas"];

            for ($x = 12; $x <= $total_de_paginas; $x++) {
                $body = [
                    "call" => "ListarClientes",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "pagina" => $x,
                        "registros_por_pagina" => 500,
                        "apenas_importado_api" => "N",
                        "ordenar_por" => "CODIGO",
                    ]
                ];
                $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
                $response = (object) $responseOmie;

                if ($response->body) {

                    if ($response->body["clientes_cadastro"]) {

                        foreach ($response->body["clientes_cadastro"] as $dadosCliente) {
                            $cnpj = $dadosCliente['cnpj_cpf'];
                            $cnpj = str_replace('.', '', $cnpj);
                            $cnpj = str_replace('/', '', $cnpj);
                            $cnpj = str_replace('-', '', $cnpj);
                            $empresa = Empresa::findOne(['documento' => $cnpj]);
                            if ($empresa) {
                                continue;
                            }

                            $empresa = new Empresa();
                            $endEmpresa = new EnderecoEmpresa();

                            if (isset($dadosCliente['tags'])) {
                                foreach ($dadosCliente['tags'] as $tag) {
                                    if ($tag['tag'] == 'Cliente') {

                                        $empresa->nome = $dadosCliente['nome_fantasia'];
                                        $empresa->razao = $dadosCliente['razao_social'];
                                        $empresa->documento = $dadosCliente['cnpj_cpf'];
                                        $empresa->documento = str_replace('.', '', $empresa->documento);
                                        $empresa->documento = str_replace('/', '', $empresa->documento);
                                        $empresa->documento = str_replace('-', '', $empresa->documento);
                                        if (isset($dadosCliente['email'])) {
                                            $empresa->email = $dadosCliente['email'];
                                            $empresa->email = substr($empresa->email, 0, 148);
                                        } else {
                                            $empresa->email = '';
                                        }
                                        $empresa->grupo_id = 1;
                                        $empresa->save(false);

                                        $endEmpresa->cep = $dadosCliente['cep'];
                                        $endEmpresa->logradouro = $dadosCliente['endereco'];
                                        $endEmpresa->bairro = substr($dadosCliente['bairro'], 0, 49);
                                        $endEmpresa->cidade_id = $dadosCliente['cidade_ibge'];
                                        $endEmpresa->numero = $dadosCliente['endereco_numero'];
                                        $endEmpresa->empresa_id = $empresa->id;

                                        $endEmpresa->save(false);

                                        echo $empresa->nome . ' - ' . $empresa->documento . "\n";
                                    }
                                }
                            }
                        }
                    } else {
                        echo "não existe dados de Cliente";
                    }
                }
            }
        }
    }
}
