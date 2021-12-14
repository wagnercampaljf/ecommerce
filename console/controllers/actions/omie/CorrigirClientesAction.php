<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;

class CorrigirClientesAction extends Action
{
    public function run($global_id)
    {
        echo "Consultando cliente omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);

        $body = [
            "call" => "ListarClientes",
            "app_key" => static::APP_KEY_OMIE,
            "app_secret" => static::APP_SECRET_OMIE,
            "param" => [
                        "pagina"                =>1,
                        "registros_por_pagina"  => 50,
                        "apenas_importado_api"  => "N",
                        //"codigo_produto" => "",
                        //"codigo_produto_integracao" => "",
                        //"codigo" => $produtoFilial->produto->codigo_global
            ]
        ];
        
        $response = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
        //print_r($response); die;
        
        $quantidade_paginas = ArrayHelper::getValue($response,'body.total_de_paginas');
        
        for ($x = 1; $x <= $quantidade_paginas; $x++){
            echo "\n".$x." - ";
            
            $body = [
                "call" => "ListarClientes",
                "app_key" => static::APP_KEY_OMIE,
                "app_secret" => static::APP_SECRET_OMIE,
                "param" => [
                    "pagina"                =>$x,
                    "registros_por_pagina"  => 50,
                    "apenas_importado_api"  => "N",
                ]
            ];
            
            $response = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
            //print_r($response); die;
            
            $clientes = ArrayHelper::getValue($response,'body.clientes_cadastro');
            
            foreach ($clientes as $k => $cliente){
                if ($cliente["pessoa_fisica"]=="S" && $cliente["inscricao_estadual"] != ""){
                    echo "\n".$k." - ".$cliente["codigo_cliente_omie"]." - ".$cliente["pessoa_fisica"]." - ".$cliente["inscricao_estadual"]." - ".$cliente["cnpj_cpf"]." - ".$cliente["nome_fantasia"];//." - ".$cliente["razao_social"];
                    //die;
                    $body = [
                        "call" => "AlterarCliente",
                        "app_key" => static::APP_KEY_OMIE,
                        "app_secret" => static::APP_SECRET_OMIE,
                        "param" => [
                            "codigo_cliente_omie"   => $cliente["codigo_cliente_omie"],
                            "inscricao_estadual"    => ""
                        ]
                    ];
                    
                    $response = $omie->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
                    print_r($response);
                    //die;
                }
                //if (ArrayHelper::getValue($response,'codigo_cliente_integracao')!=null){
                //    echo ArrayHelper::getValue($response,'codigo_cliente_integracao');
                //}
            }
        }
        
        /*$body = [
            "call" => "ConsultarCliente",
            "app_key" => static::APP_KEY_OMIE,
            "app_secret" => static::APP_SECRET_OMIE,
            "param" => [
                //"codigo_cliente" => "1921689799",
                "codigo_cliente_integracao" => "399244847",
                //"codigo" => $produtoFilial->produto->codigo_global
            ]
        ];
        
        $response = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
        print_r(ArrayHelper::getValue($response,'httpCode'));*/
            
        /*$body = [
            "call" => "ConsultarCliente",
            "app_key" => static::APP_KEY_OMIE,
            "app_secret" => static::APP_SECRET_OMIE,
            "param" => [
                "codigo_cliente_integracao" => "399244847",
            ]
        ];
        $response_omie = $meli->consulta_cliente("api/v1/geral/clientes/?JSON=",$body);
        print_r(ArrayHelper::getValue($response_omie, 'body.codigo_cliente_omie'));*/
        
        echo "\n\nFim da consulta do cliente omie...";

    }
}

