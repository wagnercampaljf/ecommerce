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

class ConsultaContaCorrenteAction extends Action
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
                "call" => "PesquisarContaCorrente",
                "app_key" => static::APP_KEY_OMIE,
                "app_secret" => static::APP_SECRET_OMIE,
                "param" => [
                            "pagina" => 1,
                            "registros_por_pagina" => 100,
                            "apenas_importado_api" => "N"
                ]
            ];
            $response = $meli->consulta_conta_corrente("api/v1/geral/contacorrente/?JSON=",$body);
            echo "123::";var_dump($response);
        }
    //$omie = new Omie2();
        //$products = $omie->products()->findAll(1, 10);
        //var_dump($products);
    }
}

