<?php
//1111
namespace backend\functions;

use backend\models\Administrador;
use backend\models\ContaCorrente;
use backend\models\PedidoProdutoFilialCotacao;
use common\models\Comprador;
use common\models\Empresa;
use common\models\EnderecoEmpresa;
use common\models\Pedido;
use common\models\PedidoProdutoFilial;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\Transportadora;
use console\controllers\actions\omie\Omie;

class FunctionsOmie
{
    public static function CriarPedido($id)
    {

        $pedido = Pedido::findOne($id);
        $pedido_produto_filial = PedidoProdutoFilial::findAll(['pedido_id' => $pedido->id]);
        $comprador = Comprador::findOne($pedido->comprador_id);
        $empresa = Empresa::findOne($comprador->empresa_id);
        $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
        $transportadora = Transportadora::findOne($pedido->transportadora_id);
        $administrador = Administrador::findOne($pedido->administrador_id);
        $conta = ContaCorrente::findOne($pedido->conta_corrente_id);
        if (!$transportadora) {
            return ['mensagem' =>  'Não foi identificado transportadora cadastrada no pedido !'];
        }

        $response = new Omie(1, 1);

        $APP_KEY_OMIE           = '';
        $APP_SECRET_OMIE        = '';
        $codigo_cenario_impostos = '';
        $tipo_pedido = $pedido->administrador_id ? 'PI' : 'PS';
        $tarifas = $pedido->administrador_id ? 0 : 0.69;
        $codVend = 0;
        $codigo_conta_corrente = 0;
        $codigo_cliente = 0;
        $data_prevista = $pedido->data_prevista;
        $etapa = "50";

        if (!$data_prevista) {
            $data_prevista = date('Y-m-d');
        }

        if ($pedido->filial_id == 94) {
            //Omie MG1
            $APP_KEY_OMIE       = '469728530271';
            $APP_SECRET_OMIE    = '6b63421c9bb3a124e012a6bb75ef4ace';
            $codigo_cenario_impostos    = "503038132";
            $codigo_conta_corrente = $conta ? $conta->codigo_conta_corrente_omie : 505881289;
            $codVend = isset($administrador->codigo_omie_mg) ? $administrador->codigo_omie_mg : 507230538;
            $etapa = "10";
        } else if ($pedido->filial_id == 95) {
            //Omie SP3
            $APP_KEY_OMIE       = '1017311982687';
            $APP_SECRET_OMIE    = '78ba33370fac6178da52d42240591291';
            $codigo_cenario_impostos    = "1018251055";
            $codigo_conta_corrente = $conta ? $conta->codigo_conta_corrente_omie : 1025868316;
            $codVend = isset($administrador->codigo_omie_filial) ? $administrador->codigo_omie_filial : 1025868316;
        } else if ($pedido->filial_id == 96) {
            //Omie SP
            $APP_KEY_OMIE       = '468080198586';
            $APP_SECRET_OMIE    = '7b3fb2b3bae35eca3b051b825b6d9f43';
            $codigo_cenario_impostos    = "500712977";
            $codigo_conta_corrente = $conta ? $conta->codigo_conta_corrente_omie : 500308502;
            $codVend = isset($administrador->codigo_omie_sp) ? $administrador->codigo_omie_sp : 500726372;
        } else {
            $APP_KEY_OMIE       = '1758907907757';
            $APP_SECRET_OMIE    = '0a69c9b49e5a188e5f43d5505f2752bc';
            $codigo_cenario_impostos    = "2388479664";
            $codigo_conta_corrente = $conta ? $conta->codigo_conta_corrente_omie : 2389614087;
            $codVend = isset($administrador->codigo_omie_mg_vendas) ? $administrador->codigo_omie_mg_vendas : 2388488427;
            $etapa = "10";
        }

        $body = [
            "call" => "ListarClientes",
            "app_key" => $APP_KEY_OMIE,
            "app_secret" => $APP_SECRET_OMIE,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 1,
                "apenas_importado_api" => "N",
                "clientesFiltro" => [
                    "cnpj_cpf" => $empresa->documento,
                ]
            ]
        ];

        $response_omie = $response->consulta("/api/v1/geral/clientes/?JSON=", $body);
       

        if ($response_omie['httpCode'] == 200) {

            $codigo_cliente = $response_omie['body']['clientes_cadastro'][0]['codigo_cliente_omie'];
            $inscricao_estadual = $response_omie['body']['clientes_cadastro'][0]['inscricao_estadual'];
            $body = [
                "call" => "AlterarCliente",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_cliente_omie"       => $codigo_cliente,
                    "nome_fantasia"             => $empresa->nome,
                    "telefone1_ddd"             => substr($empresa->telefone, 0, 2),
                    "telefone1_numero"          => substr($empresa->telefone, 2, 9),
                    "contato"                   => $empresa->nome,
                    "endereco"                  => $endEmpresa->logradouro,
                    "endereco_numero"           => $endEmpresa->numero,
                    "bairro"                    => $endEmpresa->bairro,
                    "complemento"               => $endEmpresa->complemento,
                    "estado"                    => $endEmpresa->cidade->estado->sigla,
                    "cidade"                    => $endEmpresa->cidade->nome,
                    "cep"                       => $endEmpresa->cep,
                    "email"                     => $empresa->email
                ]
            ];

            $response_omie = $response->consulta("/api/v1/geral/clientes/?JSON=", $body);            

            if ($response_omie['httpCode'] !== 200) {
                return ['mensagem' =>  $response_omie['body']['faultstring']];
            }
        } else {
            $body = [
                "call" => "IncluirCliente",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_cliente_integracao" => $comprador->id,
                    "razao_social"              => $empresa->nome,
                    "cnpj_cpf"                  => $empresa->documento,
                    "nome_fantasia"             => $empresa->nome,
                    "telefone1_ddd"             => substr($empresa->telefone, 0, 2),
                    "telefone1_numero"          => substr($empresa->telefone, 2, 9),
                    "contato"                   => $empresa->nome,
                    "endereco"                  => $endEmpresa->logradouro,
                    "endereco_numero"           => $endEmpresa->numero,
                    "bairro"                    => $endEmpresa->bairro,
                    "complemento"               => $endEmpresa->complemento,
                    "estado"                    => $endEmpresa->cidade->estado->sigla,
                    "cidade"                    => $endEmpresa->cidade->nome,
                    "cep"                       => $endEmpresa->cep,
                    "email"                     => $empresa->email
                ]
            ];

            $response_omie = $response->cria_cliente("api/v1/geral/clientes/?JSON=", $body);           

            if ($response_omie['httpCode'] !== 200) {
                return ['mensagem' =>  $response_omie['body']['faultstring']];
            } else {
                $codigo_cliente = $response_omie['body']['codigo_cliente_omie'];

                $body = [
                    "call" => "ConsultarCliente",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "codigo_cliente_omie" => $codigo_cliente,
                    ]
                ];

                $response_omie = $response->consulta("/api/v1/geral/clientes/?JSON=", $body);
              

                if ($response_omie['httpCode'] !== 200) {
                    return ['mensagem' =>  $response_omie['body']['faultstring']];
                } else {
                    $inscricao_estadual = $response_omie['body']['codigo_cliente_omie'];
                }
            }
        }

        $callOmie = 'IncluirPedido';
        $codigo_integracao = $tipo_pedido . $pedido->id;
        $codigo_pedido = '';

        if ($pedido->codigo_pedido_omie) {

            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido" => $pedido->codigo_pedido_omie,
                ]
            ];

            $response_omie = $response->consulta_pedido("api/v1/geral/pedidos/?JSON=", $body);


            if ($response_omie['httpCode'] == 200) {
                $callOmie = 'AlterarPedidoVenda';
                $codigo_pedido = $pedido->codigo_pedido_omie;
                $codigo_integracao = isset($response_omie['pedido_venda_produto']['cabecalho']['codigo_pedido_integracao']) ? $response_omie['pedido_venda_produto']['cabecalho']['codigo_pedido_integracao'] : '';
            } else {
                $pedido->codigo_pedido_omie = null;
                $pedido->save(false);
            }
        }

        $qtd_itens = 0;
        $det = [];

        foreach ($pedido_produto_filial as $k => $pedidoProduto) {
            $qtd_itens++;
        }

        $frete = $pedido->valor_frete / $qtd_itens;

        foreach ($pedido_produto_filial as $k => $pedidoProduto) {

            $pedidoProdutoCotacao = PedidoProdutoFilialCotacao::findAll(['pedido_produto_filial_id' => $pedidoProduto->id]);

            foreach ($pedidoProdutoCotacao as $produtoCotacao) {

                $qtd_itens++;
                $produtoFilial = ProdutoFilial::findOne($produtoCotacao->produto_filial_id);
                $produto_dados = Produto::findOne(['id' => $produtoFilial->produto_id]);

                $body = [
                    "call" => "ConsultarProduto",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "codigo" => "PA" . (string)$produto_dados->id,
                    ]
                ];

                $response_omie = $response->consulta_produto("api/v1/geral/produtos/?JSON=", $body);

                if ($response_omie["httpCode"] !== 200) {
                    return ['mensagem' => $response_omie['body']['faultstring']];
                }

                $codigo_produto = $response_omie["body"]["codigo_produto"];

                $aliquota_interestadual_mg = ['PR', 'RS', 'RJ', 'SP', 'SC'];
                $aliquota_interestadual_sp = ['PR', 'RS', 'RJ', 'MG', 'SC'];

                $aliquota_uf_destino = [
                    'AC' => 0.17, 'AL' => 0.17, 'AM' => 0.18, 'AP' => 0.18, 'BA' => 0.18, 'CE' => 0.18, 'DF' => 0.18, 'ES' => 0.17,
                    'GO' => 0.17, 'MA' => 0.18, 'MS' => 0.17, 'MT' => 0.17, 'MG' => 0.18, 'SP' => 0.18, 'RJ' => 0.18, 'SC' => 0.17, 'TO' => 0.18, 'SE' => 0.18, 'RR' => 0.17,
                    'RO' => 0.175, 'RS' => 0.175, 'RN' => 0.18, 'PI' => 0.17, 'PR' => 0.18, 'PB' => 0.18, 'PE' => 0.18, 'PA' => 0.17
                ];

                $perc_fcp = 0;

                if ($endEmpresa->cidade->estado->sigla == "AL" || $endEmpresa->cidade->estado->sigla == "PI") {
                    $perc_fcp = 0.01;
                }

                if ($endEmpresa->cidade->estado->sigla == "RJ") {
                    $perc_fcp = 0.02;
                }

                $cfop = '5405';
                $perc_aliquota = 0.18;
                $cst = '00';

                if ($pedido->filial_id == 95 || $pedido->filial_id == 96) {
                    if ($endEmpresa->cidade->estado->sigla !== "SP") {

                        if (in_array($endEmpresa->cidade->estado->sigla, $aliquota_interestadual_sp)) {
                            $perc_aliquota = 0.12;
                        } else {
                            $perc_aliquota = 0.07;
                        }

                        if (strlen($empresa->documento) > 11) {
                            if ($inscricao_estadual !== '') {
                                $cfop = '6102';
                            } else {
                                $cfop = '6108';
                            }
                        } else {
                            $cfop = '6108';
                        }
                    } else {
                        $cst = '60';
                        $cfop = '5405';
                    }
                } else {

                    if ($pedido->filial_id == 93 && $endEmpresa->cidade->estado->sigla == "MG") {
                        $cst = '00';
                        $cfop = '5102';
                    } else if ($pedido->filial_id == 94 && $endEmpresa->cidade->estado->sigla == "MG") {
                        $cst = '60';
                        $cfop = '5405';
                    } else {
                        if (in_array($endEmpresa->cidade->estado->sigla, $aliquota_interestadual_mg)) {
                            $perc_aliquota = 0.12;
                        } else {
                            $perc_aliquota = 0.07;
                        }

                        if (strlen($empresa->documento) > 11) {
                            if ($inscricao_estadual !== '') {
                                $cfop = '6102';
                            } else {
                                $cfop = '6108';
                            }
                        } else {
                            $cfop = '6108';
                        }
                    }
                }

                $pis_cofins = self::GerarPisCofins($produto_dados);

                $cofins = null;
                $pis = null;
                $bc_icms = 0;
                $valor_icms = 0;
                $difal = $aliquota_uf_destino[$endEmpresa->cidade->estado->sigla] - $perc_aliquota;

                if ($pedido->filial_id == 95 || $pedido->filial_id == 96) {
                    if ($endEmpresa->cidade->estado->sigla !== "SP") {
                        $bc_icms = $pedidoProduto->valor * $pedidoProduto->quantidade;
                        $valor_icms = ($pedidoProduto->valor * $pedidoProduto->quantidade) * $perc_aliquota;
                    }
                } else {
                    $bc_icms = ($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas;
                    $valor_icms = (($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas) * $perc_aliquota;
                }

                $base_icms_uf_destino = 0;
                $aliq_icms_FCP = 0;
                $aliq_interna_uf_destino = 0;
                $aliq_interestadual = 0;
                $valor_fcp_icms_inter = 0;
                $valor_icms_uf_remet = 0;
                $valor_icms_uf_dest = 0;

                if ($cfop == '6108') {
                    $base_icms_uf_destino = $bc_icms;
                    $aliq_icms_FCP = $perc_fcp * 100;
                    $aliq_interna_uf_destino = $aliquota_uf_destino[$endEmpresa->cidade->estado->sigla] * 100;
                    $aliq_interestadual = $perc_aliquota * 100;
                    $valor_fcp_icms_inter = $bc_icms * $perc_fcp;
                    $valor_icms_uf_dest = $bc_icms * $difal;
                    $valor_icms_uf_remet = 0;
                }

                $base_cofins = 0;
                $base_pis = 0;

                if ($bc_icms > 0) {
                    $base_cofins = ($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas - $valor_icms;
                    $base_pis = ($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas - $valor_icms;
                } else {
                    $base_cofins = ($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas;
                    $base_pis = ($pedidoProduto->valor * $pedidoProduto->quantidade) + $frete + $tarifas;
                }

                if ($pis_cofins == '01') {
                    $cofins = [
                        "aliq_cofins" => 3,
                        "base_cofins" => $base_cofins,
                        "cod_sit_trib_cofins" => $pis_cofins,
                        "qtde_unid_trib_cofins" => $pedidoProduto->quantidade,
                        "tipo_calculo_cofins" => "B",
                        "valor_cofins" => $base_cofins *  0.03,
                        "valor_unid_trib_cofins" => 0,
                    ];
                    $pis = [
                        "aliq_pis" => 0.65,
                        "base_pis" => $base_pis,
                        "cod_sit_trib_pis" => $pis_cofins,
                        "qtde_unid_trib_pis" => $pedidoProduto->quantidade,
                        "tipo_calculo_pis" => "B",
                        "valor_pis" => $base_pis * 0.0065,
                        "valor_unid_trib_pis" => 0,
                    ];
                } else {
                    $cofins = [
                        "aliq_cofins" => 0,
                        "base_cofins" => 0,
                        "cod_sit_trib_cofins" => $pis_cofins,
                        "qtde_unid_trib_cofins" => 0,
                        "tipo_calculo_cofins" => "",
                        "valor_cofins" => 0,
                        "valor_unid_trib_cofins" => 0,
                    ];
                    $pis = [
                        "aliq_pis" => 0,
                        "base_pis" => 0,
                        "cod_sit_trib_pis" => $pis_cofins,
                        "qtde_unid_trib_pis" => 0,
                        "tipo_calculo_pis" => "",
                        "valor_pis" => 0,
                        "valor_unid_trib_pis" => 0,
                    ];
                }

                array_push($det, [
                    "ide" => [
                        "codigo_item_integracao"    => substr($produto_dados->codigo_global, 0, 20),
                        "simples_nacional" => "N"
                    ],
                    "imposto" => [
                        "ipi" => [
                            "cod_sit_trib_ipi" => "99",
                            "enquadramento_ipi" => "999",
                            "tipo_calculo_ipi" => "B",
                        ],
                        "pis_padrao" => $pis,
                        "cofins_padrao" => $cofins,
                        "icms" => [
                            "aliq_icms" => $perc_aliquota * 100,
                            "base_icms" => $bc_icms,
                            "cod_sit_trib_icms" => $cst,
                            "modalidade_icms" => "3",
                            "origem_icms" => "0",
                            "perc_red_base_icms" => 0,
                            "valor_icms" => $valor_icms,
                            "perc_fcp_icms" => 0,
                            "base_fcp_icms" => 0,
                            "valor_fcp_icms" => 0
                        ],
                        "icms_ie" => [
                            "base_icms_uf_destino" => $base_icms_uf_destino,
                            "aliq_icms_FCP" => $aliq_icms_FCP,
                            "aliq_interna_uf_destino" => $aliq_interna_uf_destino,
                            "aliq_interestadual" => $aliq_interestadual,
                            "valor_fcp_icms_inter" => $valor_fcp_icms_inter,
                            "valor_icms_uf_dest" => $valor_icms_uf_dest,
                            "valor_icms_uf_remet" => $valor_icms_uf_remet,
                        ],
                    ],
                    "produto" => [
                        "codigo_produto"            => $codigo_produto,
                        "cfop"                      => $cfop,
                        "quantidade"                => $pedidoProduto->quantidade,
                        "valor_unitario"            => $pedidoProduto->valor,
                    ],
                ]);
            }
        }

        $body = [
            "call" => $callOmie,
            "app_key" => $APP_KEY_OMIE,
            "app_secret" => $APP_SECRET_OMIE,
            "param" => [
                [
                    "cabecalho" => [
                        "bloqueado"                 => "N",
                        "codigo_cenario_impostos"   => $codigo_cenario_impostos,
                        "codigo_cliente"            => $codigo_cliente,
                        "codigo_pedido"             => $codigo_pedido,
                        "codigo_pedido_integracao"  => $codigo_integracao,
                        "etapa"                     => $etapa,
                        "data_previsao"             => substr($data_prevista, 8, 2) . '/' . substr($data_prevista, 5, 2) . '/' . substr($data_prevista, 0, 4),
                        "quantidade_itens"          => $qtd_itens
                    ],
                    "det" => $det,
                    "frete" => [
                        "codigo_transportadora" => $transportadora->codigo_omie,
                        "modalidade"            => $pedido->tipo_frete,
                        "quantidade_volumes"    => 1,
                        "especie_volumes"       => "CAIXA",
                        "valor_frete"           => $pedido->valor_frete,
                        "outras_despesas"       => $tarifas
                    ],
                    "informacoes_adicionais"    => [
                        "numero_contrato"           => '',
                        "numero_pedido_cliente"     => $pedido->id,
                        "consumidor_final"          => "S",
                        "codigo_categoria"          => "1.01.03",
                        "dados_adicionais_nf"       => $pedido->filial_id == 93 ? "EMPRESA SOB REGIME ESPECIAL No -PTA-RE No 45.000027189-79\nArt. 32: Mercadoria destinada a uso e consumo, vedado o aproveitamento do crédito nos termos do inciso III do art. 70 do RICMS" : "",
                        "codVend"                   => $codVend,
                        "codigo_conta_corrente"     => $codigo_conta_corrente,
                    ],
                ]
            ],
        ];

        $response_omie = $response->CriarPedido($body);

        // echo '<pre>'; print_r($response_omie); echo '</pre>'; die;

        if ($response_omie['httpCode'] == 200) {
            $pedido->codigo_pedido_omie = $response_omie['body']['codigo_pedido'];
            $pedido->save(false);
            return ['mensagem' => null, 'numero_pedido' => $response_omie['body']['numero_pedido']];
        } else {
            return ['mensagem' =>  $response_omie['body']['faultstring']];
        }
    }

    public static function GerarPisCofins($produto)
    {

        $codigos = [
            //Anexo 1
            40161010,
            40169990,
            6813,
            70071100,
            70072100,
            70091000,
            73201000,
            83012000,
            83023000,
            84073390,
            84073490,
            840820,
            840991,
            840999,
            841330,
            84139100,
            84148021,
            84148022,
            841520,
            84212300,
            84213100,
            84314100,
            84314200,
            84339090,
            848310,
            84832000,
            848330,
            848340,
            848350,
            850520,
            85071000,
            8511,
            851220,
            85123000,
            851240,
            85129000,
            85272,
            85365090,
            853910,
            85443000,
            870600,
            8707,
            8708,
            90292010,
            90299010,
            90303921,
            90318040,
            9032892,
            91040000,
            94012000,

            //Anexo 2
            8429,
            843320,
            84333000,
            84334000,
            84335,
            8701,
            8702,
            8703,
            8704,
            8705,
            8706,
            8431,
            84089090,
            84122110,
            84122190,
            84123110,
            87012000,
            8702,
            8704,
            84136019,
            84148019,
            84149039,
            84329000,
            84324000,
            84328000,
            84811000,
            84812090,
            84818092,
            8483601,
            85011019
        ];

        $cod_sit_trib_cofins = '01';
        foreach ($codigos as $k => $codigo) {

            $quantidade_caracteres = strlen($codigo);
            $ncm = str_replace('.', '', $produto->codigo_montadora);
            $sub_ncm = substr($ncm, 0, $quantidade_caracteres);

            if ($sub_ncm == $codigo) {
                $cod_sit_trib_cofins = '04';
                break;
            }
        }

        return $cod_sit_trib_cofins;
    }
}
