<?php

namespace console\controllers\actions\omie;

use backend\controllers\ValorProdutoFilialController;
use backend\models\NotaFiscal;
use backend\models\NotaFiscalProduto;
use backend\models\PedidoCompra;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use yii\base\Action;
use console\controllers\actions\omie\Omie;
use Yii;

class ImportacaoNotasOmieAction extends Action
{
    public function run()
    {
        echo "INÍCIO da rotina de importação de Notas: \n\n";

        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
        ];

        foreach ($acessoOmie as $key => $value) {

            $APP_KEY_OMIE            = $key;
            $APP_SECRET_OMIE         = $value;

            $omie = new Omie(1, 1);

            $body = [
                "call" => "ListarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "pagina" => 1,
                    "registros_por_pagina" => 500,
                    "apenas_importado_api" => "N",
                    "ordenar_por" => "CODIGO",
                    "dEmiInicial" => "13/12/2021",//date('d/m/Y'),
		    "dEmiFinal" => "14/12/2021",//date('d/m/Y'),
		    //"dEmiInicial" => date('d/m/Y'),
                    //"dEmiFinal" => date('d/m/Y'),
                    "cDetalhesPedido" => "S"
                ]
            ];

            $responseOmie = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
            $response = (object) $responseOmie;

            $total_de_paginas = $response->body["total_de_paginas"];

            for ($x = 1; $x <= $total_de_paginas; $x++) {
//echo "(((((".$x.")))))";
                $body = [
                    "call" => "ListarNF",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "pagina" => $x,
                        "registros_por_pagina" => 500,
                        "apenas_importado_api" => "N",
                        "ordenar_por" => "CODIGO",
			"dEmiInicial" => "13/12/2021",//date('d/m/Y'),
                    	"dEmiFinal" => "14/12/2021",//date('d/m/Y'),
			//"dEmiInicial" => date('d/m/Y'),
                        //"dEmiFinal" => date('d/m/Y'),
                        "cDetalhesPedido" => "S"
                    ]
                ];

                $responseOmie = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
                $response = (object) $responseOmie;
		//print_r($response); die;

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
                            $perc_nota = 100;

                            $nota_fiscal->chave_nf              = $dadosNF["compl"]["cChaveNFe"];
                            $nota_fiscal->valor_nf              = $dadosNF['total']['ICMSTot']['vNF'];
                            $nota_fiscal->data_nf               = $dadosNF['ide']['dEmi'];
                            $nota_fiscal->id_nf                 = $dadosNF["compl"]["nIdNF"];
                            $nota_fiscal->id_pedido             = $dadosNF["compl"]["nIdPedido"];
                            $nota_fiscal->id_recebimento        = $dadosNF['compl']['nIdReceb'];
                            $nota_fiscal->id_transportadora     = $dadosNF['compl']['nIdTransp'];
                            $nota_fiscal->modo_frete            = $dadosNF['compl']['cModFrete'];
                            $nota_fiscal->data_cancelamento     = $dadosNF['ide']['dCan'];
                            $nota_fiscal->data_emissao          = $dadosNF['ide']['dEmi'];
                            $nota_fiscal->data_inutilizacao     = $dadosNF['ide']['dInut'];
                            $nota_fiscal->data_registro         = $dadosNF['ide']['dReg'];
                            $nota_fiscal->data_saida            = $dadosNF['ide']['dSaiEnt'];
                            $nota_fiscal->finalidade_emissao    = $dadosNF['ide']['finNFe'];
                            $nota_fiscal->tipo_nf               = $dadosNF['ide']['tpNF'];
                            $nota_fiscal->tipo_ambiente         = $dadosNF['ide']['tpAmb'];
                            $nota_fiscal->serie                 = $dadosNF['ide']['serie'];
                            $nota_fiscal->codigo_modelo         = $dadosNF['ide']['mod'];
                            $nota_fiscal->indice_pagamento      = $dadosNF['ide']['indPag'];
                            $nota_fiscal->h_saida_entrada_nf    = $dadosNF['ide']['hSaiEnt'];
                            $nota_fiscal->h_emissao             = $dadosNF['ide']['hEmi'];
                            $nota_fiscal->cod_int_empresa       = $dadosNF['nfEmitInt']['cCodEmpInt'];
                            $nota_fiscal->cod_empresa           = $dadosNF['nfEmitInt']['nCodEmp'];
                            $nota_fiscal->cod_int_cliente_fornecedor = $dadosNF['nfDestInt']['cCodCliInt'];
                            $nota_fiscal->cod_cliente           = $dadosNF['nfDestInt']['nCodCli'];
                            $nota_fiscal->numero_nf             = $dadosNF["ide"]["nNF"];
                            $nota_fiscal->e_validada            = false;

                            $body = [
                                "call" => "ConsultarCliente",
                                "app_key" => $APP_KEY_OMIE,
                                "app_secret" => $APP_SECRET_OMIE,
                                "param" => [
                                    "codigo_cliente_omie" => $nota_fiscal->cod_cliente,
                                ]
                            ];

                            $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);
                            $response = (object) $responseOmie;

                            if ($response->body) {
                                $nota_fiscal->fornecedor = $response->body['razao_social'];
                            }

                            if ($nota_fiscal->save(false)) {
                                echo $nota_fiscal->numero_nf;
                            }

                            switch ($nota_fiscal->fornecedor) {
                                case 'KARTER LUBRIFICANTES LTDA':
                                    $perc_nota = 70;
                                    break;
                                case 'JGE PRODUTOS REFRIGERADOS EIRELI - ME':
                                    $perc_nota = 50;
                                    break;
                                case 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA':
                                case 'LEFS COMERCIO IMPORTACAO E EXPORTACAO DE AUTOPECAS LTDA':
                                case 'GENERAL TRUCK DISTRIBUIDORA DE AUTO PECAS LTDA - EPP':
                                    $perc_nota = 30;
                                    break;
                                case 'RODOPLAST INDUSTRIA E COMERCIO DE COMPONENTES PLASTICOS LTDA':
                                    if ($nota_fiscal->cod_cliente == '2643204227') {
                                        $perc_nota = 25;
                                    }
                                    break;
                                case 'HARDEN COMERCIO DE EXPORTACAO E IMPORTACAO LTDA ME':
                                    $perc_nota = 25.77;
                                    break;
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
                                }

                                if (isset($produto["prod"]["vICMSST"])) {
                                    $produto_nf->valor_icms = $produto["prod"]["vICMSST"];
                                }

                                $produto_nf->outras_despesas = $produto["prod"]["vOutro"];
                                $produto_nf->valor_seguro = $produto["prod"]["vSeg"];
                                $produto_nf->valor_unitario_tributacao = $produto["prod"]["vUnCom"];
                                $produto_nf->descricao_original = $produto["prod"]["xProdOrig"];

                                if ($nota_fiscal->fornecedor == 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA') {

                                    $add_assis = $produto_nf->valor_unitario_tributacao * 0.08;
                                    $produto_nf->valor_real_produto = (($produto_nf->valor_unitario_tributacao * 100) / $perc_nota) + $add_assis;
                                } else {
                                    $imposto = ($produto_nf->valor_icms + $produto_nf->valor_ipi + $produto_nf->valor_seguro + $produto_nf->valor_total_frete + $produto_nf->outras_despesas) - $produto_nf->valor_desconto;
                                    $imposto = $imposto / $produto_nf->qtd_comercial;
                                    $produto_nf->valor_real_produto = ($produto_nf->valor_unitario_tributacao * 100) / $perc_nota + $imposto;
                                }


                                $produtoPA = [
                                    "call" => "ConsultarProduto",
                                    "app_key" => $APP_KEY_OMIE,
                                    "app_secret" => $APP_SECRET_OMIE,
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
                                    echo $produto_nf->descricao . ' PA ' . $produto_nf->pa_produto . "\n";
                                }

                                if (
                                    $nota_fiscal->finalidade_emissao != 4
                                    && $nota_fiscal->tipo_nf == 0
                                    && ($nota_fiscal->cod_cliente != 533903617 || $nota_fiscal->cod_cliente != 1018587858 ||
                                        $nota_fiscal->cod_cliente != 1813680761 || $nota_fiscal->cod_cliente != 2641483458
                                        || $nota_fiscal->cod_cliente != 3028165450)
                                ) {

                                    $produto_id = str_replace('PA', '', $produto_nf->pa_produto);
                                    $produto_bloqueado = Produto::findOne($produto_id)->e_valor_bloqueado;

                                    if ($produto_bloqueado !== true) {

                                        $filial = 0;

                                        switch ($key) {
                                            case '468080198586':
                                                $filial = 96;
                                                break;
                                            case '1017311982687':
                                                $filial = 95;
                                                break;
                                            case '469728530271':
                                                $filial = 94;
                                                break;
                                            case '1758907907757':
                                                $filial = 93;
                                                break;
                                        }

                                        $produto_filial = ProdutoFilial::find()->where("produto_id = $produto_id and filial_id = $filial")->one();

                                        if (empty($produto_filial)) {
                                            $produto_filial = new ProdutoFilial();
                                            $produto_filial->produto_id = $produto_id;
                                            $produto_filial->filial_id  = $filial;
                                            $produto_filial->quantidade = $produto_nf->qtd_comercial;
                                            $produto_filial->envio      = 1;
                                            $produto_filial->save();
                                        }

                                        $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                                            inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                                            where ($produto_nf->valor_real_produto ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                                            order by mm.id desc 
                                            limit 1")->queryScalar();

                                        if ($filial == 96 && $nota_fiscal->fornecedor == 'ZAPPAROLI IND E COM DE PLASTICOS LTDA') {
                                            $markup = 1.85;
                                        }

                                        if ($nota_fiscal->fornecedor == 'KARTER LUBRIFICANTES LTDA' || $nota_fiscal->fornecedor == 'BRIDA LUBRIFICANTES LTDA') {
                                            $markup = 1.82;
                                        }

                                        $modelValorProdutoFilial = new ValorProdutoFilial();
                                        $modelValorProdutoFilial->valor = $markup < 5 ? $produto_nf->valor_real_produto * $markup : $markup;
                                        $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                                        $modelValorProdutoFilial->produto_filial_id = $produto_filial->id;
                                        $modelValorProdutoFilial->promocao = false;
                                        $modelValorProdutoFilial->valor_compra = $produto_nf->valor_real_produto;
                                        $modelValorProdutoFilial->save();
                                        ValorProdutoFilialController::AtualizarValorProdutoFilial($modelValorProdutoFilial);
                                    }
                                }
                            }

                            if ($nota_fiscal->finalidade_emissao != 4 && $nota_fiscal->tipo_nf == 0) {

                                if (
                                    $nota_fiscal->fornecedor !== 'BR COMPANY' &&
                                    $nota_fiscal->fornecedor !== 'MORELATE DISTR DE AUTO PECAS LTDA' &&
                                    $nota_fiscal->fornecedor !== 'VANNUCCI IMPORTACAO, EXPORTACAO E COM. PECAS LTDA' &&
                                    $nota_fiscal->fornecedor !== 'MSAM Distribuidora de Pecas Ltda' &&
                                    $nota_fiscal->fornecedor !== 'ANCHIETA PECAS DISTR DE PCS P CAM E ONIBUS EIRELI'
                                ) {
                                    NotaFiscal::EmailNotaFiscalEntrada($nota_fiscal->id);
                                }

                                switch ($nota_fiscal->fornecedor) {

                                    case 'KARTER LUBRIFICANTES LTDA':
                                    case 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA':
                                    case 'LEFS COMERCIO IMPORTACAO E EXPORTACAO DE AUTOPECAS LTDA':
                                    case 'HARDEN COMERCIO DE EXPORTACAO E IMPORTACAO LTDA ME':
                                    case 'LOTTO AUTOMOTIVE COMERCIO DE AUTO PECAS LTDA':
                                    case 'ZAPPAROLI IND E COM DE PLASTICOS LTDA':
                                    case 'ROMANAPLAST INDUSTRIA E COMERCIO EIRELI':
                                    case 'F.CONFUORTO IND. COM. PECAS ACES. LTDA':
                                    case 'VERLI INDUSTRIA MECANICA LTDA':
                                    case 'Globo Ind. e Com. de Pecas Ltda':
                                    case 'EDVALDO JOSE DOS SANTOS TAPECARIA ME':
                                    case 'Mlc Industria e Comercio de Acess Para Automoveis Eireli Epp':
                                    case 'FRISART IND. E COM. DE ACESSORIOS P/ AUTOS EIRELI':
                                    case 'RODOPLAST INDUSTRIA E COMERCIO DE COMPONENTES PLASTICOS LTDA':
                                    case 'KM BRASIL AUTO PECAS - EIRELI':
                                    case 'IRMAOS AMALCABURIO LTDA':
                                    case 'Fibracel pecas para veiculos Ltda':
                                    case 'CIPEC INDUSTRIAL DE AUTOPECAS LTDA':
                                    case 'BL Fibras Ltda':
                                    case 'SPG COMPONENTES AUTOMOTIVOS LTDA':
                                    case 'FLEX AUTOMOTIVA LTDA':
                                    case 'GENERAL TRUCK DISTRIBUIDORA DE AUTO PECAS LTDA - EPP':
                                    case 'GENERAL TRUCK PR DISTRIBUIDORA DE AUTOPECAS LTDA':
                                    case 'BRIDA LUBRIFICANTES LTDA':
                                    case 'BZ AUTOMOTIVE LTDA':
                                        PedidoCompra::CriarPedidoCompras($nota_fiscal->chave_nf, $perc_nota);
                                        break;
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
}
