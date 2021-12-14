<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class PecaAgoraKitAtualizarPrecoAction extends Action
{
    public function run()
    {
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $produtos_filiais = ProdutoFilial::find()->andWhere(['=','filial_id',77])->all();
        
        foreach ($produtos_filiais as $k => $produto_filial){
            
            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id;
            
            $response_item = $meli->get("/items/".$produto_filial->meli_id."/?access_token=" . $meliAccessToken);
            //print_r($response_item); die;
            
            if($response_item["httpCode"] >= 300){
                echo " - Produto não encontrado no ML";
            }
            else{
                if($response_item["body"]->sold_quantity == 0){
                    echo "\n";print_r($response_item["body"]->permalink); echo "\n";
                    
                    $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=', 'produto_filial_id', $produto_filial->id])
                                                                        ->orderBy(['dt_inicio'=>SORT_DESC])
                                                                        ->one();
                    
                    if($valor_produto_filial){
                        
                        $valor_produto_filial_novo = new ValorProdutoFilial;
                        $valor_produto_filial_novo->produto_filial_id    = $produto_filial->id;
                        $valor_produto_filial_novo->valor                = $valor_produto_filial->valor * 1.2;
                        $valor_produto_filial_novo->valor_cnpj           = $valor_produto_filial->valor_cnpj * 1.2;
                        $valor_produto_filial_novo->dt_inicio            = date("Y-m-d H:i:s");
                        $valor_produto_filial_novo->promocao             = false;
                        $valor_produto_filial_novo->valor_compra         = $valor_produto_filial->valor_compra;
                        if($valor_produto_filial_novo->save()){
                            echo " - Preço atualizado";
                        }
                        else{
                            echo " - Preço não atualizado";
                        }
                    }
                }
                else{
                    echo " - Produto com venda";
                }
            }
        }
        
        echo "\n\nFIM da rotina de atualizacao do preço!";

    }
}
