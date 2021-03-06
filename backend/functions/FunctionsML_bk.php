<?php
//555
namespace backend\functions;

use common\models\Filial;
use yii\helpers\ArrayHelper;
use Yii;
use common\models\Produto;
use Livepixel\MercadoLivre\Meli;
use common\models\MarcaProduto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\PedidoMercadoLivreProduto;

class FunctionsML
{
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

    public static function atualizarMercadoLivre(Produto $model)
    {
        $retorno = array();

        $return = self::atualizarNome($model);
        array_push($retorno, $return);
        $return = self::atualizarCondicao($model);
        array_push($retorno, $return);
        $return = self::atualizarVideo($model);
        array_push($retorno, $return);
        $return = self::atualizarMarca($model);
        array_push($retorno, $return);
        $return = self::atualizarCodigo($model);
        array_push($retorno, $return);
        $return = self::atualizarCategoria($model);
        array_push($retorno, $return);
        $return = self::atualizarDescricao($model);
        array_push($retorno, $return);
        $return = self::atualizarImagens($model);
        array_push($retorno, $return);
        $return = self::atualizarQuantidade($model);
        array_push($retorno, $return);

        return $retorno;
    }

    public static function atualizar($produto_filial, $body, $origem)
    {
        
        $retorno = array();
    
        $retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
        
        if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
            echo " - Filial fora do ML";
            return;
        }
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
        $response           = ArrayHelper::getValue($user, 'body');
        $meliAccessToken    = $response->access_token;
        $urlDescricao = $origem == 'Descri????o' ? '/description' : '';
        
        if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
            $retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
            $response = $meli->put("items/{$produto_filial->meli_id}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                $retorno[$produto_filial->id]["meli_id_status"] = "$origem n??o alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "";
            } else {
                $retorno[$produto_filial->id]["meli_id_status"] = "$origem alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "";
                if (isset($response["body"]->permalink)) {
                    $retorno['permalink'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
            $retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
            $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "$origem n??o alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Sem Juros";
            } else {
                $retorno[$produto_filial->id]["meli_id_sem_juros_status"] = "$origem alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Sem Juros";
                if (isset($response["body"]->permalink)) {
                    $retorno['permalink'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
            $retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
            $response = $meli->put("items/{$produto_filial->meli_id_full}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                $retorno[$produto_filial->id]["meli_id_full_status"] = "$origem n??o alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Full";
            } else {
                $retorno[$produto_filial->id]["meli_id_full_status"] = "$origem alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Full";
                if (isset($response["body"]->permalink)) {
                    $retorno['permalink'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
            $retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
            $response = $meli->put("items/{$produto_filial->meli_id_flex}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                $retorno[$produto_filial->id]["meli_id_flex_status"] = "$origem n??o alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Flex";
            } else {
                $retorno[$produto_filial->id]["meli_id_flex_status"] = "$origem alterado";
                $retorno[$produto_filial->id]["tipo_ml"] = "Flex";
                if (isset($response["body"]->permalink)) {
                    $retorno['permalink'] = $response["body"]->permalink;
                }
            }
        }
        
        return $retorno;
    }

    public static function atualizarCodigo($model)
    {
        $body = [
            'attributes' => [
                [
                    "id" => "PART_NUMBER",
                    "name" => "N??mero de pe??a",
                    "value_id" => null,
                    "value_name" => $model->codigo_global,
                    "value_struct" => null,
                    "attribute_group_id" => "OTHERS",
                    "attribute_group_name" => "Outros"
                ],
            ]
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Codigo');
        }
    }

    public static function atualizarNome($model)
    {
        $title = Yii::t('app', '{nome}', ['nome' => $model->nome]);
        $title = str_replace("IVECO", "", str_replace("Iveco", "", $title));
        $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Nome');
        }
    }

    public static function atualizarCondicao($model)
    {
        $condicao = "new";
        $condicao_id = "2230284";
        $condicao_name = "Novo";
        if ($model->produtoCondicao) {
            switch ($model->produtoCondicao->meli_id) {
                case "new":
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    break;
                case "used":
                    $condicao = "used";
                    $condicao_id = "2230581";
                    $condicao_name = "Usado";
                    break;
                case "recondicionado":
                    $condicao = "new";
                    $condicao_id = "2230582";
                    $condicao_name = "Recondicionado";
                    break;
                default:
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
            }
        };

        $body = [
            "condition" => $condicao,
            'attributes' => [
                [
                    'id'                    => 'ITEM_CONDITION',
                    'name'                  => 'Condi????o do item',
                    'value_id'              => $condicao_id,
                    'value_name'            => $condicao_name,
                    'value_struct'          => null,
                    'values'                => [[
                        'id'    => $condicao_id,
                        'name'  => $condicao_name,
                        'struct' => null,
                    ]],
                    'attribute_group_id'    => "OTHERS",
                    'attribute_group_name'  => "Outros"
                ]
            ]
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Condi????o');
        }
    }

    public static function atualizarModoEnvio($model)
    {
        $body = [
            "shipping"      => [
                "mode"          => "me2",
                "methods"       => [],
                "local_pick_up" => true,
                "free_shipping" => false,
                "logistic_type" => "cross_docking",
            ],
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Modo Envio');
        }
    }

    public static function atualizarImagens($model)
    {
        $body = [
            "pictures" => $model->getUrlImagesML(),
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Imagens');
        }
    }

    public static function atualizarDescricao($model)
    {
        $page = $model->nome . "\n\nAPLICA????O:\n\n" . $model->aplicacao . $model->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo ?? o do Motorista.\n\n* Lado Direito ?? o do Passageiro.";
        $page = str_replace("'", "", $page);
        $page = str_replace("<p>", " ", $page);
        $page = str_replace("</p>", " ", $page);
        $page = str_replace("<br>", "\n", $page);
        $page = str_replace("<BR>", "\n", $page);
        $page = str_replace("<br/>", "\n", $page);
        $page = str_replace("<BR/>", "\n", $page);
        $page = str_replace("<strong>", " ", $page);
        $page = str_replace("</strong>", " ", $page);
        $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
        $page = str_replace('</span>', " ", $page);
        $page = str_replace('<span>', " ", $page);
        $page = str_replace('<ul>', " ", $page);
        $page = str_replace('</ul>', " ", $page);
        $page = str_replace('<li>', "\n", $page);
        $page = str_replace('</li>', " ", $page);
        $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
        $page = str_replace('<h1>', " ", $page);
        $page = str_replace('</h1>', " ", $page);
        $page = str_replace('<h2>', " ", $page);
        $page = str_replace('</h2>', " ", $page);
        $page = str_replace('<h3>', " ", $page);
        $gege = str_replace('</h3>', " ", $page);
        $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
        $page = str_replace('>>>', "(", $page);
        $page = str_replace('<<<', ")", $page);
        $page = str_replace('<u>', " ", $page);
        $page = str_replace('</u>', "\n", $page);
        $page = str_replace('<b>', " ", $page);
        $page = str_replace('</b>', " ", $page);
        $page = str_replace('<o:p>', " ", $page);
        $page = str_replace('</o:p>', " ", $page);
        $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
        $page = str_replace('<del>', " ", $page);
        $page = str_replace('</del>', " ", $page);
        $page = str_replace('/', "-", $page);
        $page = str_replace('<em>', " ", $page);
        $page = str_replace('<-em>', " ", $page);

	$page_comeco = "Seja Bem-vindo a Loja Oficial da Pe??a Agora no Mercado Livre.";

	$page = $page_comeco."

O PE??A AGORA AUTOPE??AS ?? uma loja de pe??as e acess??rios que atende todo o Brasil, sempre prezando pela agilidade e bom atendimento! Mais de 150 mil pe??as para melhor lhe atender!   

------------------------------------------------------------------------------------------

".$page."

------------------------------------------------------------------------------------------

1) Porque informar o chassi do ve??culo? 

Solicitamos o chassi para que dessa forma possamos ter acesso ao cat??logo da montadora e desta forma ter 100% de assertividade minando qualquer possibilidade de erro. Pode enviar tamb??m modelo e ano do veiculo

2) Qual o prazo e custo de entrega dos pedidos?

O prazo de entrega poder?? variar de acordo com a localidade de destino da encomenda, ser?? informado com o CEP na p??gina do produto e ?? exibido no abastecimento do carrinho de compras, antes da confirma????o do pedido. O custo de envio ser?? mostrado com base no total da compra e sua localiza????o, no checkout, no momento antes da compra.

3) Qual o prazo para realizar uma troca?

N??o abra reclama????o! A forma mais r??pida de resolvermos o problema seja de produto errado, produto quebrado, ?? falando diretamente com a gente no chat ou nos contatos que disponibilizamos. No caso de arrependimento da compra voc?? tem at?? 7 dias, ap??s a compra, para solicitar o estorno do pagamento, ap??s essa data o mesmo j?? entrar?? em processo de garantia

4)  Ressarcimento de valores?

O valor do produto ser?? devolvido de acordo com a forma de pagamento utilizada na compra e desde que observadas ??s condi????es descritas acima.

5)  Como s??o feitos os estornos?

O estorno ser?? feito na conta corrente em at?? 10 (dez) dias ??teis. N??o ser?? concedido cr??dito a terceiros, j?? em rela????o ao cart??o de cr??dito, estorno poder?? ocorrer em at?? 2 (duas) faturas subsequentes. Este procedimento ?? de responsabilidade da administradora do cart??o utilizado


6)  Sou pessoa jur??dica, como realizar um pedido?

Pessoa jur??dica pode ter acr??scimo de imposto.

-Utilize o campo de perguntas em caso de d??vidas e somente clique em comprar ap??s ter plena certeza em honrar a compra e concordar com as condi????es descritas acima.

 ------------------------------------------------------------------------------------------

- ATEN????O PARA OS DADOS DE ENTREGA:

- Confira o seu endere??o de entrega cadastrado, n??o alteramos endere??os ap??s a compra.

- HOR??RIO DE ATENDIMENTO:

- De Segunda ?? Sexta das 08h00 ??s 18h00";

        $body = ['plain_text' => $page];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            if($produto_filial->filial_id == 98 || $produto_filial->filial_id == 99 || $produto_filial->filial_id == 94){
                $body["plain_text"] = str_replace("Loja Oficial da ", "", $body["plain_text"]);
            }
            
            return self::atualizar($produto_filial, $body, 'Descri????o');
        }
    }

    public static function atualizarMarca($model)
    {
        $marca_produto_id = (int) $model->marca_produto_id;
        $marca_produto = MarcaProduto::findOne($marca_produto_id);
        // $marca_produto = MarcaProduto::find()->andWhere(['=', 'id', $model->marca_produto_id])->one();
        $marca = 'OPT';

        if ($marca_produto) {
            $marca = $marca_produto->nome;
        }

        $body = [
            'attributes' => [
                [
                    "id" => "BRAND",
                    "name" => "Marca",
                    "value_id" => null,
                    "value_name" => $marca,
                    "value_struct" => null,
                    "attribute_group_id" => "OTHERS",
                    "attribute_group_name" => "Outros"
                ],
            ]
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Marca');
        }
    }

    public static function atualizarCategoria($model)
    {
        $body = [
            "category_id" => utf8_encode($model->subcategoria->meli_id),
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Categoria');
        }
    }

    public static function atualizarVideo($model)
    {
        $video_id = "";
        if ($model->video == null) {
            $video_id = "rqvlr169tfE";
        } else {
            $video_complemento  = explode("=", $model->video);
            if (isset($video_complemento[1])) {
                $video_codigo       = explode("&", $video_complemento[1]);
                $video_id           = $video_codigo[0];
            } else {
                $retorno[$model->id]["meli_id_status"] = "Video n??o encontrado";
                return $retorno;
            }
        }

        $body = [
            "video_id" => $video_id,
        ];

        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            return self::atualizar($produto_filial, $body, 'Video');
        }
    }

    public static function atualizarPreco($valor = null, $produto_filial)
    {

        if (is_null($produto_filial->meli_id)) {
            return "Produto fora do ML";
        }

        if (is_null($produto_filial->filial->refresh_token_meli)) {
            return "Filial fora do ML";
        }

        $meli_id_array          = array();
        $meli_id_texto        = "'0',";
        $produtos_mercado_livre = array();

        if (is_null($valor)) {

            $produto_filial_id = $produto_filial->id;
            if (!is_null($produto_filial->produto_filial_origem_id)) {
                $produto_filial_origem = ProdutoFilial::find()->andWhere(["=", "id", $produto_filial->produto_filial_origem_id])->one();
                if ($produto_filial_origem) {
                    $produto_filial_id = $produto_filial_origem->id;
                } else {
                    return "Origem n??o encontrada!";
                }
            }

            $valor_produto_filial = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->orderBy(["id" => SORT_DESC])->one();
            if ($valor_produto_filial) {
                $valor = $valor_produto_filial->valor;
            } else {
                return "Sem valor cadastrado!";
            }
        }

        echo "(" . $valor . ")";
        $valor_principal    = $valor * 1.11;
        echo "(" . $valor_principal . ")";
        $valor_sem_juros    = $valor * 1.17;
        $valor_full         = $valor * 1.18;

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');

        $filial = Filial::find()->andWhere(['=', 'id', $produto_filial->filial_id])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;

        $valor_envio = 0;

        if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
            $meli_id_array[]         = $produto_filial->meli_id;
            $meli_id_texto        .= "'" . $produto_filial->meli_id . "',";
            $produtos_mercado_livre[]     = ["token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id, "tipo" => "meli_id"];
        }
        if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
            $meli_id_array[]        = $produto_filial->meli_id_sem_juros;
            $meli_id_texto              .= "'" . $produto_filial->meli_id_sem_juros . "',";
            $produtos_mercado_livre[]     = ["token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
        }
        if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
            $meli_id_array[]         = $produto_filial->meli_id_full;
            $meli_id_texto              .= "'" . $produto_filial->meli_id_full . "',";
            $produtos_mercado_livre[]     = ["token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id_full, "tipo" => "meli_id_full"];
        }

        $produtos_filial_duplicado = ProdutoFilial::find()->andWhere(["=", "produto_filial_origem_id", $produto_filial->id])->all();
        foreach ($produtos_filial_duplicado as $produto_filial_duplicado) {

            $filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', $produto_filial_duplicado->filial_id])->one();
            $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

            if (!is_null($produto_filial_duplicado->meli_id) && $produto_filial_duplicado->meli_id <> "") {
                $meli_id_array[] = $produto_filial_duplicado->meli_id;
                $meli_id_texto              .= "'" . $produto_filial_duplicado->meli_id . "',";
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id, "tipo" => "meli_id"];
            }
            if (!is_null($produto_filial_duplicado->meli_id_sem_juros) && $produto_filial_duplicado->meli_id_sem_juros <> "") {
                $meli_id_array[] = $produto_filial_duplicado->meli_id_sem_juros;
                $meli_id_texto              .= "'" . $produto_filial_duplicado->meli_id_sem_juros . "',";
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
            }
            if (!is_null($produto_filial_duplicado->meli_id_full) && $produto_filial_duplicado->meli_id_full <> "") {
                $meli_id_array[] = $produto_filial_duplicado->meli_id_full;
                $meli_id_texto              .= "'" . $produto_filial_duplicado->meli_id_full . "',";
                $produtos_mercado_livre[] = ["token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_full, "tipo" => "meli_id_full"];
            }
        }

        $meli_id_texto = trim($meli_id_texto, ",");

        $pedido_mercado_livre_produto = PedidoMercadoLivreProduto::find()->join("INNER JOIN", "pedido_mercado_livre", "pedido_mercado_livre.id = pedido_mercado_livre_id")
            ->where(" shipping_option_list_cost is not null and shipping_option_list_cost <> shipping_option_cost and produto_meli_id in (" . $meli_id_texto . ")")
            ->orderBy(["shipping_option_list_cost" => SORT_DESC])
            ->one();


        if ($pedido_mercado_livre_produto) {

            $quantidade = 0;
            $pedido_mercado_livre_produto_quantidades = PedidoMercadoLivreProduto::find()->andWhere(["=", "pedido_mercado_livre_id", $pedido_mercado_livre_produto->pedido_mercado_livre_id])
                ->all();
            foreach ($pedido_mercado_livre_produto_quantidades as $pedido_mercado_livre_produto_quantidade) {
                $quantidade +=  $pedido_mercado_livre_produto_quantidade->quantity;
            }

            $valor_envio = ($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_list_cost * 1.2) - ($pedido_mercado_livre_produto->pedidoMercadoLivre->shipping_option_cost);
            $valor_envio /= $quantidade;
            $valor_envio = ($valor_envio < 0) ? ($valor_envio * (-1)) : $valor_envio;
        } else {
            if ($valor >= 500) {
                if ($valor_envio < 11) {
                    $valor_envio = 11;
                }
            } elseif ($valor <= 65) {
                if ($valor_envio < 6) {
                    $valor_envio = 6;
                }
            } elseif ($valor > 65 and $valor < 500) {
                if ($valor_envio < 25) {
                    $valor_envio = 25;
                }
            }
        }

        if ($valor >= 500) {
            if ($valor_envio < 11) {
                $valor_envio = 11;
            }
        } elseif ($valor <= 65) {
            if ($valor_envio < 6) {
                $valor_envio = 6;
            }
        } elseif ($valor > 65 and $valor < 500) {
            if ($valor_envio < 25) {
                $valor_envio = 25;
            }
        }

        echo "(" . $valor_principal . ")";
        $valor_principal    += $valor_envio;
        $valor_sem_juros    += $valor_envio;
        $valor_full         += $valor_envio;

        foreach ($produtos_mercado_livre as $k => $produto_mercado_livre) {

            $valor_final = $valor_principal;
            switch ($produto_mercado_livre["tipo"]) {
                case "meli_id":
                    $valor_final = $valor_principal;
                    break;
                case "meli_id_sem_juros":
                    $valor_final = $valor_sem_juros;
                    break;
                case "meli_full":
                    $valor_final = $valor_full;
                    break;
            }

            //Atualiza????o Pre??o
            $body = ["price" => round($valor_final, 2)];
            $response = $meli->put("items/{$produto_mercado_livre["meli_id"]}?access_token=" . $produto_mercado_livre["token"], $body, []);
            if ($response['httpCode'] >= 300) {
                $produtos_mercado_livre[$k]["resposta_ml"] = $response;
            } else {
                $produtos_mercado_livre[$k]["resposta_ml"] = ["permalink" => $response["body"]->permalink];
            }
        }

        return [$produtos_mercado_livre, $valor, $valor_envio, $valor_principal, $valor_sem_juros, $valor_full];
    }
    
    function atualizarQuantidade($model){
        
        $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=', 'produto_id', $model->id])
                                                    ->andWhere(['<>', 'filial_id', 98])
                                                    ->orderBy(["filial_id" => SORT_ASC])
                                                    ->all();
        
        foreach($produtos_filiais as $produto_filial){
            //Atualiza????o Quantidade
            $body = ["available_quantity" => $produto_filial->quantidade,];
            if($produto_filial->filial_id == 96){
                //echo " - PE??A AGORA F??SICA";
                $body["sale_terms"] = [[
                    "id"            => "MANUFACTURING_TIME",
                    "value_id"      => null,
                    "value_name"    => null,
                ]];
            }
            else{
                $produto_filial_fisica = ProdutoFilial::find()  ->andWhere(["=", "filial_id", 96])
                ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                ->one();
                if($produto_filial_fisica){
                    //echo " - PE??A AGORA F??SICA Tamb??m";
                    if($produto_filial_fisica->quantidade > 0){
                        //echo " - PE??A AGORA F??SICA N??o zerado";
                        $body["sale_terms"] = [[
                            "id"            => "MANUFACTURING_TIME",
                            "value_id"      => null,
                            "value_name"    => null,
                        ]];
                    }
                    else{
                        //echo " - PE??A AGORA F??SICA zerado";
                        if($produto_filial->produto->dias_expedicao > 0){
                            $body["sale_terms"] = [[
                                "id"            => "MANUFACTURING_TIME",
                                "name"          => "Disponibilidade de estoque",
                                "value_id"      => null,
                                "value_name"    => $produto_filial->produto->dias_expedicao." dias",
                                "value_struct"  =>  [[
                                    "number"    => $produto_filial->produto->dias_expedicao,
                                    "unit"      => "dias"
                                ]],
                                "values"    =>  [[
                                    "id"        => null,
                                    "name"      => $produto_filial->produto->dias_expedicao." dias",
                                    "struct"    =>  [
                                        "number"    => $produto_filial->produto->dias_expedicao,
                                        "unit"      => "dias"
                                    ]
                                ]]
                            ]];
                        }
                    }
                }
                else{
                    if($produto_filial->produto->dias_expedicao > 0){
                        $body["sale_terms"] = [[
                            "id"            => "MANUFACTURING_TIME",
                            "name"          => "Disponibilidade de estoque",
                            "value_id"      => null,
                            "value_name"    => $produto_filial->produto->dias_expedicao." dias",
                            "value_struct"  =>  [[
                                "number"    => $produto_filial->produto->dias_expedicao,
                                "unit"      => "dias"
                            ]],
                            "values"    =>  [[
                                "id"        => null,
                                "name"      => $produto_filial->produto->dias_expedicao." dias",
                                "struct"    =>  [
                                    "number"    => $produto_filial->produto->dias_expedicao,
                                    "unit"      => "dias"
                                ]
                            ]]
                        ]];
                    }
                }
            }
            
            return self::atualizar($produto_filial, $body, 'Quantidade');
        }
    }
}
