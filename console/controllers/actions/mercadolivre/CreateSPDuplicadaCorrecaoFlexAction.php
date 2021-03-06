<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\MarcaProduto;
use common\models\ProdutoCondicao;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use backend\functions\FunctionsML;
use common\models\PedidoMercadoLivreProduto;

class CreateSPDuplicadaCorrecaoFlexAction extends Action
{
    public function run()
    {
        
        $arquivo_log = fopen("/var/tmp/log_mercado_livre_create_SP3_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");
        
        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->andWhere(['id' => [43]])
                                    ->andWhere(["<>", "id", 93])
                                    ->andWhere(["<>", "id", 94])
                                    ->andWhere(["<>", "id", 96])
                                    ->andWhere(["<>", "id", 98])
                                    ->andWhere(["<>", "id", 99])
                                    ->andWhere(["<>", "id", 100])
                                    ->orderBy(["id" => SORT_ASC])
                                    ->all();
                                    
        foreach ($filials as $filial) {
            echo "\n\n==>".$filial->id." - ".$filial->nome."<==\n\n";
            
            if($filial->id < 38){
                echo " - Pular FILIAL!!";
                continue;
            }
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 300) {
                $meliAccessToken = $response->access_token;
                
                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->where("   quantidade > 0
                                                        and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial)
                                                    ")
                                                    //and produto_filial.id in (select distinct produto_filial_id from pedido_mercado_livre_produto)
                                             ->orderBy('id')
                                             ->all();
                 
                 /* @var $produtoFilial ProdutoFilial */
                 foreach ($produtoFilials as $k => $produtoFilial) {
                     echo "\n".$k." - ".$produtoFilial->id;//." - ".$produtoFilial->produto->nome;
                     //continue;
                     if($k <= 0 and $produtoFilial->filial_id == 38){
                         echo " - Pular!!";
                         continue;
                     }
                     
                     $produtos_filiais_conta_duplicada = ProdutoFilial::find()  ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                                ->andWhere(["is", "meli_id", null])
                                                                                ->andWhere(["=", "filial_id", 98])
                                                                                ->all();
                     
                     foreach ($produtos_filiais_conta_duplicada as $i => $produto_filial_conta_duplicada) {
                         if(is_null($produto_filial_conta_duplicada->meli_id_full)){
                             
                             $title = Yii::t('app', '{nome}', ['nome' => $produtoFilial->produto->nome ]);
                             
                             $nome = $produtoFilial->produto->nome;
                             
                             if(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                                 $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@11@', $nome);
                             }
                             elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                                 $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@10@', $nome);
                             }
                             elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                                 $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@9@', $nome);
                             }
                             elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                                 $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@8@', $nome);
                             }
                             
                             $titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
                             
                             $modo = "me2";
                             
                             //Atualiza????o da Condi????o
                             $produto_condicao = ProdutoCondicao::find()->andWhere(['=', 'id', $produtoFilial->produto->produto_condicao_id])->one();
                             
                             $condicao = "new";
                             $condicao_id = "2230284";
                             $condicao_name = "Novo";
                             if($produto_condicao){
                                 switch ($produto_condicao->meli_id){
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
                             
                             $marca_produto = MarcaProduto::find()->andWhere(['=','id',$produtoFilial->produto->marca_produto_id])->one();
                             $marca = "OPT";
                             if($marca_produto){
                                 $marca = $marca_produto->nome."/CONSULTAR";
                             }
                             
                             //Obter dados da categoria recomendada
                             $categoria_meli_id  = "";
                             $nome_array         = explode(" ", $titulo_novo);
                             $nome               =   $nome_array[0]
                             .((array_key_exists(1,$nome_array)) ? "%20".$nome_array[1] : "")
                             .((array_key_exists(2,$nome_array)) ? "%20".$nome_array[2] : "");
                             /*foreach($nome_array as $i => $nome_explode){
                              if($i>=2) break;
                              $nome .= "%20".$nome_array[$i+1];
                              }*/
                             //echo "\n".$nome; //continue;
                             
                             $categoria_meli_id = "MLB191833";
                             
                             $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                             //print_r($response_categoria_recomendada);
                             if ($response_categoria_recomendada['httpCode'] >= 300) {
                                 echo " - ERRO Categoria Recomendada";
                             }
                             else {
                                 foreach($response_categoria_recomendada["body"] as $j => $categoria_recomendada){
                                     //print_r($categoria_recomendada);
                                     $pos = strpos($categoria_recomendada->domain_id, "AUTOMOT");
                                     
                                     if ($pos === false) {
                                         echo "  - Categoria N??O AUTO";
                                     } else {
                                         echo " - OK Categoria Recomendada - Categoria AUTO";
                                         $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                                         $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                                         echo " - ".$categoria_meli_id.' - '.$categoria_meli_nome;
                                         
                                         break;
                                     }
                                     
                                 }
                             }
                             //die;
                             //continue;
                             
                             $body = [
                                 //"title" => (strlen($title) <= 60) ? $title : substr($title, 0,60),
                                 "title" => mb_substr($titulo_novo,0,60),
                                 "category_id" => utf8_encode($categoria_meli_id),
                                 "listing_type_id" => "bronze",
                                 "currency_id" => "BRL",
                                 "price" => utf8_encode(round($produtoFilial->getValorMercadoLivre(), 2)),
                                 "available_quantity" => utf8_encode($produtoFilial->quantidade),
                                 "seller_custom_field" =>utf8_encode($produtoFilial->id),
                                 "condition" => $condicao,
                                 "pictures" => $produtoFilial->produto->getUrlImagesML(),
                                 "shipping" => [
                                     "mode" => $modo,
                                     "local_pick_up" => true,
                                     "free_shipping" => false,
                                     "free_methods" => [],
                                     "tags" => ["self_service_out"],
                                 ],
                                 "sale_terms" => [
                                     [       "id" => "WARRANTY_TYPE",
                                         "value_id" => "2230280"
                                     ],
                                     [       "id" => "WARRANTY_TIME",
                                         "value_name" => "3 meses"
                                     ]
                                 ],
                                 'attributes' =>[
                                     [
                                         'id'                    => 'PART_NUMBER',
                                         'name'                  => 'N??mero de pe??a',
                                         'value_id'              => null,
                                         'value_name'            => $produtoFilial->produto->codigo_global,
                                         'value_struct'          => null,
                                         'values'                => [[
                                             'id'    => null,
                                             'name'  => $produtoFilial->produto->codigo_global,
                                             'struct'=> null,
                                         ]],
                                         'attribute_group_id'    => "OTHERS",
                                         'attribute_group_name'  => "Outros"
                                     ],
                                     [
                                         "id"=> "BRAND",
                                         "name"=> "Marca",
                                         "value_id"=> null,
                                         "value_name"=> $marca,
                                         "value_struct"=> null,
                                         "attribute_group_id"=> "OTHERS",
                                         "attribute_group_name"=> "Outros"
                                     ],
                                     [
                                         'id'                    => 'ITEM_CONDITION',
                                         'name'                  => 'Condi????o do item',
                                         'value_id'              => $condicao_id,
                                         'value_name'            => $condicao_name,
                                         'value_struct'          => null,
                                         'values'                => [[
                                             'id'    => $condicao_id,
                                             'name'  => $condicao_name,
                                             'struct'=> null,
                                         ]],
                                         'attribute_group_id'    => "OTHERS",
                                         'attribute_group_name'  => "Outros"
                                     ],
                                     [
                                         'id' => 'SKU',
                                         'name' => 'SKU',
                                         'value_id' => null,
                                         'value_name' => $produtoFilial->id.'_meli_id',
                                         'value_struct' => null,
                                         'values' => [
                                             [
                                                 'id' => null,
                                                 'name' => $produtoFilial->id.'_meli_id',
                                                 'struct' => null
                                             ]
                                         ],
                                         'attribute_group_id' => "OTHERS",
                                         'attribute_group_name' => "Outros"
                                     ]
                                 ]
                             ];
                             
                             $user_outro     = $meli->refreshAccessToken($produto_filial_conta_duplicada->filial->refresh_token_meli);
                             $response_outro = ArrayHelper::getValue($user_outro, 'body');
                             if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 300) {
                                 echo "\nLogou no ML";
                                 $meliAccessToken_outro = $response_outro->access_token;
                                 $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                                 //echo "<pre>"; print_r($response); echo "</pre>";
                                 if ($response['httpCode'] >= 300) {
                                     print_r($response);
                                     fwrite($arquivo_log, ';Produto n??o criado no ML(Duplicado');
                                 } else {
                                     $produto_filial_conta_duplicada->meli_id_full = $response['body']->id;
                                     if ($produto_filial_conta_duplicada->save()) {
                                         self::atualizarDescricao($produto_filial_conta_duplicada->produto, $produto_filial_conta_duplicada->meli_id_full, $meliAccessToken_outro, $meli);
                                         
                                         //$produto_filial_conta_duplicada->produto->atualizarMLDescricao();
                                         echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - ok(Conta duplicada)";
                                     } else {
                                         print_r($response);
                                         fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro(Conta duplicada)");
                                     }
                                 }
                                 //die;
                             }
                             else{
                                 echo "\nN??o logou no ML";
                             }
                         }
                         else{
                             echo " - Produto j?? no ML (Duplicado)";
                         }
                     }
                 }
            }
        }
        
        fclose($arquivo_log);
        
    }
    
    public static function atualizarDescricao($model, $meli_id, $token, $meli)
    {
        
        //self::$retorno = array();
        
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
        
        //$page_comeco = "Seja Bem-vindo a Loja Oficial da Pe??a Agora no Mercado Livre.";
        
        //$page = $page_comeco."
        $page = "
        
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
        
        $response = $meli->put("items/{$meli_id}" . "/description?access_token=" . $token, $body, []);
        if ($response['httpCode'] >= 300) {
            echo " - Descri????o n??o alterada";
        }
        else {
            echo " - Descri????o alterada";
        }
    }
}
