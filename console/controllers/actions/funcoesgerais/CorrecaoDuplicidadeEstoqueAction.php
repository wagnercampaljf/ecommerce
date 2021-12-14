<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;

class CorrecaoDuplicidadeEstoqueAction extends Action
{

    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $subquery = "(select produto_filial.filial_id, produto_filial.produto_id, count(*) as quantidade from produto_filial group by produto_filial.filial_id, produto_filial.produto_id)";

        $estoque_duplicado = ProdutoFilial::find()->select('foo.quantidade, foo.filial_id, foo.produto_id')
                                                ->andWhere(['>','foo.quantidade',1])
                                                ->from(['foo' => $subquery])
                                                ->all();

        $filial_id = 0;
        $meliAccessToken = "";

        foreach ($estoque_duplicado as $i => $estoque){
            echo "\n".$i." - ".$estoque->filial_id." - ".$estoque->produto_id." - ".$estoque->quantidade;

            $produtos_filiais = ProdutoFilial::find()   ->andWhere(['=','produto_id',$estoque->produto_id])
                                                        ->andWhere(['=','filial_id',$estoque->filial_id])
                                                        ->all();

           foreach ($produtos_filiais as $k => $produto_filial){
                echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id;
                //continue;

                if($produto_filial->meli_id <> null and $produto_filial->meli_id <> ""){
                    echo " - Produto NO ML";

                    if($filial_id <> $estoque->filial_id){
                        $filial_id = $estoque->filial_id;
                        $filial = Filial::find()->andWhere(['=','id',$estoque->filial_id])->one();
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                    }

                    $response = $meli->get("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken);
                    if ($response['httpCode'] >= 300) {
                        echo " - Erro";
	     		if($produto_filial->delete()){
	                         echo " - Deletado";
	                }
	                else{
	                         echo " - Não Deletado";
	                }
                    }
                    else{
                        echo " - OK - ".ArrayHelper::getValue($response, 'body.sold_quantity');
			if(ArrayHelper::getValue($response, 'body.sold_quantity') == 0){
				$body = [
	                                "available_quantity" => 0,//utf8_encode($produtoFilial->quantidade),
				];
				$response_alteracao = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body,[]);

				if ($response['httpCode'] >= 300) {
                		        echo " - Não zerado ML";
		                }
				else{
		                        echo " - Zerado ML";
				}

				if($produto_filial->delete()){
				        echo " - Deletado";
                                }
                                else{
                                        echo " - Não Deletado";
                                }

				//die;
				break;
			}
			else{
				continue;
			}
                    }
                }
                else{
                    echo " - Produto FORA ML";

		    if($produto_filial->delete()){
                         echo " - Deletado";
                    }
                    else{
                         echo " - Não Deletado";
                    }
                    break;
                }
            }
        }
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
