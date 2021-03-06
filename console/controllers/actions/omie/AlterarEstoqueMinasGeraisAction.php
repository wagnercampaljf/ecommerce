<?php

namespace console\controllers\actions\omie;

//use common\models\Produto;
//use common\models\ValorProdutoFilial;
//use console\controllers\actions\omie\Omie;
//use Yii;
//use yii\base\Action;
use common\models\Produto;
use yii\helpers\ArrayHelper;


class AlterarEstoqueMinasGeraisAction extends Action
{
    public function run()
    {
       
        echo "Verificar e corrigir TODAS as contas no Omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        //TESTE
        
        /*$body = [
            "call" => "ListarLocaisEstoque",
            "app_key" => $APP_KEY_OMIE_MG,
            "app_secret" => $APP_SECRET_OMIE_MG,
            "param" => [
                "nPagina" => 1,
                "nRegPorPagina" => 20,
            ]
        ];
        $response_criar_minas_gerais = $omie->consulta("/api/v1/estoque/local/?JSON=",$body);
        print_r($response_criar_minas_gerais); die;*/
        
        //TESTE

        echo "\nInicio \n";
        
        //PRODUTOS CONTA MINAS GERAIS
        $produtos_conta_minas_gerais = Array();
        //$file = fopen("/var/tmp/produtos_omie_minas_gerais_18-09-2020_zerar.csv", 'r');
        //$file = fopen("/var/tmp/omie_produtos_18-09-2020_zerar.csv", 'r');
        //$file = fopen("/var/tmp/produtos_omie_minas_gerais_21-09-2020_zerar.csv", 'r');
        $file = fopen("/var/tmp/omie_pellegrino_estoque_mg_21-09-2020.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_conta_minas_gerais[] = $line;
        }
        fclose($file);
        
        echo "\n\n arrays preenchidos";
        
        $x = 0;
        
        //print_r($produtos_conta_minas_gerais);die;
        
        foreach ($produtos_conta_minas_gerais as $k => $produto_minas_gerais){
            
            echo "\n".$x++." - ".$produto_minas_gerais[0];
            
            $produto = Produto::find()->andWhere(['=', 'codigo_global', $produto_minas_gerais[0]])->one();
            
            if($produto){
            
                echo " - Produto encontrado pelo GLOBAL (PA".$produto->id.")";
            
                $body = [
                    "call" => "ConsultarProduto",
                    "app_key" => $APP_KEY_OMIE_MG,
                    "app_secret" => $APP_SECRET_OMIE_MG,
                    "param" => [
                        //"codigo_produto" => $produto_minas_gerais[2],
                        //"codigo" => $produto_minas_gerais[0],
                        "codigo" => "PA".$produto->id,
                    ]
                ];
                
                $response_produto_minas_gerais = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_minas_gerais);
                
                if($response_produto_minas_gerais["httpCode"] < 300){
                    echo " - Produto encontrado Minas Gerais";
                    //continue;
                    
                    $body = [
                        "call" => "IncluirAjusteEstoque",
                        "app_key" => $APP_KEY_OMIE_MG,
                        "app_secret" => $APP_SECRET_OMIE_MG,
                        "param" => [
                            "codigo_local_estoque" => 0,
                            "id_prod" => $response_produto_minas_gerais["body"]["codigo_produto"],
                            "data" => date("d/m/Y"),
                            "quan" => $produto_minas_gerais[1],//"0",
                            "obs" => "Ajuste de estoque feito pela API (".date("Y-m-d").")",
                            "origem" => "AJU",
                            "tipo" => "SLD",
                            "motivo" => "INV",
                        ]
                    ];
                    //print_r($body); die;
                    $response_criar_minas_gerais = $omie->consulta("/api/v1/estoque/ajuste/?JSON=",$body);
                    
                    if($response_criar_minas_gerais["httpCode"] == 200){
                        echo " - Ajuste feito Conta Minas Gerais";
                    }
                    else{
                        echo " - Ajuste n??o feito Conta Minas Gerais";
                        print_r($response_criar_minas_gerais);
                    }
                }
                else{
                    
                    echo " - Produto n??o encontrado Minas Gerais";
                    
                    //////////////////////////////////////////////////////////////////////////////////////////////
                    
                    $body = [
                        "call" => "ConsultarProduto",
                        "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                        "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                        "param" => [
                            "codigo_integracao" => "PA".$produto->id,
                            //"pagina" => 1,
                            //"registros_por_pagina" => 100,
                            //"apenas_importado_api" => "N",
                            //"filtrar_por_status" => "ATRASADO"
                        ]
                    ];
                    
                    $response_produto_conta_duplicada = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
                    //print_r($response_produto_conta_duplicada); die;
                    
                    if($response_produto_conta_duplicada["httpCode"] == 200){
                        echo " - Produto encontrado Conta Duplicada";
                        
                        $body = [
                            "call" => "IncluirProduto",
                            "app_key" => $APP_KEY_OMIE_MG,
                            "app_secret" => $APP_SECRET_OMIE_MG,
                            "param" => [
                                "codigo_produto_integracao" => $response_produto_conta_duplicada["body"]["codigo_produto_integracao"],
                                "codigo"                    => $response_produto_conta_duplicada["body"]["codigo"],
                                "descricao"                 => str_replace(" ","%20", $response_produto_conta_duplicada["body"]["descricao"]),
                                "ncm"                       => $response_produto_conta_duplicada["body"]["ncm"],
                                "valor_unitario"            => $response_produto_conta_duplicada["body"]["valor_unitario"],
                                "unidade"                   => $response_produto_conta_duplicada["body"]["unidade"],
                                "tipoItem"                  => $response_produto_conta_duplicada["body"]["tipoItem"],
                                "peso_liq"                  => $response_produto_conta_duplicada["body"]["peso_liq"],
                                "peso_bruto"                => $response_produto_conta_duplicada["body"]["peso_bruto"],
                                "altura"                    => $response_produto_conta_duplicada["body"]["altura"],
                                "largura"                   => $response_produto_conta_duplicada["body"]["largura"],
                                "profundidade"              => $response_produto_conta_duplicada["body"]["profundidade"],
                                "marca"                     => $response_produto_conta_duplicada["body"]["marca"],
                                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                            ]
                        ];
                        //print_r($body);
                        /*$response_criar_minas_gerais = $omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                        
                        if($response_criar_minas_gerais["httpCode"] == 200){
                            echo " - Produto cadastrado Conta Minas Gerais";
                        }
                        else{
                            echo " - Produto n??o cadastrado Conta Minas Gerais";
                            print_r($response_criar_minas_gerais);
                        }*/

                    }
                    else{
                        echo " - Produto n??o encontrado Conta Duplicada";
                    }
                    
                    //////////////////////////////////////////////////////////////////////////////////////////////
                }
            }
            else{
                echo " - Produto n??o encontrado pelo GLOBAL";
            }
        }
    }
}



