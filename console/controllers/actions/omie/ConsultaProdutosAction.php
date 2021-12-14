<?php

namespace console\controllers\actions\omie;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use console\controllers\actions\omie\Omie2;
use console\models\SkyhubClient;
use yii\helpers\Json;

class ConsultaProdutosAction extends Action
{
    public function run($global_id)
    {
        /*$meli = new Omie(static::APP_ID, static::SECRET_KEY);

        $user = $meli->refreshAccessToken('TG-5b5f1c7be4b09e746623a2ca-193724256');
        
        $response = ArrayHelper::getValue($user, 'body');
        //print_r($response);
        $meliAccessToken = $response->access_token;
            
        $response = $meli->get("items/{'MLB1093023599'}?access_token=" . $meliAccessToken);
        //print_r($response);*/
        
        echo "Criando produtos...\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['=', 'id', 72])->one();

        echo "\n entrou \n";
        
        $produtoFilials = $filial->getProdutoFilials()->andWhere(['=','id',$global_id])->all();
        
        foreach ($produtoFilials as $produtoFilial) {
            echo "\n for \n";
            
            $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não categoria",
                'error_yii');
                echo "1\n";
                return;
            }
            if (is_null($produtoFilial->valorMaisRecente)) {
                Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                'error_yii');
                echo "2\n";
                return;
            }
            $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',
                ['produto' => $produtoFilial]);
            
            $title = Yii::t('app', '{nome} ({cod})', [
                'cod' => $produtoFilial->produto->codigo_global,
                'nome' => $produtoFilial->produto->nome
            ]);
            switch ($produtoFilial->envio) {
                case 1:
                    $modo = "me2";
                    break;
                case 2:
                    $modo = "not_specified";
                    break;
                case 3:
                    $modo = "custom";
                    break;
            }
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => static::APP_KEY_OMIE,
                "app_secret" => static::APP_SECRET_OMIE,
                "param" => [
                            "codigo_produto" => "",
                            //"codigo_produto" => 344218142,
                            "codigo_produto_integracao" => "",
                            "codigo" => "0000006"
			    //"codigo" => $produtoFilial->produto->codigo_global
                ]
            ];
            
            $response = $meli->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            echo "123::";var_dump($response);
            
            //https://app.omie.com.br/api/v1/geral/produtos/?JSOM={"call":"ConsultarProduto","app_key":"1560731700","app_secret":"226dcf372489bb45ceede61bfd98f0f1","param":[{"codigo_produto":344218142,"codigo_produto_integracao":"","codigo":""}]}
            
            //https://app.omie.com.br/api/v1/geral/produtos/?JSON={"call":"ConsultarProduto","app_key":"468080198586","app_secret":"7b3fb2b3bae35eca3b051b825b6d9f43","param":[{"codigo_produto":344218142,"codigo_produto_integracao":"","codigo":""}]}
        }
    //$omie = new Omie2();
        //$products = $omie->products()->findAll(1, 10);
        //var_dump($products);
    }
}

