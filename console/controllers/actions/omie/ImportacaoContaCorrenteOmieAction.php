<?php

namespace console\controllers\actions\omie;

use backend\models\ContaCorrente;
use yii\base\Action;
use console\controllers\actions\omie\Omie;


class ImportacaoContaCorrenteOmieAction extends Action
{
    public function run()
    {
        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
        ];

        $omie = new Omie(1, 1);

        $body = [
            "call" => "ListarContasCorrentes",
            "app_key" => '1758907907757',
            "app_secret" => '0a69c9b49e5a188e5f43d5505f2752bc',
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "filtrar_apenas_ativo" => "S"
            ]
        ];

        $responseOmie = $omie->consulta("/api/v1/geral/contacorrente/?JSON=", $body);

        if ($responseOmie['body']["ListarContasCorrentes"]) {

            foreach ($responseOmie['body']["ListarContasCorrentes"] as $dados_conta_corrente) {

                $conta_corrente = ContaCorrente::findOne(['codigo_conta_corrente_omie' => $dados_conta_corrente['nCodCC']]);

                if ($conta_corrente) {
                    continue;
                }

                $conta_corrente = new ContaCorrente();

                $conta_corrente->descricao = $dados_conta_corrente['descricao'];
                $conta_corrente->filial_id = 93;
                $conta_corrente->codigo_conta_corrente_omie = $dados_conta_corrente['nCodCC'];
                $conta_corrente->save(false);

                echo $conta_corrente->descricao . ' - ' . $conta_corrente->codigo_conta_corrente_omie . "\n";
            }
        } else {
            echo "não existe dados de CC";
        }
    }
}
