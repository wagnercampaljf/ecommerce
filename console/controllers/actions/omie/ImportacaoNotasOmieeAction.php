<?php

namespace console\controllers\actions\omie;

use backend\models\NotaFiscal;
use backend\models\NotaFiscalProduto;
use backend\models\PedidoCompra;
use yii\base\Action;
use console\controllers\actions\omie\Omie;


class ImportacaoNotasOmieeAction extends Action
{
    public function run()
    {
        echo "INÍCIO da rotina de importação de Notas: \n\n";

        $omie = new Omie(1, 1);

        $body = [
            "call" => "ListarNF",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 500,
                "apenas_importado_api" => "N",
                "ordenar_por" => "CODIGO",
                "dEmiInicial" => "01/10/2019",
                "cDetalhesPedido" => "S",
                "tpNF" => "0",
                "cnpj_cpf" => "04238156000166"
            ]
        ];

        $responseOmie = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
        $response = (object) $responseOmie;

        $total_de_paginas = $response->body["total_de_paginas"];

        for ($x = 1; $x <= $total_de_paginas; $x++) {
            $body = [
                "call" => "ListarNF",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    "pagina" => $x,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => "01/10/2019",
                    "cDetalhesPedido" => "S",
                    "tpNF" => "0",
                    "cnpj_cpf" => "04238156000166"
                ]
            ];
            $responseOmie = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
            $response = (object) $responseOmie;

            $nota_fiscal = null;
            $produto_nf = null;

            if ($response->body) {

                if ($response->body["nfCadastro"]) {

                    foreach ($response->body["nfCadastro"] as $dadosNF) {

                        $nota_fiscal = NotaFiscal::findOne(['chave_nf' => $dadosNF["compl"]["cChaveNFe"]]);

                        if ($nota_fiscal) {
                            continue;
                        }

                        $nota_fiscal = new NotaFiscal();
                        $perc_nota = 70;

                        $nota_fiscal->chave_nf = $dadosNF["compl"]["cChaveNFe"];
                        $nota_fiscal->valor_nf = $dadosNF['total']['ICMSTot']['vNF'];
                        $nota_fiscal->data_nf = $dadosNF['ide']['dEmi'];
                        $nota_fiscal->id_nf = $dadosNF["compl"]["nIdNF"];
                        $nota_fiscal->id_pedido = $dadosNF["compl"]["nIdPedido"];
                        $nota_fiscal->id_recebimento = $dadosNF['compl']['nIdReceb'];
                        $nota_fiscal->id_transportadora = $dadosNF['compl']['nIdTransp'];
                        $nota_fiscal->modo_frete = $dadosNF['compl']['cModFrete'];
                        $nota_fiscal->data_cancelamento = $dadosNF['ide']['dCan'];
                        $nota_fiscal->data_emissao = $dadosNF['ide']['dEmi'];
                        $nota_fiscal->data_inutilizacao = $dadosNF['ide']['dInut'];
                        $nota_fiscal->data_registro = $dadosNF['ide']['dReg'];
                        $nota_fiscal->data_saida = $dadosNF['ide']['dSaiEnt'];
                        $nota_fiscal->finalidade_emissao = $dadosNF['ide']['finNFe'];
                        $nota_fiscal->tipo_nf = $dadosNF['ide']['tpNF'];
                        $nota_fiscal->tipo_ambiente = $dadosNF['ide']['tpAmb'];
                        $nota_fiscal->serie = $dadosNF['ide']['serie'];
                        $nota_fiscal->codigo_modelo = $dadosNF['ide']['mod'];
                        $nota_fiscal->indice_pagamento = $dadosNF['ide']['indPag'];
                        $nota_fiscal->h_saida_entrada_nf = $dadosNF['ide']['hSaiEnt'];
                        $nota_fiscal->h_emissao = $dadosNF['ide']['hEmi'];
                        $nota_fiscal->cod_int_empresa = $dadosNF['nfEmitInt']['cCodEmpInt'];
                        $nota_fiscal->cod_empresa = $dadosNF['nfEmitInt']['nCodEmp'];
                        $nota_fiscal->cod_int_cliente_fornecedor = $dadosNF['nfDestInt']['cCodCliInt'];
                        $nota_fiscal->cod_cliente = $dadosNF['nfDestInt']['nCodCli'];
                        $nota_fiscal->numero_nf = $dadosNF["ide"]["nNF"];
                        $nota_fiscal->e_validada = false;

                        $body = [
                            "call" => "ConsultarCliente",
                            "app_key" => '468080198586',
                            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                            "param" => [
                                "codigo_cliente_omie" => $nota_fiscal->cod_cliente,
                            ]
                        ];
                        $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
                        $response = (object) $responseOmie;
                        echo $response->body['codigo_cliente_omie'];

                        if ($response->body) {
                            $nota_fiscal->fornecedor = $response->body['razao_social'];
                        }

                        if ($nota_fiscal->save(false)) {
                            echo $nota_fiscal->numero_nf . " - " . $nota_fiscal->data_nf;
                        }

                        foreach ($dadosNF["det"] as $produto) {

                            $produto_nf = new NotaFiscalProduto();

                            $produto_nf->nota_fiscal_id = $nota_fiscal->id;
                            $produto_nf->descricao = $produto["prod"]["xProd"];
                            $produto_nf->valor_produto = $produto["prod"]["vProd"];
                            $produto_nf->codigo_produto = $produto["prod"]["cProd"];
                            $produto_nf->cod_int_item = $produto["nfProdInt"]["cCodItemInt"];
                            $produto_nf->cod_int_produto = $produto["nfProdInt"]["cCodProdInt"];
                            $produto_nf->cod_item = $produto["nfProdInt"]["nCodItem"];
                            $produto_nf->cod_produto = $produto["nfProdInt"]["nCodProd"];
                            $produto_nf->cod_fiscal_operacao_servico = $produto["prod"]["CFOP"];
                            $produto_nf->cod_situacao_tributaria_icms = $produto["prod"]["EXTIPI"];
                            $produto_nf->cod_ncm = $produto["prod"]["NCM"];
                            $produto_nf->ean = $produto["prod"]["cEAN"];

                            if (isset($produto["prod"]["cEANTrib"])) {
                                $produto_nf->ean_tributável = $produto["prod"]["cEANTrib"];
                            }
                            $produto_nf->codigo_produto_original = $produto["prod"]["cProdOrig"];
                            $produto_nf->codigo_local_estoque = $produto["prod"]["codigo_local_estoque"];
                            $produto_nf->cmc_total = $produto["prod"]["nCMCTotal"];
                            $produto_nf->cmc_unitario = $produto["prod"]["nCMCUnitario"];

                            if (isset($produto["prod"]["pICMS"])) {
                                $produto_nf->aliquota_icms = $produto["prod"]["pICMS"];
                            }

                            $produto_nf->qtd_comercial = $produto["prod"]["qCom"];
                            $produto_nf->qtd_tributavel = $produto["prod"]["qTrib"];
                            $produto_nf->unid_tributavel = $produto["prod"]["uCom"];
                            $produto_nf->valor_desconto = $produto["prod"]["vDesc"];
                            $produto_nf->valor_total_frete = $produto["prod"]["vFrete"];
                            if (isset($produto["prod"]["vIPI"])) {
                                $produto_nf->valor_ipi = $produto["prod"]["vIPI"];
                            } else {
                                $produto_nf->valor_ipi = 0;
                            }

                            if (isset($produto["prod"]["vICMSST"])) {
                                $produto_nf->valor_icms = $produto["prod"]["vICMSST"];
                            } else {
                                $produto_nf->valor_icms = 0;
                            }

                            $produto_nf->outras_despesas = $produto["prod"]["vOutro"];
                            $produto_nf->valor_seguro = $produto["prod"]["vSeg"];
                            $produto_nf->valor_unitario_tributacao = $produto["prod"]["vUnCom"];
                            $produto_nf->descricao_original = $produto["prod"]["xProdOrig"];

                            $imposto = ($produto_nf->valor_icms + $produto_nf->valor_ipi + $produto_nf->valor_seguro + $produto_nf->valor_total_frete + $produto_nf->outras_despesas) - $produto_nf->valor_desconto;
                            $imposto = $imposto / $produto_nf->qtd_comercial;
                            $produto_nf->valor_real_produto = ($produto_nf->valor_unitario_tributacao * 100) / $perc_nota + $imposto;

                            $produtoPA = [
                                "call" => "ConsultarProduto",
                                "app_key" => '468080198586',
                                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                                "param" => [
                                    "codigo_produto" => $produto_nf->cod_produto
                                ]
                            ];

                            $respOmie = $omie->consulta("/api/v1/geral/produtos/?JSON=", $produtoPA);
                            $resp = (object) $respOmie;

                            if ($resp->body) {
                                if (isset($resp->body["codigo_produto_integracao"])) {
                                    $produto_nf->pa_produto = $resp->body["codigo_produto_integracao"];
                                }
                            }

                            if ($produto_nf->save(false)) {
                                echo '      ' . $produto_nf->descricao . '    PA ' . $produto_nf->pa_produto . "\n";
                            }
                        }
                    }
                } else {
                    echo "não existe dados da nota";
                }
            }
        }
    }
}
