<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateContaDuplicadaAction extends Action
{
    public function run()
    {
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

    	$filials = Filial::find()
            //->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [43]])
            //->andWhere(['<>','id', 92])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            //continue;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

        		$meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
                                        ->andWhere(['is not','meli_id',null])
                                        ->andWhere(['is not','produto_filial_origem_id',null])
                                        //->andWhere(['>','quantidade',0])
                    		            //->andWhere(['produto_filial.meli_id' => ['MLB883822941']])
                                        //->andWhere(['produto_filial.id' => [144224]])
                                        ->orderBy('produto_filial.id')
                                        ->all();
                echo "==>>>lkkl<<<==";

                foreach ($produtoFilials as $k => $produto_filial) {
                    //echo $produto_filial->produto->nome . "\n ";

                    if ($produto_filial->produto_filial_origem_id == NULL){
                        continue;    
                    }
                    
                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'id', $produto_filial->produto_filial_origem_id])->one();
                    
                    if ($produtoFilial->produto->fabricante_id != null) {

                        echo "\n".$k ." - Cópia: ".$produto_filial->id." - ".$produto_filial->meli_id ." - Origem: ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ";
		    
                        //Aqui começa o código
	                   
                        $body = [
        	                "pictures" => $produtoFilial->produto->getUrlImagesML(),
                        ];
	                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);

	                    if ($response['httpCode'] >= 300) {
	                        Yii::error($response['body'], 'mercado_livre_update');
				            echo "ERROR \n";
        			    } 
        			    else {
            				echo "ok \n";
            				print_r(ArrayHelper::getValue($response, 'body.permalink'));
	                    }
                        
                        //Aqui termina o código
                    }
                }
            }
            echo "Fim da filial: " . $filial->nome . "\n";
        }
    }
}
