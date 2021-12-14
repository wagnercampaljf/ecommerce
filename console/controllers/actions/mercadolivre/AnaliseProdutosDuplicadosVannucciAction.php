<?php

namespace console\controllers\actions\mercadolivre;

use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;
use common\models\Produto;


class AnaliseProdutosDuplicadosVannucciAction extends Action
{

    public function run($cliente = 1){

        echo "INÍCIO\n\n";

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;

        /*$LinhasArray = Array();
        $file = fopen('/var/tmp/ListaCompletaVannucci05-08-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);*/

        $produtosFilial = ProdutoFilial::find() ->joinWith('produto')
                                                ->andWhere(['=','filial_id',38])
                                                //->where(" codigo_fabricante in (select codigo_fabricante from (select count(*) as quantidade, codigo_fabricante from produto where codigo_fabricante like 'V%' group by codigo_fabricante) as foo where quantidade > 1) ")
                                                ->andWhere(['produto_id' => [231221]])
						->orderBy('codigo_fabricante ASC')
                                                //->orderBy('codigo_global DESC')
                                                ->all();

        foreach($produtosFilial as $i => $produtoFilial){

            //if($i == 82 || $i == 146 || $i == 147){
            if($i < 0){
                continue;
            }

            echo "\n".$i." - ".$produtoFilial->id." - ".$produtoFilial->produto->codigo_fabricante." - ".$produtoFilial->produto->codigo_global;
            continue;
            $ultimo_caracter = substr($produtoFilial->produto->codigo_global, -1);

            echo "(".$ultimo_caracter.")";

            //if($ultimo_caracter == "," || $ultimo_caracter == "." || $ultimo_caracter == "_"){
                if($produtoFilial->meli_id <> ""){
                    $response_itens = $meli->get("/items/".$produtoFilial->meli_id."?access_token=".$meliAccessToken);
                    echo " - Quantidade de venda: ".ArrayHelper::getValue($response_itens, 'body.sold_quantity');
                    if(ArrayHelper::getValue($response_itens, 'body.sold_quantity') == 0){
        	            $body = ["available_quantity" => $produtoFilial->quantidade,];
	                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
	                    if ($response['httpCode'] >= 300) {
	                        echo " - ".$produtoFilial->meli_id." - ERROR";
	                    }
	                    else {
	                        echo " - ".$produtoFilial->meli_id." - OK";
	                    }
		    }
                }
                else{
                    echo " - Não está no ML";
                }

                $produto = Produto::find()->andWhere(['=','id',$produtoFilial->produto_id])->one();
                var_dump($produto->delete());
            //}
        }

        echo "\n\nFIM!";
    }
}

