<?php
//8888

namespace common\models;

use common\models\PedidoMercadoLivre as ModelsPedidoMercadoLivre;
use Yii;
use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use console\controllers\actions\omie\Omie;
use yii\base\ErrorException;
use yii\helpers\Json;
use backend\functions\FunctionsML;
use common\models\PedidoMercadoLivreQuery as ModelsPedidoMercadoLivreQuery;

/**
 * Este é o model para a tabela "pedido_mercado_livre".
 *
 * @property integer $id
 * @property string $pedido_meli_id
 * @property string $date_created
 * @property string $date_closed
 * @property string $last_updated
 * @property string $total_amount
 * @property string $paid_amount
 * @property string $shipping_id
 * @property string $status
 * @property string $buyer_id
 * @property string $buyer_nickname
 * @property string $buyer_email
 * @property string $buyer_first_name
 * @property string $buyer_last_name
 * @property string $buyer_doc_type
 * @property string $buyer_doc_number
 * @property string $shipping_base_cost
 * @property string $shipping_status
 * @property string $shipping_substatus
 * @property string $shipping_date_created
 * @property string $shipping_last_updated
 * @property string $shipping_tracking_number
 * @property string $shipping_tracking_method
 * @property string $shipping_service_id
 * @property string $receiver_id
 * @property string $receiver_address_id
 * @property string $receiver_address_line
 * @property string $receiver_street_name
 * @property string $receiver_street_number
 * @property string $receiver_comment
 * @property string $receiver_zip_code
 * @property string $receiver_city_id
 * @property string $receiver_city_name
 * @property string $receiver_state_id
 * @property string $receiver_state_name
 * @property string $receiver_country_id
 * @property string $receiver_country_name
 * @property string $receiver_neighborhood_id
 * @property string $receiver_neighborhood_name
 * @property string $receiver_municipality_id
 * @property string $receiver_municipality_name
 * @property string $receiver_delivery_preference
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $shipping_option_id
 * @property string $shipping_option_shipping_method_id
 * @property string $shipping_option_name
 * @property string $shipping_option_list_cost
 * @property string $shipping_option_cost
 * @property string $shipping_option_delivery_type
 * @property string $user_id
 * @property boolean $e_pedido_autorizado
 * @property boolean $e_pedido_cancelado
 * @property boolean $e_nota_fiscal_anexada
 * @property boolean $e_pedido_faturado
 * @property string $email_texto
 * @property string $email_enderecos
 * @property string $pack_id
 * @property string $email_assunto
 * @property boolean $e_xml_subido
 * @property boolean $e_etiqueta_impressa
 * @property string $data_hora_autorizacao
 * @property string $data_hora_cancelamento
 * @property string $data_hora_envio
 * @property integer $autorizado_por
 * @property integer $cancelado_por
 * @property integer $enviado_por
 * @property integer $codigo_pedido_omie
 * @property integer $nota_fiscal_compra_id
 * @property boolean $e_pre_nota_impressa
 * @property integer $quantidade_impressoes_pre_nota
 * @property integer $filial_id
 * @property integer $transportadora_id
 * @property string $observacao
 *
 * @property PedidoMercadoLivrePagamento[] $pedidoMercadoLivrePagamentos
 * @property PedidoMercadoLivreProduto[] $pedidoMercadoLivreProdutos
 *
 * @author Unknown 15/06/2020
 */
class PedidoMercadoLivre extends \yii\db\ActiveRecord
{

    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public static function tableName()
    {
        return 'pedido_mercado_livre';
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function rules()
    {
        return [
            [['pedido_meli_id', 'total_amount'], 'required'],
            [['date_created', 'date_closed', 'last_updated', 'shipping_date_created', 'shipping_last_updated'], 'safe'],
            [['total_amount', 'paid_amount', 'shipping_base_cost', 'shipping_option_list_cost', 'shipping_option_cost'], 'number'],
            [['e_pedido_autorizado', 'e_pedido_cancelado', 'e_nota_fiscal_anexada', 'e_pedido_faturado', 'e_xml_subido', 'e_etiqueta_impressa'], 'boolean'],
            [['email_texto', 'email_assunto'], 'string'],
            [['comentario'], 'string'],
            [['pedido_meli_id', 'shipping_id', 'status', 'buyer_id', 'buyer_doc_type', 'shipping_service_id', 'receiver_id', 'receiver_address_id', 'receiver_street_number', 'receiver_zip_code', 'receiver_country_id', 'receiver_neighborhood_id', 'receiver_municipality_id', 'shipping_option_id', 'shipping_option_name', 'shipping_option_delivery_type', 'user_id'], 'string', 'max' => 20],
            [['buyer_nickname', 'buyer_email', 'buyer_first_name', 'buyer_last_name', 'receiver_address_line', 'receiver_street_name'], 'string', 'max' => 200],
            [['buyer_doc_number', 'shipping_tracking_number', 'receiver_state_name', 'receiver_country_name'], 'string', 'max' => 30],
            [['shipping_status'], 'string', 'max' => 40],
            [['shipping_substatus', 'receiver_comment', 'receiver_neighborhood_name'], 'string', 'max' => 100],
            [['shipping_tracking_method', 'receiver_city_id', 'receiver_state_id', 'receiver_municipality_name', 'receiver_delivery_preference'], 'string', 'max' => 50],
            [['receiver_city_name', 'receiver_name', 'pack_id'], 'string', 'max' => 60],
            [['receiver_phone'], 'string', 'max' => 15],
            [['filial_id', 'transportadora_id'], 'integer'],
            [['shipping_option_shipping_method_id'], 'string', 'max' => 10],
            [['email_enderecos'], 'string', 'max' => 400],
            [['observacao_envio'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 15/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_meli_id' => 'Pedido Meli ID',
            'date_created' => 'Date Created',
            'date_closed' => 'Date Closed',
            'last_updated' => 'Last Updated',
            'total_amount' => 'Total Amount',
            'paid_amount' => 'Paid Amount',
            'shipping_id' => 'Shipping ID',
            'status' => 'Status',
            'buyer_id' => 'Buyer ID',
            'buyer_nickname' => 'Buyer Nickname',
            'buyer_email' => 'Buyer Email',
            'buyer_first_name' => 'Nome',
            'buyer_last_name' => 'Sobrenome',
            'buyer_doc_type' => 'Tipo Doc',
            'buyer_doc_number' => 'Documento',
            'shipping_base_cost' => 'Shipping Base Cost',
            'shipping_status' => 'Status Envio',
            'shipping_substatus' => 'Shipping Substatus',
            'shipping_date_created' => 'Shipping Date Created',
            'shipping_last_updated' => 'Shipping Last Updated',
            'shipping_tracking_number' => 'Shipping Tracking Number',
            'shipping_tracking_method' => 'Shipping Tracking Method',
            'shipping_service_id' => 'Shipping Service ID',
            'receiver_id' => 'Receiver ID',
            'receiver_address_id' => 'Receiver Address ID',
            'receiver_address_line' => 'Receiver Address Line',
            'receiver_street_name' => 'Receiver Street Name',
            'receiver_street_number' => 'Receiver Street Number',
            'receiver_comment' => 'Receiver Comment',
            'receiver_zip_code' => 'Receiver Zip Code',
            'receiver_city_id' => 'Receiver City ID',
            'receiver_city_name' => 'Receiver City Name',
            'receiver_state_id' => 'Receiver State ID',
            'receiver_state_name' => 'Receiver State Name',
            'receiver_country_id' => 'Receiver Country ID',
            'receiver_country_name' => 'Receiver Country Name',
            'receiver_neighborhood_id' => 'Receiver Neighborhood ID',
            'receiver_neighborhood_name' => 'Receiver Neighborhood Name',
            'receiver_municipality_id' => 'Receiver Municipality ID',
            'receiver_municipality_name' => 'Receiver Municipality Name',
            'receiver_delivery_preference' => 'Receiver Delivery Preference',
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => 'Receiver Phone',
            'shipping_option_id' => 'Shipping Option ID',
            'shipping_option_shipping_method_id' => 'Shipping Option Shipping Method ID',
            'shipping_option_name' => 'Shipping Option Name',
            'shipping_option_list_cost' => 'Shipping Option List Cost',
            'shipping_option_cost' => 'Shipping Option Cost',
            'shipping_option_delivery_type' => 'Shipping Option Delivery Type',
            'user_id' => 'User ID',
            'e_pedido_autorizado' => 'E Pedido Autorizado',
            'e_pedido_cancelado' => 'E Pedido Cancelado',
            'e_nota_fiscal_anexada' => 'E Nota Fiscal Anexada',
            'e_pedido_faturado' => 'E Pedido Faturado',
            'email_texto' => 'Email Texto',
            'email_enderecos' => 'Email Enderecos',
            'comentario' => 'Comentario',
            'pack_id' => 'Pack ID',
            'e_pedido_cancelado' => 'E pedido cancelado',
            'email_assunto' => 'Email Assunto',
            'e_xml_subido' => "E XML Subido",
            'e_etiqueta_impressa' => "E etiqueta impressa",
            'shipping_id'    => 'Etiqueta',
            'filial_id'    => 'Filial ID',
            'observacao_envio'    => 'Observação',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
     */
    public function getPedidoMercadoLivrePagamentos()
    {
        return $this->hasMany(PedidoMercadoLivrePagamento::class, ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
     */
    public function getPedidoMercadoLivreProdutos()
    {
        return $this->hasMany(PedidoMercadoLivreProduto::class, ['pedido_mercado_livre_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
     */
    public static function find()
    {
        return new PedidoMercadoLivreQuery(get_called_class());
    }

    public static function baixarPedidoML($order, $e_html = false)
    {

        $status_retorno = "";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $contas_ml  = [
            [
                "descricao"             => "Conta Principal São Paulo",
                "nome_arquivo"          => "conta_principal_sao_paulo",
                "filial_id_referencia"  => 72,
            ],
            [
                "descricao"             => "Conta Duplicada São Paulo",
                "nome_arquivo"          => "conta_duplicada_sao_paulo",
                "filial_id_referencia"  => 98,
            ],
            [
                "descricao"             => "Conta Principal Minas Gerais",
                "nome_arquivo"          => "conta_principal_minas_gerais",
                "filial_id_referencia"  => 94,
            ]
        ];

        foreach ($contas_ml as $conta_ml) {
            $filial = Filial::find()->andWhere(['=', 'id', $conta_ml["filial_id_referencia"]])->one();
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            $meliAccessToken = $response->access_token;

            $user_id_array  = explode("-", $filial->refresh_token_meli);
            $user_id        = $user_id_array[2];

            $url_order = "/orders/" . $order;

            $response_order = $meli->get($url_order . "?access_token=" . $meliAccessToken);

            if ($response_order["httpCode"] < 300) {
                $url_belling_info = $url_order . "/billing_info?access_token=" . $meliAccessToken;
                $billing_info   = $meli->get($url_belling_info);
                print_r($url_belling_info);
                if ($billing_info['httpCode'] >= 204) {
                    $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
                    return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Sem informações do cliente</span>" : "\nSem informa    es do Cliente";
                }

                $status_retorno .= self::criarAlterarPedidoMercadoLivre($response_order, $billing_info, $e_html);
                $envio_dados = $meli->get("/shipments/" . ArrayHelper::getValue($response_order, 'body.shipping.id') . "?access_token=" . $meliAccessToken);
                if ($envio_dados['httpCode'] >= 300) {

                    $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
                    return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Sem informações de envio</span>" : "\nSem informa    es de envio";
                } else {
                    $status_retorno .= self::criarAlterarPedidoMercadoLivreEnvio($response_order, $billing_info, $envio_dados);
                }

                $status_retorno .= self::criarPedidoOmie($user_id, $response_order, $envio_dados, $billing_info, $e_html, $order);
                $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
                return $status_retorno;
            }
        }
    }

    public static function criarPedidoOmie($user_id, $response_order, $envio_dados, $billing_info, $e_html, $order)
    {

        $status_retorno = "";

        $omie = new Omie(1, 1);

        $app_key                    = "";
        $app_secret                 = "";
        $codigo_cenario_impostos    = "";
        $codigo_transportadora      = 0;
        $codVend                    = 0;
        $codigo_conta_corrente      = 0;

        $pedido_mercado_livre = PedidoMercadoLivre::findOne(['pedido_meli_id' => $order]);
        $filial_id = $pedido_mercado_livre->filial_id;
        $transportadora_id = $pedido_mercado_livre->transportadora_id;
        $transportadora = Transportadora::findOne($transportadora_id);

        if (!empty($filial_id)) {
            switch ($filial_id) {
                    //Conta principal - SP
                case "96":
                    $app_key                    = "468080198586";
                    $app_secret                 = "7b3fb2b3bae35eca3b051b825b6d9f43";
                    $codigo_cenario_impostos    = "500712977";
                    $codigo_transportadora      = $transportadora->codigo_omie;
                    $codVend                    = 500726231;
                    $codigo_conta_corrente      = 502875713;
                    break;
                    //Conta duplicada - SP
                case "95":
                    $app_key                    = "1017311982687";
                    $app_secret                 = "78ba33370fac6178da52d42240591291";
                    $codigo_cenario_impostos    = "1018251055";
                    $codigo_transportadora      = $transportadora->codigo_omie;
                    $codVend                    = 1018256043;
                    $codigo_conta_corrente      = 1018255531;
                    break;
                    //Conta principal - MG
                case "93":
                    $app_key                    = "1758907907757";
                    $app_secret                 = "0a69c9b49e5a188e5f43d5505f2752bc";
                    $codigo_cenario_impostos    = "2388479664";
                    $codigo_transportadora      = $transportadora->codigo_omie;
                    $codVend                    = 2388488707;
                    $codigo_conta_corrente      = 2404912790;
                    break;
                case "94":
                    $app_key                    = "469728530271";
                    $app_secret                 = "6b63421c9bb3a124e012a6bb75ef4ace";
                    $codigo_cenario_impostos    = "503038132";
                    $codigo_transportadora      = $transportadora->codigo_omie;
                    $codVend                    = 738093586;
                    $codigo_conta_corrente      = 740042551;
                    break;
            }
        } else {
            switch ($user_id) {
                    //Conta principal - SP
                case "193724256":
                    $app_key                    = "468080198586";
                    $app_secret                 = "7b3fb2b3bae35eca3b051b825b6d9f43";
                    $codigo_cenario_impostos    = "500712977";
                    $codigo_transportadora      = "2671593129";
                    $codVend                    = 500726231;
                    $codigo_conta_corrente      = 502875713;
                    break;
                    //Conta duplicada - SP
                case "435343067":
                    $app_key                    = "1017311982687";
                    $app_secret                 = "78ba33370fac6178da52d42240591291";
                    $codigo_cenario_impostos    = "1018251055";
                    $codigo_transportadora      = "1200472679";
                    $codVend                    = 1018256043;
                    $codigo_conta_corrente      = 1018255531;
                    break;
                    //Conta principal - MG
                case "195972862":
                    $app_key                    = "1758907907757";
                    $app_secret                 = "0a69c9b49e5a188e5f43d5505f2752bc";
                    $codigo_cenario_impostos    = "2388479664";
                    $codigo_transportadora      = "2388465018";
                    $codVend                    = 2388488707;
                    $codigo_conta_corrente      = 2404912790;
                    break;
            }
        }


        $body = [
            "call" => "ListarClientes",
            "app_key" => $app_key,
            "app_secret" => $app_secret,
            "param" => [
                "pagina" => 1,
                "registros_por_pagina" => 1,
                "apenas_importado_api" => "N",
                "clientesFiltro" => [
                    "cnpj_cpf" => $billing_info["body"]->billing_info->doc_number,
                ]
            ]
        ];
        $response_omie = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=", $body);
        $cliente_codigo_integracao  = "";
        $codigo_cliente             = "";
        $inscricao_estadual         = '';

        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200) {
            $status_retorno = "\nCliente já cadastrado";

            foreach ($response_omie["body"]["clientes_cadastro"] as $i => $cliente) {
                $cliente_codigo_integracao = $cliente["codigo_cliente_integracao"];
                $codigo_cliente            = $cliente["codigo_cliente_omie"];
                $inscricao_estadual        = $cliente['inscricao_estadual'];
            }
        } else {

            $cep = substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'), 0, 10);
            $endereco = PedidoMercadoLivre::GetEndereco($cep);
            $endereco = json_decode($endereco);

            $body = [
                "call" => "IncluirCliente",
                "app_key" => $app_key,
                "app_secret" => $app_secret,
                "param" => [
                    "codigo_cliente_integracao" => substr(ArrayHelper::getValue($response_order, 'body.buyer.id'), 0, 20) . "ML",
                    "razao_social"              => str_replace(" ", "%20", substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name') . " " . ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 59)),
                    "cnpj_cpf"                  => substr($billing_info["body"]->billing_info->doc_number, 0, 20),
                    "nome_fantasia"             => str_replace(" ", "%20", substr(ArrayHelper::getValue($response_order, 'body.buyer.first_name') . " " . ArrayHelper::getValue($response_order, 'body.buyer.last_name'), 0, 100)),
                    "telefone1_ddd"             => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'), 0, 5),
                    "telefone1_numero"          => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_phone'), 0, 15),
                    "contato"                   => str_replace(" ", "%20", substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.receiver_name'), 0, 100)),
                    "endereco"                  => str_replace(" ", "%20", substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_name'), 0, 59)),
                    "endereco_numero"           => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.street_number'), 0, 10),
                    "bairro"                    => utf8_encode(str_replace(" ", "%20", substr(((ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name') == "") ? "Centro" : ArrayHelper::getValue($envio_dados, 'body.receiver_address.neighborhood.name')), 0, 30))),
                    "complemento"               => utf8_encode(substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.comment'), 0, 39)),
                    "estado"                    => str_replace(" ", "%20", substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2)),
                    "cidade"                    => $endereco->ibge, //"3536703",//$endereco->ibge,
                    "cep"                       => substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.zip_code'), 0, 10),
                    "email"                     => "cliente.pecaagora@gmail.com", //ArrayHelper::getValue($response_order, 'body.buyer.email'),
                ]
            ];

            $response_omie = $omie->cria_cliente("api/v1/geral/clientes/?JSON=", $body);

            if (ArrayHelper::getValue($response_omie, 'httpCode') == 200) {
                $status_retorno .= "\nCliente Criado";
                $cliente_codigo_integracao = substr(ArrayHelper::getValue($response_order, 'body.buyer.id'), 0, 20) . "ML";
            } else {
                return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Cliente não criado (" . $response_omie["body"]["faultstring"] . ")</span>" : "\nCliente não criado (" . $response_omie["body"]["faultstring"] . ")";
            }
        }

        //Verificar se existe pedido
        $body = [
            "call" => "ConsultarPedido",
            "app_key" => $app_key,
            "app_secret" => $app_secret,
            "param" => [
                "codigo_pedido_integracao" => ArrayHelper::getValue($response_order, 'body.id'),
            ]
        ];
        $response_omie = $omie->consulta_pedido("api/v1/geral/pedidos/?JSON=", $body);

        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200) {
            $status_retorno .= "\nPedido já cadastrado no Omie";

            if ($pedido_mercado_livre) {
                $pedido_mercado_livre->codigo_pedido_omie = ArrayHelper::getValue($response_omie, 'body.pedido_venda_produto.cabecalho.codigo_pedido');
                if ($pedido_mercado_livre->save()) {
                    $status_retorno .= "\nCodigo Omie adicionado no pedido do ML";
                } else {
                    $status_retorno .= "\nCodigo Omie não adicionado no pedido do ML";
                }
            }
        } else {
            if ($pedido_mercado_livre) {
                $pedido_mercado_livre->codigo_pedido_omie = null;
                $pedido_mercado_livre->save();
            }
        }

        //Adicionar novo PEDIDO
        $produtoML = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
        if (!$produtoML) {
            $produtoML = ProdutoFilial::find()->andWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();

            if (!$produtoML) {
                $produtoML = ProdutoFilial::find()->andWhere(['=', 'meli_id_full', ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();

                if (!$produtoML) {
                    $produtoML = ProdutoFilial::find()->andWhere(['=', 'meli_id_flex', ArrayHelper::getValue($response_order, 'body.order_items.0.item.id')])->one();
                }
            }
        }

        if (!$produtoML) {
            return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Produto sem vínculo ( MELI_ID: " . ArrayHelper::getValue($response_order, 'body.order_items.0.item.id') . " )</span>" : "\nProduto sem vínculo ( MELI_ID: " . ArrayHelper::getValue($response_order, 'body.order_items.0.item.id') . " )";
        }

        FunctionsML::atualizarPreco(null, $produtoML);

        $aliquota_interestadual_mg = ['PR', 'RS', 'RJ', 'SP', 'SC']; // 7% else 12% - MG > MG 18%
        $aliquota_interestadual_sp = ['PR', 'RS', 'RJ', 'MG', 'SC'];

        $aliquota_uf_destino = [
            'AC' => 0.17, 'AL' => 0.17, 'AM' => 0.18, 'AP' => 0.18, 'BA' => 0.18, 'CE' => 0.18, 'DF' => 0.18, 'ES' => 0.17,
            'GO' => 0.17, 'MA' => 0.18, 'MS' => 0.17, 'MT' => 0.17, 'MG' => 0.18, 'SP' => 0.18, 'RJ' => 0.18, 'SC' => 0.17, 'TO' => 0.18, 'SE' => 0.18, 'RR' => 0.17,
            'RO' => 0.175, 'RS' => 0.18, 'RN' => 0.18, 'PI' => 0.18, 'PR' => 0.18, 'PB' => 0.18, 'PE' => 0.18, 'PA' => 0.17
        ];

        $perc_fcp = 0;

        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) == "AL" || substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) == "PI") {
            $perc_fcp = 0.01;
        }

        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) == "RJ") {
            $perc_fcp = 0.02;
        }

        $cfop = '5405';
        $perc_aliquota = 0.18;
        $cst = '00';

        if ($user_id == '193724256' || $user_id == '435343067') {
            if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) !== "SP") {

                if (in_array(substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2), $aliquota_interestadual_sp)) {
                    $perc_aliquota = 0.12;
                } else {
                    $perc_aliquota = 0.07;
                }

                if (strlen(substr(str_replace(" ", "", $billing_info["body"]->billing_info->doc_number), 0, 20)) > 11) {
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

            if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) !== "MG") {

                if (in_array(substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2), $aliquota_interestadual_mg)) {
                    $perc_aliquota = 0.12;
                } else {
                    $perc_aliquota = 0.07;
                }

                if (strlen(substr(str_replace(" ", "", $billing_info["body"]->billing_info->doc_number), 0, 20)) > 11) {
                    if ($inscricao_estadual !== '') {
                        $cfop = '6102';
                    } else {
                        $cfop = '6108';
                    }
                } else {
                    $cfop = '6108';
                }
            } else {
                $cst = '00';
                $cfop = '5102';
            }
        }

        $det = [];

        $total_tarifa = 0;
        $total_frete = 0;

        if ($pedido_mercado_livre->codigo_pedido_omie <> null) {

            $produto_ml = PedidoMercadoLivreProduto::findOne(['pedido_mercado_livre_id' => $pedido_mercado_livre->id]);

            $produto_cotacao_ml = PedidoMercadoLivreProdutoProdutoFilial::findAll(['pedido_mercado_livre_produto_id' => $produto_ml->id]);

            if ($produto_cotacao_ml) {

                array_push($det, [
                    "ide" => [
                        "codigo_item_integracao"    => substr($produtoML->produto->codigo_global, 0, 20),
                        "acao_item" => "E"
                    ],
                ]);

                foreach ($produto_cotacao_ml as $cotacao) {

                    $produto_filial_cotacao = ProdutoFilial::findOne($cotacao->produto_filial_id);
                    $produto = Produto::findOne($produto_filial_cotacao->produto_id);

                    $codigo_pa      = "PA" . $produto_filial_cotacao->produto_id;
                    $codigo_global  = $produto_filial_cotacao->produto->codigo_global;
                    $multiplicador = $produto_ml->produtoFilial->produto->multiplicador;
                    $quantidade     = $cotacao->quantidade;
                    $valor_unitario = ($produto_ml->full_unit_price * $produto_ml->quantity) / $quantidade;

                    $tarifas        = $produto_ml->sale_fee * $produto_ml->quantity;
                    $total_tarifa   = $tarifas;
                    $frete          = ArrayHelper::getValue($envio_dados, 'body.shipping_option.cost') - ArrayHelper::getValue($envio_dados, 'body.shipping_option.list_cost');
                    $frete          = ($frete < 0) ? $frete * (-1) : $frete;
                    $total_frete    = $frete;
                    $valor_unitario = $valor_unitario - (($tarifas / $quantidade) + ($frete / $quantidade));

                    $pis_cofins = self::GerarPisCofins($produto_filial_cotacao->produto);

                    $cofins = null;
                    $pis = null;

                    if ($pis_cofins == '01') {
                        $cofins = [
                            "aliq_cofins" => 3,
                            "base_cofins" => $valor_unitario * $quantidade,
                            "cod_sit_trib_cofins" => $pis_cofins,
                            "qtde_unid_trib_cofins" => $quantidade,
                            "tipo_calculo_cofins" => "B",
                            "valor_cofins" => ($valor_unitario * $quantidade) *  0.03,
                            "valor_unid_trib_cofins" => 0,
                        ];
                        $pis = [
                            "aliq_pis" => 0.65,
                            "base_pis" => $valor_unitario * $quantidade,
                            "cod_sit_trib_pis" => $pis_cofins,
                            "qtde_unid_trib_pis" => $quantidade,
                            "tipo_calculo_pis" => "B",
                            "valor_pis" => ($valor_unitario * $quantidade) *  0.0065,
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

                    $bc_icms = 0;
                    $valor_icms = 0;
                    $aliq_icms = 0;
                    $difal = $aliquota_uf_destino[substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2)] - $perc_aliquota;

                    if ($user_id == '193724256' || $user_id == '435343067') {
                        if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) !== "SP") {
                            $bc_icms = $valor_unitario * $quantidade;
                            $valor_icms = ($valor_unitario * $quantidade) * $perc_aliquota;
                            $aliq_icms = $perc_aliquota * 100;
                        }
                    } else {
                        $bc_icms = ($valor_unitario * $quantidade) + $tarifas + $frete;
                        $valor_icms = (($valor_unitario * $quantidade) + $tarifas + $frete) * $perc_aliquota;
                        $aliq_icms = $perc_aliquota * 100;
                    }

                    array_push($det, [
                        "ide" => [
                            "codigo_item_integracao"    => substr($produto_filial_cotacao->produto->codigo_global, 0, 20),
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
                                "aliq_icms" => $aliq_icms,
                                "base_icms" => $bc_icms,
                                "cod_sit_trib_icms" => $cst,
                                "modalidade_icms" => "3",
                                "origem_icms" => "0",
                                "perc_red_base_icms" => 0,
                                "valor_icms" => $valor_icms,
                                "perc_fcp_icms" => 0,
                                "base_fcp_icms" => 0.00,
                                "valor_fcp_icms" => 0.00
                            ],
                            "icms_ie" => [
                                "base_icms_uf_destino" => $valor_unitario * $quantidade,
                                "aliq_icms_FCP" => $perc_fcp * 100,
                                "aliq_interna_uf_destino" => $aliquota_uf_destino[substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2)] * 100,
                                "aliq_interestadual" => $perc_aliquota * 100,
                                "valor_fcp_icms_inter" => $valor_unitario * $quantidade * $perc_fcp,
                                "valor_icms_uf_dest" => ($valor_unitario * $quantidade) * $difal,
                                "valor_icms_uf_remet" => 0,
                            ],
                        ],
                        "produto" => [
                            "codigo_produto_integracao" => $codigo_pa, //"PA".$produtoML->produto->id,
                            "cfop"                      => $cfop,
                            "quantidade"                => $quantidade, //ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                            "valor_unitario"            => $valor_unitario, //ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                        ],
                    ]);
                }
            }
        } else {

            $codigo_pa      = "PA" . $produtoML->produto_id;
            $codigo_global  = $produtoML->produto->codigo_global;
            $multiplicador  = ((!is_null($produtoML->produto->multiplicador)) ? $produtoML->produto->multiplicador : 1);
            $quantidade     = ArrayHelper::getValue($response_order, 'body.order_items.0.quantity') * $multiplicador;
            $valor_unitario = ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price') / $multiplicador;

            if (!(strpos($codigo_global, "CX.") === false) || !(strpos($codigo_global, "P.") === false)) {

                $codigo_global_limpo = str_replace("CX.", "", $codigo_global);
                $codigo_global_limpo = str_replace("P.", "", $codigo_global_limpo);
                // echo "<br><br>";

                if (!(strpos($codigo_global, "-") === false)) {

                    $codigo_global_limpo_sem_unidades   = explode("-", $codigo_global_limpo);
                    $codigo_global_limpo                = $codigo_global_limpo_sem_unidades[0];
                }
                // echo "<br><br>";

                $produto_unitario   = Produto::find()->andWhere(['=', 'codigo_global', $codigo_global_limpo])->one();
                if (!$produto_unitario) {
                    return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Sem produto unitário cadastrado</span>" : "Sem produto unitário cadastrado";
                }

                $codigo_pa          = "PA" . $produto_unitario->id;
            }

            //DESCONTOS
            $tarifas        = 0.0;
            $frete          = 0.0;
            foreach (ArrayHelper::getValue($response_order, 'body.order_items') as $k => $produto) {

                $tarifas        = ArrayHelper::getValue($produto, 'sale_fee') * ArrayHelper::getValue($response_order, 'body.order_items.0.quantity');
                $total_tarifa   = $tarifas;
                $frete          = ArrayHelper::getValue($envio_dados, 'body.shipping_option.cost') - ArrayHelper::getValue($envio_dados, 'body.shipping_option.list_cost');
                $frete          = ($frete < 0) ? $frete * (-1) : $frete;
                $total_frete = $frete;
                $valor_unitario = $valor_unitario - (($tarifas / $quantidade) + ($frete / $quantidade));
            }

            $pis_cofins = self::GerarPisCofins($produtoML->produto);

            $cofins = null;
            $pis = null;

            if ($pis_cofins == '01') {
                $cofins = [
                    "aliq_cofins" => 3,
                    "base_cofins" => $valor_unitario * $quantidade,
                    "cod_sit_trib_cofins" => $pis_cofins,
                    "qtde_unid_trib_cofins" => $quantidade,
                    "tipo_calculo_cofins" => "B",
                    "valor_cofins" => ($valor_unitario * $quantidade) *  0.03,
                    "valor_unid_trib_cofins" => 0,
                ];
                $pis = [
                    "aliq_pis" => 0.65,
                    "base_pis" => $valor_unitario * $quantidade,
                    "cod_sit_trib_pis" => $pis_cofins,
                    "qtde_unid_trib_pis" => $quantidade,
                    "tipo_calculo_pis" => "B",
                    "valor_pis" => ($valor_unitario * $quantidade) *  0.0065,
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
            $bc_icms = 0;
            $valor_icms = 0;
            $aliq_icms = 0;
            $difal = $aliquota_uf_destino[substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2)] - $perc_aliquota;

            if ($user_id == '193724256' || $user_id == '435343067') {
                if (substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2) !== "SP") {
                    $bc_icms = $valor_unitario * $quantidade;
                    $valor_icms = ($valor_unitario * $quantidade) * $perc_aliquota;
                    $aliq_icms = $perc_aliquota * 100;
                }
            } else {
                $bc_icms = ($valor_unitario * $quantidade) + $tarifas + $frete;
                $valor_icms = (($valor_unitario * $quantidade) + $tarifas + $frete) * $perc_aliquota;
                $aliq_icms = $perc_aliquota * 100;
            }

            array_push($det, [
                "ide" => [
                    "codigo_item_integracao"    => substr($produtoML->produto->codigo_global, 0, 20),
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
                        "aliq_icms" => $aliq_icms,
                        "base_icms" => $bc_icms,
                        "cod_sit_trib_icms" => $cst,
                        "modalidade_icms" => "3",
                        "origem_icms" => "0",
                        "perc_red_base_icms" => 0,
                        "valor_icms" => $valor_icms,
                        "perc_fcp_icms" => $perc_fcp * 100,
                        "base_fcp_icms" => $bc_icms,
                        "valor_fcp_icms" => $bc_icms * $perc_fcp
                    ],
                    "icms_ie" => [
                        "base_icms_uf_destino" => $valor_unitario * $quantidade,
                        "aliq_icms_FCP" => $perc_fcp * 100,
                        "aliq_interna_uf_destino" => $aliquota_uf_destino[substr(ArrayHelper::getValue($envio_dados, 'body.receiver_address.state.id'), -2)] * 100,
                        "aliq_interestadual" => $perc_aliquota * 100,
                        "valor_fcp_icms_inter" => $valor_unitario * $quantidade * $perc_fcp,
                        "valor_icms_uf_dest" => ($valor_unitario * $quantidade) * $difal,
                        "valor_icms_uf_remet" => 0,
                    ],
                ],
                "produto" => [
                    "codigo_produto_integracao" => $codigo_pa, //"PA".$produtoML->produto->id,
                    "cfop"                      => $cfop,
                    "quantidade"                => $quantidade, //ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                    "valor_unitario"            => $valor_unitario, //ArrayHelper::getValue($response_order, 'body.order_items.0.unit_price'),
                ],
            ]);
        }

        $call = "IncluirPedido";

        if ($pedido_mercado_livre->codigo_pedido_omie <> null) {
            $call = "AlterarPedidoVenda";
        }

        $body = [
            "call" => $call,
            "app_key" => $app_key,
            "app_secret" => $app_secret,
            "param" => [
                [
                    "cabecalho" => [
                        "bloqueado"                 => "N",
                        "codigo_cliente"            => $codigo_cliente, //ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'),
                        "codigo_cenario_impostos"   => $codigo_cenario_impostos,
                        "codigo_cliente_integracao" => $cliente_codigo_integracao, //ArrayHelper::getValue($response_order, 'body.buyer.id'),
                        "codigo_pedido_integracao"  => ArrayHelper::getValue($response_order, 'body.id'),
                        "etapa"                     => "10",
                        "data_previsao"             => substr(ArrayHelper::getValue($response_order, 'body.date_created'), 8, 2) . '/' . substr(ArrayHelper::getValue($response_order, 'body.date_created'), 5, 2) . '/' . substr(ArrayHelper::getValue($response_order, 'body.date_created'), 0, 4),
                        "quantidade_itens"          => ArrayHelper::getValue($response_order, 'body.order_items.0.quantity'),
                    ],
                    "det" => $det,
                    "frete" => [
                        "codigo_transportadora" => $codigo_transportadora,
                        "modalidade"            => 0,
                        "quantidade_volumes"    => 1,
                        "especie_volumes"       => "CAIXA",
                        "outras_despesas"       => $total_tarifa,
                        "valor_frete"           => $total_frete,
                    ],
                    "informacoes_adicionais"    => [
                        "numero_contrato"           => ArrayHelper::getValue($response_order, 'body.payments.0.id'),
                        "numero_pedido_cliente"     => ArrayHelper::getValue($response_order, 'body.id'),
                        "consumidor_final"          => "S",
                        "codigo_categoria"          => "1.01.03",
                        "codVend"                   => $codVend,
                        "codigo_conta_corrente"     => $codigo_conta_corrente,
                        "dados_adicionais_nf"        => ($user_id == "195972862") ? "EMPRESA SOB REGIME ESPECIAL No -PTA-RE No 45.000027189-79\nArt. 32: Mercadoria destinada a uso e consumo, vedado o aproveitamento do crédito nos termos do inciso III do art. 70 do RICMS" : "",
                    ],
                ],
            ],
        ];

        $response_omie = $omie->CriarPedido($body);

        if (ArrayHelper::getValue($response_omie, 'httpCode') == 200) {

            if ($pedido_mercado_livre->codigo_pedido_omie) {
                $status_retorno .= "\nPedido Alterado";
            } else {
                $status_retorno .= "\nPedido criado";
            }

            $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "pedido_meli_id", $order])->one();
            if ($pedido_mercado_livre) {
                $pedido_mercado_livre->codigo_pedido_omie = ArrayHelper::getValue($response_omie, 'body.codigo_pedido');
                if ($pedido_mercado_livre->save()) {
                    $status_retorno .= "\nCodigo Omie adicionado no pedido do ML";
                } else {
                    $status_retorno .= "\nCodigo Omie não adicionado no pedido do ML";
                }
            }
        } else {
            return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Pedido não criado (" . $response_omie["body"]["faultstring"] . ")</span>" : "Pedido não criado (" . $response_omie["body"]["faultstring"] . ")";
        }

        return $status_retorno;
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


    // public static function gerarImposto($csosn, $produto)
    // {

    //     $imposto = [
    //         "ipi" => [
    //             "cod_sit_trib_ipi"  => 99,
    //             "enquadramento_ipi" => 999,
    //             "tipo_calculo_ipi"  => "B",
    //         ],
    //         "pis_padrao" => [
    //             "cod_sit_trib_pis"  => 49,
    //             "tipo_calculo_pis"  => "B",
    //         ],
    //         "cofins_padrao" => [
    //             "cod_sit_trib_cofins"   => 49,
    //             "tipo_calculo_cofins"   => "B",
    //         ],
    //         "icms_sn" => [
    //             "cod_sit_trib_icms_sn" => $csosn,
    //         ],
    //     ];

    //     $codigos = [
    //         //Anexo 1
    //         40161010,
    //         40169990,
    //         6813,
    //         70071100,
    //         70072100,
    //         70091000,
    //         73201000,
    //         83012000,
    //         83023000,
    //         84073390,
    //         84073490,
    //         840820,
    //         840991,
    //         840999,
    //         841330,
    //         84139100,
    //         84148021,
    //         84148022,
    //         841520,
    //         84212300,
    //         84213100,
    //         84314100,
    //         84314200,
    //         84339090,
    //         848310,
    //         84832000,
    //         848330,
    //         848340,
    //         848350,
    //         850520,
    //         85071000,
    //         8511,
    //         851220,
    //         85123000,
    //         851240,
    //         85129000,
    //         85272,
    //         85365090,
    //         853910,
    //         85443000,
    //         870600,
    //         8707,
    //         8708,
    //         90292010,
    //         90299010,
    //         90303921,
    //         90318040,
    //         9032892,
    //         91040000,
    //         94012000,

    //         //Anexo 2
    //         8429,
    //         843320,
    //         84333000,
    //         84334000,
    //         84335,
    //         8701,
    //         8702,
    //         8703,
    //         8704,
    //         8705,
    //         8706,
    //         8431,
    //         84089090,
    //         84122110,
    //         84122190,
    //         84123110,
    //         87012000,
    //         8702,
    //         8704,
    //         84136019,
    //         84148019,
    //         84149039,
    //         84329000,
    //         84324000,
    //         84328000,
    //         84811000,
    //         84812090,
    //         84818092,
    //         8483601,
    //         85011019
    //     ];

    //     //echo "<pre>"; print_r($imposto); echo "</pre>"; die;

    //     foreach ($codigos as $k => $codigo) {

    //         //echo "<br>".$k." - ".$codigo;

    //         $quantidade_caracteres = strlen($codigo);
    //         $ncm = str_replace('.', '', $produto->codigo_montadora);
    //         $sub_ncm = substr($ncm, 0, $quantidade_caracteres);

    //         //echo " - ".$quantidade_caracteres." - ".$sub_ncm;

    //         if ($sub_ncm == $codigo) {
    //             $imposto["cofins_padrao"]["cod_sit_trib_cofins"] = "04";
    //             $imposto["cofins_padrao"]["tipo_calculo_cofins"] = "";

    //             $imposto["pis_padrao"]["cod_sit_trib_pis"] = "04";
    //             $imposto["pis_padrao"]["tipo_calculo_pis"] = "";

    //             break;
    //         }
    //     }

    //     //echo "<pre>"; print_r($imposto); echo "</pre>";

    // }


    public static function criarAlterarPedidoMercadoLivre($order, $billing_info, $e_html)
    {

        $status_retorno = "";

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andwhere(['=', 'pedido_meli_id', ArrayHelper::getValue($order, 'body.id')])->one();
        if ($pedido_mercado_livre) {
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.date_created'), 0, 19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.date_closed'), 0, 19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.last_updated'), 0, 19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');
            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) (isset($order["body"]->buyer->first_name)) ? ArrayHelper::getValue($order, 'body.buyer.first_name') : "";
            $pedido_mercado_livre->buyer_last_name      = (string) (isset($order["body"]->buyer->last_name)) ? ArrayHelper::getValue($order, 'body.buyer.last_name') : "";
            $pedido_mercado_livre->buyer_doc_type       = (string) (isset($billing_info["body"]->billing_info->doc_type)) ? $billing_info["body"]->billing_info->doc_type : "";
            $pedido_mercado_livre->buyer_doc_number     = (string) (isset($billing_info["body"]->billing_info->doc_number)) ? $billing_info["body"]->billing_info->doc_number : "";
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            echo "((##11##))";
            if (is_null($pedido_mercado_livre->email_enderecos)) {
                //$pedido_mercado_livre->email_enderecos = "entregasp.pecaagora@gmail.com; notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com; entregasp.pecaagora@gmail.com";
                $pedido_mercado_livre->email_enderecos = "notafiscal.pecaagora@gmail.com; compras.pecaagora@gmail.com";
            }

            if ($pedido_mercado_livre->save()) {
                $status_retorno .= "\nPedido alterado";
            } else {
                $status_retorno .=  "\nPedido não alterado";
            }
        } else {
            //echo "<pre>"; print_r($order); echo "</pre>"; die;
            $pedido_mercado_livre = new PedidoMercadoLivre();
            $pedido_mercado_livre->pedido_meli_id       = (string) ArrayHelper::getValue($order, 'body.id');
            $pedido_mercado_livre->total_amount         = ArrayHelper::getValue($order, 'body.total_amount');
            $pedido_mercado_livre->date_created         = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.date_created'), 0, 19));
            $pedido_mercado_livre->date_closed          = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.date_closed'), 0, 19));
            $pedido_mercado_livre->last_updated         = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.last_updated'), 0, 19));
            $pedido_mercado_livre->paid_amount          = (string) ArrayHelper::getValue($order, 'body.paid_amount');
            $pedido_mercado_livre->shipping_id          = (string) ArrayHelper::getValue($order, 'body.shipping.id');

            $pedido_mercado_livre->status               = (string) ArrayHelper::getValue($order, 'body.status');
            $pedido_mercado_livre->buyer_id             = (string) ArrayHelper::getValue($order, 'body.buyer.id');
            $pedido_mercado_livre->buyer_nickname       = (string) (isset($order["body"]->buyer->nickname)) ? ArrayHelper::getValue($order, 'body.buyer.nickname') : "";
            $pedido_mercado_livre->buyer_email          = (string) (isset($order["body"]->buyer->email)) ? ArrayHelper::getValue($order, 'body.buyer.email') : "";
            $pedido_mercado_livre->buyer_first_name     = (string) ArrayHelper::getValue($order, 'body.buyer.first_name');

            $pedido_mercado_livre->buyer_last_name      = (string) ArrayHelper::getValue($order, 'body.buyer.last_name');
            print_r($billing_info);
            print_r($order);
            echo "((##22##))";
            $pedido_mercado_livre->buyer_doc_type       = (string) $billing_info["body"]->billing_info->doc_type;
            $pedido_mercado_livre->buyer_doc_number     = (string) $billing_info["body"]->billing_info->doc_number;
            echo 888;
            $pedido_mercado_livre->user_id              = (string) ArrayHelper::getValue($order, 'body.seller.id');
            $pedido_mercado_livre->pack_id              = (string) ArrayHelper::getValue($order, 'body.pack_id');
            //$pedido_mercado_livre->email_enderecos      = "entregasp.pecaagora@gmail.com, notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";
            $pedido_mercado_livre->email_enderecos      = "notafiscal.pecaagora@gmail.com, compras.pecaagora@gmail.com";

            if ($pedido_mercado_livre->save()) {
                $status_retorno .= "\nPedido criado";
            } else {
                return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Pedido não criado</span>" : "Pedido não criado";
            }
        }

        if ($pedido_mercado_livre) {

            $produtos_email = "
DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.
                
Cód.: {codigo}
Descrição: {descricao}
Quantidade: {quantidade}
Valor: R$ {valor}
Observação: {observacao}
NCM: {ncm}
PA{pa}
                
                
Envio: Carmópolis de Minas, 963, Vila Maria.
                
  
Atenciosamente,
                
                
Peça Agora
Site: https://www.pecaagora.com/
E-mail: compras.pecaagora@gmail.comSetor de Compras:(32)3015-0023Whatsapp:(32)988354007
Skype: pecaagora";

            foreach (ArrayHelper::getValue($order, 'body.order_items') as $k => $produto) {

                $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                    ->andWhere(['=', 'produto_meli_id', ArrayHelper::getValue($produto, 'item.id')])
                    ->one();
                if ($pedido_mercado_livre_produto) {
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');

                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_flex', ArrayHelper::getValue($produto, 'item.id')])
                        ->one();
                    if ($produto_filial) {
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }

                    if ($pedido_mercado_livre_produto->save()) {
                        $status_retorno .= "\nProduto alterado";
                    } else {
                        $status_retorno .= "\nProduto não alterado";
                    }
                } else {
                    //echo "<pre>==>"; print_r($pedido_mercado_livre->id); echo "<==</pre>"; die;
                    $pedido_mercado_livre_produto                          = new PedidoMercadoLivreProduto();
                    $pedido_mercado_livre_produto->pedido_mercado_livre_id = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_produto->produto_meli_id         = ArrayHelper::getValue($produto, 'item.id');
                    $pedido_mercado_livre_produto->title                   = ArrayHelper::getValue($produto, 'item.title');
                    $pedido_mercado_livre_produto->categoria_meli_id       = ArrayHelper::getValue($produto, 'item.category_id');
                    $pedido_mercado_livre_produto->condition               = ArrayHelper::getValue($produto, 'item.condition');
                    $pedido_mercado_livre_produto->quantity                = ArrayHelper::getValue($produto, 'quantity');
                    $pedido_mercado_livre_produto->unit_price              = ArrayHelper::getValue($produto, 'unit_price');
                    $pedido_mercado_livre_produto->full_unit_price         = ArrayHelper::getValue($produto, 'full_unit_price');
                    $pedido_mercado_livre_produto->sale_fee                = ArrayHelper::getValue($produto, 'sale_fee');
                    $pedido_mercado_livre_produto->listing_type_id         = ArrayHelper::getValue($produto, 'listing_type_id');

                    //$produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])->one();
                    $produto_filial = ProdutoFilial::find()->orWhere(['=', 'meli_id', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_sem_juros', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_full', ArrayHelper::getValue($produto, 'item.id')])
                        ->orWhere(['=', 'meli_id_flex', ArrayHelper::getValue($produto, 'item.id')])
                        ->one();
                    if ($produto_filial) {
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial->id;
                    }

                    if ($pedido_mercado_livre_produto->save()) {
                        $status_retorno .= "\nProduto criado";
                    } else {
                        $status_retorno .= "\nProduto não criado";
                    }
                }
            }

            //Cadastra os dados dos pagamentos
            foreach (ArrayHelper::getValue($order, 'body.payments') as $k => $pagamento) {

                $pedido_mercado_livre_pagamento = PedidoMercadoLivrePagamento::find()->andWhere(['=', 'pedido_mercado_livre_id', $pedido_mercado_livre->id])
                    ->andWhere(['=', 'pagamento_meli_id', ArrayHelper::getValue($pagamento, 'id')])
                    ->one();
                if ($pedido_mercado_livre_pagamento) {
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');

                    if ($pedido_mercado_livre_pagamento->save()) {
                        $status_retorno .= "\nPagamento alterado";
                    } else {
                        $status_retorno .= "\nPagamento não alterado";
                    }
                } else {
                    $pedido_mercado_livre_pagamento                             = new PedidoMercadoLivrePagamento();
                    $pedido_mercado_livre_pagamento->pedido_mercado_livre_id    = $pedido_mercado_livre->id;
                    $pedido_mercado_livre_pagamento->pagamento_meli_id          = (string) ArrayHelper::getValue($pagamento, 'id');
                    $pedido_mercado_livre_pagamento->payer_id                   = (string) ArrayHelper::getValue($pagamento, 'payer_id');
                    $pedido_mercado_livre_pagamento->card_id                    = (string) ArrayHelper::getValue($pagamento, 'card_id');
                    $pedido_mercado_livre_pagamento->payment_method_id          = (string) ArrayHelper::getValue($pagamento, 'payment_method_id');
                    $pedido_mercado_livre_pagamento->operation_type             = (string) ArrayHelper::getValue($pagamento, 'operation_type');
                    $pedido_mercado_livre_pagamento->payment_type               = (string) ArrayHelper::getValue($pagamento, 'payment_type');
                    $pedido_mercado_livre_pagamento->status                     = (string) ArrayHelper::getValue($pagamento, 'status');
                    $pedido_mercado_livre_pagamento->status_detail              = (string) ArrayHelper::getValue($pagamento, 'status_detail');
                    $pedido_mercado_livre_pagamento->transaction_amount         = ArrayHelper::getValue($pagamento, 'transaction_amount');
                    $pedido_mercado_livre_pagamento->taxes_amount               = ArrayHelper::getValue($pagamento, 'taxes_amount');
                    $pedido_mercado_livre_pagamento->shipping_cost              = ArrayHelper::getValue($pagamento, 'shipping_cost');
                    $pedido_mercado_livre_pagamento->coupon_amount              = ArrayHelper::getValue($pagamento, 'coupon_amount');
                    $pedido_mercado_livre_pagamento->overpaid_amount            = ArrayHelper::getValue($pagamento, 'overpaid_amount');
                    $pedido_mercado_livre_pagamento->total_paid_amount          = ArrayHelper::getValue($pagamento, 'total_paid_amount');
                    $pedido_mercado_livre_pagamento->installment_amount         = ArrayHelper::getValue($pagamento, 'installment_amount');
                    $pedido_mercado_livre_pagamento->date_approved              = ArrayHelper::getValue($pagamento, 'date_approved');
                    $pedido_mercado_livre_pagamento->authorization_code         = (string) ArrayHelper::getValue($pagamento, 'authorization_code');
                    $pedido_mercado_livre_pagamento->date_created               = ArrayHelper::getValue($pagamento, 'date_created');
                    $pedido_mercado_livre_pagamento->date_last_modified         = ArrayHelper::getValue($pagamento, 'date_last_modified');

                    if ($pedido_mercado_livre_pagamento->save()) {
                        $status_retorno .= "\nPagamento criado";
                    } else {
                        $status_retorno .= "\nPagamento não criado";
                    }
                }
            }

            $pedido_mercado_livre->email_texto = $produtos_email;
            $pedido_mercado_livre->email_assunto = "{estoque} - {de} Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem}) - {pedido_meli_id}";
            if ($pedido_mercado_livre->user_id == "435343067") {
                $pedido_mercado_livre->email_assunto = "{estoque} - {de} Novo Pedido {codigo_fabricante} * {quantidade} - {nome} ({margem}) - {pedido_meli_id}";
            }
            if ($pedido_mercado_livre->save()) {
                $status_retorno .= "\nDados do email alterados";
            } else {
                $status_retorno .= "\nDados do email não alterados";
            }
        }

        return $status_retorno;
    }

    public static function AtualizarPedidoML($order, $e_html = false)
    {

        $status_retorno = "";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        //echo $meliAccessToken;die;
        $user_id = "193724256";

        $url_order = "/orders/" . $order;

        $response_order = $meli->get($url_order . "?access_token=" . $meliAccessToken);

        if ($response_order["httpCode"] >= 300) {
            $filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', 98])->one();
            $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken = $response_conta_duplicada->access_token;
            //echo $meliAccessToken;die;
            $response_order = $meli->get("/orders/" . $order . "?access_token=" . $meliAccessToken);

            if ($response_order["httpCode"] >= 300) {
                $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
                return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>VENDA NÃO ENCONTRADA EM NENHUMA CONTA DO MERCADO LIVRE</span>" : "\nVENDA NÃO ENCONTRADA EM NENHUMA CONTA DO MERCADO LIVRE";
            }

            $user_id = "435343067";
        }

        $billing_info   = $meli->get($url_order . "/billing_info;?access_token=" . $meliAccessToken);

        $status_retorno .= self::criarAlterarPedidoMercadoLivre($response_order, $billing_info, $e_html);

        //$url_shipping = "/shipments/".ArrayHelper::getValue($response_order, 'body.shipping.id')."?access_token=" . $meliAccessToken;
        $envio_dados = $meli->get("/shipments/" . ArrayHelper::getValue($response_order, 'body.shipping.id') . "?access_token=" . $meliAccessToken);
        //echo "<pre>".$order.$url_shipping; print_r($envio_dados); echo "</pre>"; return $status_retorno;

        if ($envio_dados['httpCode'] >= 300) {
            $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
            return $status_retorno .= ($e_html) ? "<br><span style='color: red;'>Sem informações de envio</span>" : "\nSem informa    es de envio";
        } else {
            $status_retorno .= self::criarAlterarPedidoMercadoLivreEnvio($response_order, $billing_info, $envio_dados);
        }

        $status_retorno = ($e_html) ? str_replace("\n", "<br>", $status_retorno) : $status_retorno;
        return $status_retorno;
    }

    public static function criarAlterarPedidoMercadoLivreEnvio($order, $billing_info, $shipping = null)
    {

        $status_retorno = "";

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andwhere(['=', 'pedido_meli_id', ArrayHelper::getValue($order, 'body.id')])->one();

        if ($pedido_mercado_livre) {

            //echo "123"; var_dump((string)ArrayHelper::getValue($shipping, 'body.receiver_address.city.id'));die;

            //Cadastra os dados de envio e do recebedor
            if (!is_null($shipping)) {

                $pedido_mercado_livre->shipping_base_cost                   = ArrayHelper::getValue($shipping, 'body.base_cost');
                $pedido_mercado_livre->shipping_status                      = (string) ArrayHelper::getValue($shipping, 'body.status');
                $pedido_mercado_livre->shipping_substatus                   = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                $pedido_mercado_livre->shipping_date_created                = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.date_created'), 0, 19));
                $pedido_mercado_livre->shipping_last_updated                = str_replace("T", " ", substr(ArrayHelper::getValue($order, 'body.last_updated'), 0, 19));
                $pedido_mercado_livre->shipping_tracking_number             = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                $pedido_mercado_livre->shipping_tracking_method             = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                $pedido_mercado_livre->shipping_service_id                  = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                $pedido_mercado_livre->receiver_id                          = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                $pedido_mercado_livre->receiver_address_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                $pedido_mercado_livre->receiver_address_line                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                $pedido_mercado_livre->receiver_street_name                 = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                $pedido_mercado_livre->receiver_street_number               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                $pedido_mercado_livre->receiver_comment                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                $pedido_mercado_livre->receiver_zip_code                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                $pedido_mercado_livre->receiver_city_id                     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                $pedido_mercado_livre->receiver_city_name                   = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                $pedido_mercado_livre->receiver_state_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                $pedido_mercado_livre->receiver_state_name                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                $pedido_mercado_livre->receiver_country_id                  = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                $pedido_mercado_livre->receiver_country_name                = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                $pedido_mercado_livre->receiver_neighborhood_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                $pedido_mercado_livre->receiver_neighborhood_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                $pedido_mercado_livre->receiver_municipality_id             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                $pedido_mercado_livre->receiver_municipality_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                $pedido_mercado_livre->receiver_delivery_preference         = (string) isset($shipping->body->receiver_address->delivery_preference) ? ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                $pedido_mercado_livre->receiver_name                        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                $pedido_mercado_livre->receiver_phone                       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                $pedido_mercado_livre->shipping_option_id                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                $pedido_mercado_livre->shipping_option_shipping_method_id   = (string) isset($shipping->body->shipping_option->shipping_method_id) ? ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                $pedido_mercado_livre->shipping_option_name                 = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                $pedido_mercado_livre->shipping_option_list_cost            = ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                $pedido_mercado_livre->shipping_option_cost                 = ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                $pedido_mercado_livre->shipping_option_delivery_type        = (string) isset($shipping->body->shipping_option->delivery_type) ? ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                //echo "<pre>"; print_r($pedido_mercado_livre->receiver_city_id); echo "</pre>";
                //echo "<pre>"; var_dump( ArrayHelper::getValue($shipping, 'body.receiver_address.city.id')); echo "</pre>";
                //echo "<pre>"; print_r($pedido_mercado_livre); echo "</pre>";

                //var_dump($pedido_mercado_livre->save()); die;

                if ($pedido_mercado_livre->save()) {
                    $status_retorno .= "\nEnvio alterado";
                } else {
                    $status_retorno .= "\nEnvio não alterado";
                }


                //SHIPPING
                $pedido_mercado_livre_shipments = PedidoMercadoLivreShipments::find()->andWhere(['=', 'meli_id', ArrayHelper::getValue($shipping, 'body.id')])->one();
                if ($pedido_mercado_livre_shipments) {

                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) (ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone') != "XXXXXXX") ? ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone') : $pedido_mercado_livre_shipments->receiver_address_receiver_phone;
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'), 0, 19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;

                    //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;

                    if ($pedido_mercado_livre_shipments->save()) {
                        $status_retorno .= "\nEnvio tabela alterado";

                        //echo "<pre>===>>"; print_r($pedido_mercado_livre_shipments); echo "<<==="; die;

                        foreach (ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item) {

                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;

                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                                ->andWhere(['=', 'meli_id', $shipping_item->id])
                                ->one();
                            if (!$pedido_mercado_livre_shipments_item) {
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) isset($shipping_item->dimensions_source->id) ? $shipping_item->dimensions_source->id : "";
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) isset($shipping_item->dimensions_source->origin) ? $shipping_item->dimensions_source->origin : "";
                            if ($pedido_mercado_livre_shipments_item->save()) {
                                $status_retorno .= "\nProduto do envio " . $status_item;
                            } else {
                                $status_retorno .= "\nProduto do envio " . $status_item;
                            }
                        }
                    } else {
                        $status_retorno = "\nEnvio tabela não alterado";
                    }
                } else {

                    $pedido_mercado_livre_shipments = new PedidoMercadoLivreShipments;
                    $pedido_mercado_livre_shipments->meli_id                                = (string) ArrayHelper::getValue($shipping, 'body.id');
                    $pedido_mercado_livre_shipments->mode                                   = (string) ArrayHelper::getValue($shipping, 'body.mode');
                    $pedido_mercado_livre_shipments->created_by                             = (string) ArrayHelper::getValue($shipping, 'body.created_by');
                    $pedido_mercado_livre_shipments->order_id                               = (string) ArrayHelper::getValue($shipping, 'body.order_id');
                    $pedido_mercado_livre_shipments->order_cost                             = (float) ArrayHelper::getValue($shipping, 'body.order_cost');
                    $pedido_mercado_livre_shipments->base_cost                              = (float) ArrayHelper::getValue($shipping, 'body.base_cost');
                    $pedido_mercado_livre_shipments->site_id                                = (string) ArrayHelper::getValue($shipping, 'body.site_id');
                    $pedido_mercado_livre_shipments->status                                 = (string) ArrayHelper::getValue($shipping, 'body.status');
                    $pedido_mercado_livre_shipments->substatus                              = (string) ArrayHelper::getValue($shipping, 'body.substatus');
                    $pedido_mercado_livre_shipments->history_date_cancelled                 = (isset($shipping["body"]->status_history->date_cancelled)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_cancelled'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_delivered                 = (isset($shipping["body"]->status_history->date_delivered)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_delivered'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_first_visit               = (isset($shipping["body"]->status_history->date_first_visit)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_first_visit'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_handling                  = (isset($shipping["body"]->status_history->date_handling)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_handling'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_not_delivered             = (isset($shipping["body"]->status_history->date_not_delivered)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_not_delivered'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_ready_to_ship             = (isset($shipping["body"]->status_history->date_ready_to_ship)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_ready_to_ship'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_shipped                   = (isset($shipping["body"]->status_history->date_shipped)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_shipped'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->history_date_returned                  = (isset($shipping["body"]->status_history->date_returned)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_returned'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->date_created                           = (isset($shipping["body"]->status_history->date_created)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.date_created'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->last_updated                           = (isset($shipping["body"]->status_history->last_updated)) ? str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.status_history.last_updated'), 0, 19)) : "";
                    $pedido_mercado_livre_shipments->tracking_number                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_number');
                    $pedido_mercado_livre_shipments->tracking_method                        = (string) ArrayHelper::getValue($shipping, 'body.tracking_method');
                    $pedido_mercado_livre_shipments->service_id                             = (string) ArrayHelper::getValue($shipping, 'body.service_id');
                    $pedido_mercado_livre_shipments->sender_id                              = (string) ArrayHelper::getValue($shipping, 'body.sender_id');
                    $pedido_mercado_livre_shipments->receiver_id                            = (string) ArrayHelper::getValue($shipping, 'body.receiver_id');
                    $pedido_mercado_livre_shipments->receiver_address_id                    = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.id');
                    $pedido_mercado_livre_shipments->receiver_address_address_line          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.address_line');
                    $pedido_mercado_livre_shipments->receiver_address_street_name           = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_name');
                    $pedido_mercado_livre_shipments->receiver_address_street_number         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.street_number');
                    $pedido_mercado_livre_shipments->receiver_address_comment               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.comment');
                    $pedido_mercado_livre_shipments->receiver_address_zip_code              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.zip_code');
                    $pedido_mercado_livre_shipments->receiver_address_city_id               = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.id');
                    $pedido_mercado_livre_shipments->receiver_address_city_name             = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.city.name');
                    $pedido_mercado_livre_shipments->receiver_address_state_id              = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.id');
                    $pedido_mercado_livre_shipments->receiver_address_state_name            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.state.name');
                    $pedido_mercado_livre_shipments->receiver_address_country_id            = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.id');
                    $pedido_mercado_livre_shipments->receiver_address_country_name          = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.country.name');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.id');
                    $pedido_mercado_livre_shipments->receiver_address_neighborhood_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.neighborhood.name');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_id       = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.id');
                    $pedido_mercado_livre_shipments->receiver_address_municipality_name     = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.municipality.name');
                    $pedido_mercado_livre_shipments->receiver_address_delivery_preference   = (isset($shipping["body"]->receiver_address->delivery_preference)) ? (string) ArrayHelper::getValue($shipping, 'body.receiver_address.delivery_preference') : "";
                    $pedido_mercado_livre_shipments->receiver_address_receiver_name         = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_name');
                    $pedido_mercado_livre_shipments->receiver_address_receiver_phone        = (string) ArrayHelper::getValue($shipping, 'body.receiver_address.receiver_phone');
                    $pedido_mercado_livre_shipments->shipping_option_id                     = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.id');
                    $pedido_mercado_livre_shipments->shipping_option_shipping_method_id     = (isset($shipping["body"]->shipping_option->shipping_method_id)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.shipping_method_id') : "";
                    $pedido_mercado_livre_shipments->shipping_option_name                   = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.name');
                    $pedido_mercado_livre_shipments->shipping_option_currency_id            = (string) ArrayHelper::getValue($shipping, 'body.shipping_option.currency_id');
                    $pedido_mercado_livre_shipments->shipping_option_list_cost              = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.list_cost');
                    $pedido_mercado_livre_shipments->shipping_option_cost                   = (float) ArrayHelper::getValue($shipping, 'body.shipping_option.cost');
                    $pedido_mercado_livre_shipments->delivery_type                          = (isset($shipping["body"]->shipping_option->delivery_type)) ? (string) ArrayHelper::getValue($shipping, 'body.shipping_option.delivery_type') : "";
                    $pedido_mercado_livre_shipments->comments                               = (string) ArrayHelper::getValue($shipping, 'body.comments');
                    $pedido_mercado_livre_shipments->date_first_printed                     = str_replace("T", " ", substr(ArrayHelper::getValue($shipping, 'body.date_first_printed'), 0, 19));
                    $pedido_mercado_livre_shipments->market_place                           = (string) ArrayHelper::getValue($shipping, 'body.market_place');
                    $pedido_mercado_livre_shipments->type                                   = (isset($shipping["body"]->type)) ? (string) ArrayHelper::getValue($shipping, 'body.type') : "";
                    $pedido_mercado_livre_shipments->logistic_type                          = (isset($shipping["body"]->logistic_type)) ? (string) ArrayHelper::getValue($shipping, 'body.logistic_type') : "";
                    $pedido_mercado_livre_shipments->application_id                         = (isset($shipping["body"]->application_id)) ? (string) ArrayHelper::getValue($shipping, 'body.application_id') : "";
                    $pedido_mercado_livre_shipments->pedido_mercado_livre_id                = $pedido_mercado_livre->id;


                    //echo "<pre>===>>"; var_dump($pedido_mercado_livre_shipments); echo "<<===";
                    //die;

                    //echo "======>>>"; var_dump($pedido_mercado_livre_shipments->save()); echo "<<<======";

                    if ($pedido_mercado_livre_shipments->save()) {
                        $status_retorno .= "\nEnvio tabela criado";

                        foreach (ArrayHelper::getValue($shipping, 'body.shipping_items') as $shipping_item) {

                            $status_item = "alterado";
                            $pedido_mercado_livre_shipments_item = PedidoMercadoLivreShipmentsItem::find()->andWhere(['=', 'pedido_mercado_livre_shipments_id', $pedido_mercado_livre_shipments->id])
                                ->andWhere(['=', 'meli_id', $shipping_item->id])
                                ->one();
                            if (!$pedido_mercado_livre_shipments_item) {
                                $pedido_mercado_livre_shipments_item = new PedidoMercadoLivreShipmentsItem;
                                $status_item = "criado";
                            }

                            //echo "<pre>"; print_r($shipping_item); echo "</pre>"; die;
                            $pedido_mercado_livre_shipments_item->pedido_mercado_livre_shipments_id   = $pedido_mercado_livre_shipments->id;
                            $pedido_mercado_livre_shipments_item->meli_id                             = (string) $shipping_item->id;
                            $pedido_mercado_livre_shipments_item->description                         = (string) $shipping_item->description;
                            $pedido_mercado_livre_shipments_item->quantity                            = $shipping_item->quantity;
                            $pedido_mercado_livre_shipments_item->dimensions                          = (string) $shipping_item->dimensions;
                            $pedido_mercado_livre_shipments_item->dimensions_source_id                = (string) (isset($shipping["body"]->dimensions_source->id)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.id') : "";
                            $pedido_mercado_livre_shipments_item->dimensions_source_origin            = (string) (isset($shipping["body"]->dimensions_source->origin)) ? ArrayHelper::getValue($shipping, 'body.dimensions_source.origin') : "";
                            if ($pedido_mercado_livre_shipments_item->save()) {
                                $status_retorno .= "\nProduto do pedido " . $status_item;
                            } else {
                                $status_retorno .= "\nProduto do pedido " . $status_item;
                            }
                        }
                    } else {
                        echo "\nEnvio tabela não criado";
                    }
                }
                //SHIPPING
            }
        }

        return $status_retorno;
    }
    public static function GetEndereco($cep)
    {
        $cep = str_replace('-', '', $cep);
        $cep = substr($cep, 0, 8);
        try {
            $end = file_get_contents('https://viacep.com.br/ws/' . $cep . '/json/');
        } catch (ErrorException $e) {
            return Json::encode(['error' => true]);
        }
        $endereco = Json::decode($end);
        if (!empty($endereco)) {
            return Json::encode($endereco);
        }

        return $endereco;
    }
}

/**
 * Classe para contenção de escopos da PedidoMercadoLivre, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 15/06/2020
 */
class PedidoMercadoLivreQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 15/06/2020
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_mercado_livre.nome' => $sort_type]);
    }
}
