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

    static $retorno = array();

    public static function atualizarMercadoLivre(Produto $model)
    {

        self::atualizarNome($model);
        self::atualizarCondicao($model);
        self::atualizarVideo($model);
        self::atualizarMarca($model);
        self::atualizarCodigo($model);
        self::atualizarCategoria($model);
        self::atualizarDescricao($model);
        self::atualizarImagens($model);
        self::atualizarQuantidade($model);

        return self::$retorno;
    }

    public static function atualizar($produto_filial, $body, $origem)
    {

        self::$retorno[$produto_filial->id]["filial_id"] = $produto_filial->filial_id;
        self::$retorno[$produto_filial->id]["tipo_conta_ml"] = "Mercado Livre Principal";

        if (is_null($produto_filial->filial->refresh_token_meli) or $produto_filial->filial->refresh_token_meli == "") {
            echo " - Filial fora do ML";
            return;
        }

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user               = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
        $response           = ArrayHelper::getValue($user, 'body');
        $meliAccessToken    = $response->access_token;
        $urlDescricao = $origem == 'Descrição' ? '/description' : '';

        if (!is_null($produto_filial->meli_id) && $produto_filial->meli_id <> "") {
            self::$retorno[$produto_filial->id]["meli_id"] = $produto_filial->meli_id;
            $response = $meli->put("items/{$produto_filial->meli_id}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                self::$retorno[$produto_filial->id]["meli_id_status_" . $origem] = "$origem não alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "";
            } else {
                self::$retorno[$produto_filial->id]["meli_id_status_" . $origem] = "$origem alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "";
                if (isset($response["body"]->permalink)) {
                    self::$retorno[$produto_filial->id]['permalink'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
            self::$retorno[$produto_filial->id]["meli_id_sem_juros"] = $produto_filial->meli_id_sem_juros;
            $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                self::$retorno[$produto_filial->id]["meli_id_sem_juros_status_" . $origem] = "$origem não alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Sem Juros";
            } else {
                self::$retorno[$produto_filial->id]["meli_id_sem_juros_status_" . $origem] = "$origem alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Sem Juros";
                if (isset($response["body"]->permalink)) {
                    self::$retorno[$produto_filial->id]['permalink_sem_juros'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
            self::$retorno[$produto_filial->id]["meli_id_full"] = $produto_filial->meli_id_full;
            $response = $meli->put("items/{$produto_filial->meli_id_full}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                self::$retorno[$produto_filial->id]["meli_id_full_status_" . $origem] = "$origem não alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Full";
            } else {
                self::$retorno[$produto_filial->id]["meli_id_full_status_" . $origem] = "$origem alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Full";
                if (isset($response["body"]->permalink)) {
                    self::$retorno[$produto_filial->id]['permalink_full'] = $response["body"]->permalink;
                }
            }
        }
        if (!is_null($produto_filial->meli_id_flex) && $produto_filial->meli_id_flex <> "") {
            self::$retorno[$produto_filial->id]["meli_id_flex"] = $produto_filial->meli_id_flex;
            $response = $meli->put("items/{$produto_filial->meli_id_flex}" . $urlDescricao . "?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                self::$retorno[$produto_filial->id]["meli_id_flex_status_" . $origem] = "$origem não alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Flex";
            } else {
                self::$retorno[$produto_filial->id]["meli_id_flex_status_" . $origem] = "$origem alterado";
                self::$retorno[$produto_filial->id]["tipo_ml"] = "Flex";
                if (isset($response["body"]->permalink)) {
                    self::$retorno[$produto_filial->id]['permalink_flex'] = $response["body"]->permalink;
                }
            }
        }

        //CONTA DUPLICADA
        $produtos_filial_duplicado = ProdutoFilial::find()->andWhere(["=", "produto_filial_origem_id", $produto_filial->id])->all();
        foreach ($produtos_filial_duplicado as $produto_filial_duplicado) {

            if ($origem == 'Descrição') {
                $body["plain_text"] = str_replace("Loja Oficial da ", "", $body["plain_text"]);
            }

            self::$retorno[$produto_filial_duplicado->id]["filial_id"] = $produto_filial_duplicado->filial_id;
            self::$retorno[$produto_filial_duplicado->id]["tipo_conta_ml"] = "Mercado Livre Filial";
            $filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', $produto_filial_duplicado->filial_id])->one();
            $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
            $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
            $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

            if (!is_null($produto_filial_duplicado->meli_id) && $produto_filial_duplicado->meli_id <> "") {
                self::$retorno[$produto_filial_duplicado->id]["meli_id"] = $produto_filial_duplicado->meli_id;
                $response = $meli->put("items/{$produto_filial_duplicado->meli_id}" . $urlDescricao . "?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if ($response['httpCode'] >= 300) {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_status_" . $origem] = "$origem não alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "";
                } else {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_status_" . $origem] = "$origem alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "";
                    if (isset($response["body"]->permalink)) {
                        self::$retorno[$produto_filial_duplicado->id]['permalink'] = $response["body"]->permalink;
                    }
                }
            }
            if (!is_null($produto_filial_duplicado->meli_id_sem_juros) && $produto_filial_duplicado->meli_id_sem_juros <> "") {
                self::$retorno[$produto_filial_duplicado->id]["meli_id_sem_juros"] = $produto_filial_duplicado->meli_id_sem_juros;
                $response = $meli->put("items/{$produto_filial_duplicado->meli_id_sem_juros}" . $urlDescricao . "?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if ($response['httpCode'] >= 300) {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_sem_juros_status_" . $origem] = "$origem não alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Sem Juros";
                } else {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_sem_juros_status_" . $origem] = "$origem alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Sem Juros";
                    if (isset($response["body"]->permalink)) {
                        self::$retorno[$produto_filial_duplicado->id]['permalink_sem_juros'] = $response["body"]->permalink;
                    }
                }
            }
            if (!is_null($produto_filial_duplicado->meli_id_full) && $produto_filial_duplicado->meli_id_full <> "") {
                self::$retorno[$produto_filial_duplicado->id]["meli_id_full"] = $produto_filial_duplicado->meli_id_full;
                $response = $meli->put("items/{$produto_filial_duplicado->meli_id_full}" . $urlDescricao . "?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if ($response['httpCode'] >= 300) {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_full_status_" . $origem] = "$origem não alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Full";
                } else {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_full_status_" . $origem] = "$origem alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Full";
                    if (isset($response["body"]->permalink)) {
                        self::$retorno[$produto_filial_duplicado->id]['permalink_full'] = $response["body"]->permalink;
                    }
                }
            }
            if (!is_null($produto_filial_duplicado->meli_id_flex) && $produto_filial_duplicado->meli_id_flex <> "") {
                self::$retorno[$produto_filial_duplicado->id]["meli_id_flex"] = $produto_filial_duplicado->meli_id_flex;
                $response = $meli->put("items/{$produto_filial_duplicado->meli_id_flex}" . $urlDescricao . "?access_token=" . $meliAccessToken_conta_duplicada, $body, []);
                if ($response['httpCode'] >= 300) {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_flex_status_" . $origem] = "$origem não alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Flex";
                } else {
                    self::$retorno[$produto_filial_duplicado->id]["meli_id_flex_status_" . $origem] = "$origem alterado";
                    self::$retorno[$produto_filial_duplicado->id]["tipo_ml"] = "Flex";
                    if (isset($response["body"]->permalink)) {
                        self::$retorno[$produto_filial_duplicado->id]['permalink_flex'] = $response["body"]->permalink;
                    }
                }
            }
        }

        return self::$retorno;
    }

    public static function atualizarCodigo($model)
    {
        $body = [
            'attributes' => [
                [
                    "id" => "PART_NUMBER",
                    "name" => "Número de peça",
                    "value_id" => null,
                    "value_name" => $model->codigo_global,
                    "value_struct" => null,
                    "attribute_group_id" => "OTHERS",
                    "attribute_group_name" => "Outros"
                ],
            ]
        ];

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            return self::atualizar($produto_filial, $body, 'Codigo');
        }
    }

    public static function atualizarNome($model)
    {
        $title = Yii::t('app', '{nome}', ['nome' => $model->nome]);
        $title = str_replace("IVECO", "", str_replace("Iveco", "", $title));
        $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Nome');
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
                    'name'                  => 'Condição do item',
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

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Condição');
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

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Modo Envio');
        }
    }

    public static function atualizarImagens($model)
    {
        $body = [
            "pictures" => $model->getUrlImagesML(),
        ];

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Imagens');
        }
    }

    public static function atualizarDescricao($model)
    {

        //self::$retorno = array();

        $page = $model->nome . "\n\nAPLICAÇÃO:\n\n" . $model->aplicacao . $model->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo é o do Motorista.\n\n* Lado Direito é o do Passageiro.";
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

        $page_comeco = "Seja Bem-vindo a Loja Oficial da Peça Agora no Mercado Livre.";

        $page = $page_comeco . "

O PEÇA AGORA AUTOPEÇAS é uma loja de peças e acessórios que atende todo o Brasil, sempre prezando pela agilidade e bom atendimento! Mais de 150 mil peças para melhor lhe atender!   

------------------------------------------------------------------------------------------

" . $page . "

------------------------------------------------------------------------------------------

1) Porque informar o chassi do veículo? 

Solicitamos o chassi para que dessa forma possamos ter acesso ao catálogo da montadora e desta forma ter 100% de assertividade minando qualquer possibilidade de erro. Pode enviar também modelo e ano do veiculo

2) Qual o prazo e custo de entrega dos pedidos?

O prazo de entrega poderá variar de acordo com a localidade de destino da encomenda, será informado com o CEP na página do produto e é exibido no abastecimento do carrinho de compras, antes da confirmação do pedido. O custo de envio será mostrado com base no total da compra e sua localização, no checkout, no momento antes da compra.

3) Qual o prazo para realizar uma troca?

Não abra reclamação! A forma mais rápida de resolvermos o problema seja de produto errado, produto quebrado, é falando diretamente com a gente no chat ou nos contatos que disponibilizamos. No caso de arrependimento da compra você tem até 7 dias, após a compra, para solicitar o estorno do pagamento, após essa data o mesmo já entrará em processo de garantia

4)  Ressarcimento de valores?

O valor do produto será devolvido de acordo com a forma de pagamento utilizada na compra e desde que observadas às condições descritas acima.

5)  Como são feitos os estornos?

O estorno será feito na conta corrente em até 10 (dez) dias úteis. Não será concedido crédito a terceiros, já em relação ao cartão de crédito, estorno poderá ocorrer em até 2 (duas) faturas subsequentes. Este procedimento é de responsabilidade da administradora do cartão utilizado


6)  Sou pessoa jurídica, como realizar um pedido?

Pessoa jurídica pode ter acréscimo de imposto.

-Utilize o campo de perguntas em caso de dúvidas e somente clique em comprar após ter plena certeza em honrar a compra e concordar com as condições descritas acima.

 ------------------------------------------------------------------------------------------

- ATENÇÃO PARA OS DADOS DE ENTREGA:

- Confira o seu endereço de entrega cadastrado, não alteramos endereços após a compra.

- HORÁRIO DE ATENDIMENTO:

- De Segunda à Sexta das 08h00 às 18h00";

        $body = ['plain_text' => $page];

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            if ($produto_filial->filial_id == 98 || $produto_filial->filial_id == 99 || $produto_filial->filial_id == 94) {
                $body["plain_text"] = str_replace("Loja Oficial da ", "", $body["plain_text"]);
            }

            self::atualizar($produto_filial, $body, 'Descrição');

            //return self::$retorno;
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

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Marca');
        }
    }

    public static function atualizarCategoria($model)
    {
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $categoria_meli_id   = "";
        $nome = str_replace(" ","%20",$model->nome);

        $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);

        if ($response_categoria_recomendada['httpCode'] >= 300) {
            echo " - ERRO Categoria Recomendada";
            $categoria_meli_id =$model->subcategoria->meli_id;

        }
        else {
            if (!empty($response_categoria_recomendada["body"])) {
                foreach(ArrayHelper::getValue($response_categoria_recomendada, 'body') as $categoria){
                    $response_categoria = $meli->get("categories/".ArrayHelper::getValue($categoria, 'category_id'));
                    $shipping_modes = ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes');
                    if(in_array("me2", $shipping_modes)){
                        echo " - ".ArrayHelper::getValue($categoria, 'category_id')." - Categoria Recomendada - ME";

                        $body = [
                            "shipping"      => [
                                "mode"          => "me2",
                                "methods"       => [],
                                "local_pick_up" => true,
                                "free_shipping" => false,
                                "logistic_type" => "cross_docking",
                            ],
                            "category_id" => ArrayHelper::getValue($categoria, 'category_id')
                        ];

                        
                    }

                }
            }else{
                echo 'Sem Me';
            }
            
        }
        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Categoria');
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
                $retorno[$model->id]["meli_id_status"] = "Video não encontrado";
                return $retorno;
            }
        }

        $body = [
            "video_id" => $video_id,
        ];

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $k => $produto_filial) {
            self::atualizar($produto_filial, $body, 'Video');
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
                    return "Origem não encontrada!";
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
            $produtos_mercado_livre[]     = ["id" => $produto_filial->id, "token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id, "tipo" => "meli_id"];
        }
        if (!is_null($produto_filial->meli_id_sem_juros) && $produto_filial->meli_id_sem_juros <> "") {
            $meli_id_array[]        = $produto_filial->meli_id_sem_juros;
            $meli_id_texto              .= "'" . $produto_filial->meli_id_sem_juros . "',";
            $produtos_mercado_livre[]     = ["id" => $produto_filial->id, "token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
        }
        if (!is_null($produto_filial->meli_id_full) && $produto_filial->meli_id_full <> "") {
            $meli_id_array[]         = $produto_filial->meli_id_full;
            $meli_id_texto              .= "'" . $produto_filial->meli_id_full . "',";
            $produtos_mercado_livre[]     = ["id" => $produto_filial->id, "token" => $meliAccessToken, "meli_id" => $produto_filial->meli_id_full, "tipo" => "meli_id_full"];
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
                $produtos_mercado_livre[] = ["id" => $produto_filial_duplicado->id, "token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id, "tipo" => "meli_id"];
            }
            if (!is_null($produto_filial_duplicado->meli_id_sem_juros) && $produto_filial_duplicado->meli_id_sem_juros <> "") {
                $meli_id_array[] = $produto_filial_duplicado->meli_id_sem_juros;
                $meli_id_texto              .= "'" . $produto_filial_duplicado->meli_id_sem_juros . "',";
                $produtos_mercado_livre[] = ["id" => $produto_filial_duplicado->id, "token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_sem_juros, "tipo" => "meli_id_sem_juros"];
            }
            if (!is_null($produto_filial_duplicado->meli_id_full) && $produto_filial_duplicado->meli_id_full <> "") {
                $meli_id_array[] = $produto_filial_duplicado->meli_id_full;
                $meli_id_texto              .= "'" . $produto_filial_duplicado->meli_id_full . "',";
                $produtos_mercado_livre[] = ["id" => $produto_filial_duplicado->id, "token" => $meliAccessToken_conta_duplicada, "meli_id" => $produto_filial_duplicado->meli_id_full, "tipo" => "meli_id_full"];
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

            //Atualização Preço
            $body = ["price" => round($valor_final, 2)];

            self::$retorno[$produto_mercado_livre['id']]["meli_id"] = $produto_mercado_livre['meli_id'];
            $response = $meli->put("items/{$produto_mercado_livre['meli_id']}?access_token=" . $produto_mercado_livre['token'], $body, []);
            if ($response['httpCode'] >= 300) {
                self::$retorno[$produto_mercado_livre['id']]["meli_id_status_preco"] = "Preço não alterado";
                self::$retorno[$produto_mercado_livre["id"]]["tipo_ml"] = $produto_mercado_livre["tipo"];
            } else {
                self::$retorno[$produto_mercado_livre['id']]["meli_id_status_preco"] = "Preço alterado";
                self::$retorno[$produto_mercado_livre["id"]]["tipo_ml"] = $produto_mercado_livre["tipo"];
                if (isset($response["body"]->permalink)) {
                    self::$retorno[$produto_mercado_livre['id']]['permalink'] = $response["body"]->permalink;
                }
            }
            // $response = $meli->put("items/{$produto_mercado_livre["meli_id"]}?access_token=" . $produto_mercado_livre["token"], $body, []);
            // if ($response['httpCode'] >= 300) {
            //     $produtos_mercado_livre[$k]["resposta_ml"] = $response;
            // } else {
            //     $produtos_mercado_livre[$k]["resposta_ml"] = ["permalink" => $response["body"]->permalink];
            // }
        }

        return self::$retorno;
    }

    public static function atualizarQuantidade($model)
    {

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $model->id])
            ->andWhere(['<>', 'filial_id', 98])
            ->orderBy(["filial_id" => SORT_ASC])
            ->all();

        foreach ($produtos_filiais as $produto_filial) {
            //Atualização Quantidade
            $body = ["available_quantity" => $produto_filial->quantidade,];
            if ($produto_filial->filial_id == 96) {
                //echo " - PEÇA AGORA FÍSICA";
                $body["sale_terms"] = [[
                    "id"            => "MANUFACTURING_TIME",
                    "value_id"      => null,
                    "value_name"    => null,
                ]];
            } else {
                $produto_filial_fisica = ProdutoFilial::find()->andWhere(["=", "filial_id", 96])
                    ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                    ->one();
                if ($produto_filial_fisica) {
                    //echo " - PEÇA AGORA FÍSICA Também";
                    if ($produto_filial_fisica->quantidade > 0) {
                        //echo " - PEÇA AGORA FÍSICA Não zerado";
                        $body["sale_terms"] = [[
                            "id"            => "MANUFACTURING_TIME",
                            "value_id"      => null,
                            "value_name"    => null,
                        ]];
                    } else {
                        //echo " - PEÇA AGORA FÍSICA zerado";
                        if ($produto_filial->produto->dias_expedicao > 0) {
                            $body["sale_terms"] = [[
                                "id"            => "MANUFACTURING_TIME",
                                "name"          => "Disponibilidade de estoque",
                                "value_id"      => null,
                                "value_name"    => $produto_filial->produto->dias_expedicao . " dias",
                                "value_struct"  =>  [[
                                    "number"    => $produto_filial->produto->dias_expedicao,
                                    "unit"      => "dias"
                                ]],
                                "values"    =>  [[
                                    "id"        => null,
                                    "name"      => $produto_filial->produto->dias_expedicao . " dias",
                                    "struct"    =>  [
                                        "number"    => $produto_filial->produto->dias_expedicao,
                                        "unit"      => "dias"
                                    ]
                                ]]
                            ]];
                        }
                    }
                } else {
                    if ($produto_filial->produto->dias_expedicao > 0) {
                        $body["sale_terms"] = [[
                            "id"            => "MANUFACTURING_TIME",
                            "name"          => "Disponibilidade de estoque",
                            "value_id"      => null,
                            "value_name"    => $produto_filial->produto->dias_expedicao . " dias",
                            "value_struct"  =>  [[
                                "number"    => $produto_filial->produto->dias_expedicao,
                                "unit"      => "dias"
                            ]],
                            "values"    =>  [[
                                "id"        => null,
                                "name"      => $produto_filial->produto->dias_expedicao . " dias",
                                "struct"    =>  [
                                    "number"    => $produto_filial->produto->dias_expedicao,
                                    "unit"      => "dias"
                                ]
                            ]]
                        ]];
                    }
                }
            }

            self::atualizar($produto_filial, $body, 'Quantidade');

            //return self::$retorno;
        }
    }
}
