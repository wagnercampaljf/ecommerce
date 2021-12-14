<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Imagens;
use common\models\Filial;
use common\models\MarkupDetalhe;
use common\models\MarkupMestre;
use common\models\ValorProdutoFilial;

class GerarRelatorioProdutosMarkupAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_conta_principal_markup_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;nome;categoria;preço;link\n");
        
        $filial     = Filial::find()->andWhere(["=", "id", 72])->one();
        $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
        $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response   = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";
            
            $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();
            
            $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();
            
            $faixas = [];
            
            foreach ($markups_detalhe as $markup_detalhe){
                $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];
            }
            
            $x = 0;
            $i = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 1773){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        //print_r($response_item);die;
                        
                        $produto_filial_id  = "Produto sem vínculo";
                        $markup             = "Valor de compra não cadastrado";
                        $markup_observacao  = "";
                        $produto_filial     = ProdutoFilial::find() ->orWhere(["=", "meli_id", $meli_id])
                                                                ->orWhere(["=", "meli_id_sem_juros", $meli_id])
                                                                ->one();
                        if($produto_filial){
                            
                            $produto_filial_id = $produto_filial->id;
                            
                            $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(["=", "produto_filial_id", $produto_filial->id])
                                                                                ->orderBy(["dt_inicio" => SORT_DESC])
                                                                                ->one();
                            if($valor_produto_filial){
                                if(!is_null($valor_produto_filial->valor_compra) && $valor_produto_filial->valor_compra > 0){
                                    $preco_compra   = (float) $valor_produto_filial->valor_compra;
                                    
                                    foreach ($faixas as $k => $faixa) {
                                        if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                                            $markup = $faixa[2];
                                            if ($faixa[3]){
                                                $markup_observacao = "Valor Absoluto";
                                            }
                                            else{
                                                $markup_observacao = "Porcentagem";
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.status').";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.price').";".ArrayHelper::getValue($response_item, 'body.available_quantity').";".ArrayHelper::getValue($response_item, 'body.sold_quantity').";".$markup.";".$markup_observacao.";".ArrayHelper::getValue($response_item, 'body.permalink').";".$produto_filial_id."\n");
                        
                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                    }
                }
                
                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 
