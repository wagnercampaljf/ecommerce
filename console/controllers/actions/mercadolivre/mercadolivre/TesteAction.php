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

class TesteAction extends Action
{
    public function run($global_id)
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => 62])
            ->all();


        $produtos_filial_soro = ProdutoFilial::find()   ->joinWith('produto')
            ->andWhere(['like','produto.codigo_fabricante', 'S'])
            ->andWhere(['=', 'filial_id', 59])
            ->orderBy('produto_filial.id')
            ->all();


        foreach($produtos_filial_soro as $k => $produto_filial_soro){
            echo "\n".$k." - ".$produto_filial_soro->id." - ".$produto_filial_soro->produto->codigo_fabricante." - ".$produto_filial_soro->produto->codigo_global." - ".$produto_filial_soro->produto->nome;

            $nome = $produto_filial_soro->produto->nome;
            $nome = str_replace(" ", "%20", $nome);

            $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);

            if(isset($response_categoria_recomendada["body"][0]->category_id)){
                $response_categoria = $meli->get("categories/".$response_categoria_recomendada["body"][0]->category_id);

                echo $response_categoria["body"]->id." - ";

                foreach($response_categoria["body"]->settings->shipping_modes as $i => $modo){
                    echo " - ".$modo;
                    if($modo == "me2"){
                        $response_shipping = $meli->get("categories/".$response_categoria_recomendada["body"][0]->category_id."/shipping");
                        print_r($response_shipping);
                    }
                }
            }

            /*if(array_key_exists($produto_filial_soro->produto->codigo_fabricante, $produto_planilha_soro)){
                echo " - Produto encontrado";
            }
            else{
                echo " - Produto não encontrado";
            }*/
        }
    }
}
