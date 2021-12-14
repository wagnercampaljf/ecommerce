<?php

namespace console\controllers\actions\omie;

//use common\models\Produto;
//use common\models\ValorProdutoFilial;
//use console\controllers\actions\omie\Omie;
//use Yii;
//use yii\base\Action;
use common\models\Produto;
use common\models\ValorProdutoFilial;
use yii\helpers\ArrayHelper;


class VerificacaoCorrecaoTodasContasAction extends Action
{
    public function run()
    {

        $teste = (int) 1234567890123;
        
        echo "Verificar e corrigir TODAS as contas no Omie...\n\n";
        $omie = new Omie(static::APP_ID, static::SECRET_KEY);
        
        
        $APP_KEY_OMIE_SP              = '468080198586';
        $APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        

        echo "\n entrou \n";
        
        //PRODUTOS CONTA PRINCIPAL
        $produtos_conta_principal = Array();
        //$file = fopen("/var/tmp/omie_produtos/produtos_omie_principal_14-09-2020.csv", 'r');
        $file = fopen("/var/tmp/produtos_omie_principal_30-09-2020.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_conta_principal[$line[1]] = $line;
        }
        fclose($file);
        
        //PRODUTOS CONTA DUPLICADA
        $produtos_conta_duplicada = Array();
        //$file = fopen("/var/tmp/omie_produtos/produtos_omie_duplicada_14-09-2020.csv", 'r');
        $file = fopen("/var/tmp/produtos_omie_duplicada_30-09-2020.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_conta_duplicada[$line[1]] = $line;
        }
        fclose($file);
        
        //PRODUTOS CONTA MINAS GERAIS
        $produtos_conta_minas_gerais = Array();
        //$file = fopen("/var/tmp/omie_produtos/produtos_omie_minas_gerais_14-09-2020.csv", 'r');
        $file = fopen("/var/tmp/produtos_omie_minas_gerais_30-09-2020.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $produtos_conta_minas_gerais[$line[1]] = $line;
        }
        fclose($file);
        
        echo "\n\n arrays preenchidos";
        
        //LOG PRINCIPAL
        $arquivo_log_principal = fopen("/var/tmp/log_produtos_omie_principal_30-09-2020_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log_principal, "codigo;status");
        
        //LOG DUPLICADA
        $arquivo_log_duplicada = fopen("/var/tmp/log_produtos_omie_duplicada_30-09-2020_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log_duplicada, "codigo;status");
        
        //LOG MINAS GERAIS
        $arquivo_log_minas_gerais = fopen("/var/tmp/log_produtos_omie_minas_gerais_30-09-2020_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log_minas_gerais, "codigo;status");
        
        //LOG PEÇAAGORA
        $arquivo_log_pecaagora = fopen("/var/tmp/log_produtos_pecaagora_30-09-2020_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log_pecaagora, "codigo;status");
        
        $x = 0;
        
        foreach ($produtos_conta_principal as $k => $produto_conta_principal){
            break;            
            echo "\n".$x++." - ".$produto_conta_principal[1];
            fwrite($arquivo_log_principal, "\n".$produto_conta_principal[1].";");

            $codigo = (int) str_replace("PA","",substr($produto_conta_principal[1], 0, 9));
            echo " - ".$codigo;
            
            if($x <= 1){
                continue;
            }
            
            $produto = Produto::find()->andWhere(['=','id', $codigo])->one();
            
            if(!$produto){

                echo " - produto não encontrado no peça";
                fwrite($arquivo_log_principal, "Produto não encontrado no peça");
                //continue;
                
                $body = [
                    "call" => "ExcluirProduto",
                    "app_key" => $APP_KEY_OMIE_SP,
                    "app_secret" => $APP_SECRET_OMIE_SP,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_principal[1],
                        //"inativo" => "S",
                    ]
                ];
                
                $response_produto_principal = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_principal);
                if (ArrayHelper::getValue($response_produto_principal, 'httpCode') == 200){
                    echo " - OK(Excluir)";
                    fwrite($arquivo_log_principal, " - OK(Excluir)");
                }else{
                    echo " - Erro(Excluir)";
                    fwrite($arquivo_log_principal, " - Erro(Excluir)");
                }
                
                
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => $APP_KEY_OMIE_SP,
                    "app_secret" => $APP_SECRET_OMIE_SP,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_principal[1],
                        "inativo" => "S",
                    ]
                ];
                
                $response_produto_principal = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_principal);
                if (ArrayHelper::getValue($response_produto_principal, 'httpCode') == 200){
                    echo " - OK(Inativar)";
                    fwrite($arquivo_log_principal, " - OK(Inativar)");
                }else{
                    echo " - Erro(Inativar)";
                    fwrite($arquivo_log_principal, " - Erro(Inativar)");
                }
                                
            }
            else{
                echo " - produto encontrado no peça";
                fwrite($arquivo_log_principal, "Produto encontrado no peça");
            }
        }
        
        $x = 0;
        
        foreach ($produtos_conta_duplicada as $k => $produto_conta_duplicada){
            break;
            echo "\n".$x++." - ".$produto_conta_duplicada[1];
            fwrite($arquivo_log_duplicada, "\n".$produto_conta_duplicada[1].";");
            
            $codigo = (int) str_replace("PA","",substr($produto_conta_duplicada[1], 0, 9));
            echo " - ".$codigo;
            
            if($x <= 1){
                continue;
            }
            
            $produto = Produto::find()->andWhere(['=','id', $codigo])->one();
            
            if(!$produto){
                
                echo " - produto não encontrado no peça";
                fwrite($arquivo_log_duplicada, "Produto não encontrado no peça");
                //continue;
                
                $body = [
                    "call" => "ExcluirProduto",
                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_duplicada[1],
                        //"inativo" => "S",
                    ]
                ];
                
                $response_produto_duplicada = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_duplicada);
                if (ArrayHelper::getValue($response_produto_duplicada, 'httpCode') == 200){
                    echo " - OK(Excluir)";
                    fwrite($arquivo_log_duplicada, " - OK(Excluir)");
                }else{
                    echo " - Erro(Excluir)";
                    fwrite($arquivo_log_duplicada, " - Erro(Excluir)");
                }
                
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_duplicada[1],
                        "inativo" => "S",
                    ]
                ];
                
                $response_produto_duplicada = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_duplicada);
                if (ArrayHelper::getValue($response_produto_duplicada, 'httpCode') == 200){
                    echo " - OK(Inativo)";
                    fwrite($arquivo_log_duplicada, " - OK(Inativo)");
                }else{
                    echo " - Erro(Inativo)";
                    fwrite($arquivo_log_duplicada, " - Erro(Inativo)");
                }
                
            }
            else{
                echo " - produto encontrado no peça";
                fwrite($arquivo_log_duplicada, "Produto encontrado no peça");
            }
        }
        
        $x = 0;
        
        foreach ($produtos_conta_minas_gerais as $k => $produto_conta_minas_gerais){
            break;
            echo "\n".$x++." - ".$produto_conta_minas_gerais[1];
            fwrite($arquivo_log_minas_gerais, "\n".$produto_conta_minas_gerais[1].";");
            
            $codigo = (int) str_replace("PA","",substr($produto_conta_minas_gerais[1], 0, 9));
            echo " - ".$codigo;
            
            if($x <= 1){
                continue;
            }
            
            $produto = Produto::find()->andWhere(['=','id', $codigo])->one();
            
            if(!$produto){
                
                echo " - produto não encontrado no peça";
                fwrite($arquivo_log_minas_gerais, "Produto não encontrado no peça");
                //continue;
                
                $body = [
                    "call" => "ExcluirProduto",
                    "app_key" => $APP_KEY_OMIE_MG,
                    "app_secret" => $APP_SECRET_OMIE_MG,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_minas_gerais[1],
                        //"inativo" => "S",
                    ]
                ];
                
                $response_produto_minas_gerais = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_duplicada);
                if (ArrayHelper::getValue($response_produto_minas_gerais, 'httpCode') == 200){
                    echo " - OK(Excluir)";
                    fwrite($arquivo_log_minas_gerais, " - OK(Excluir)");
                }else{
                    echo " - Erro(Excluir)";
                    fwrite($arquivo_log_minas_gerais, " - Erro(Excluir)");
                }
                
                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => $APP_KEY_OMIE_MG,
                    "app_secret" => $APP_SECRET_OMIE_MG,
                    "param" => [
                        //"codigo" => $produto_conta_principal[1],
                        "codigo_produto_integracao" => $produto_conta_minas_gerais[1],
                        "inativo" => "S",
                    ]
                ];
                
                $response_produto_minas_gerais = $omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response_produto_duplicada);
                if (ArrayHelper::getValue($response_produto_minas_gerais, 'httpCode') == 200){
                    echo " - OK(Inativo)";
                    fwrite($arquivo_log_minas_gerais, " - OK(Inativo)");
                }else{
                    echo " - Erro(Inativo)";
                    fwrite($arquivo_log_minas_gerais, " - Erro(Inativo)");
                }
                
            }
            else{
                echo " - produto encontrado no peça";
                fwrite($arquivo_log_minas_gerais, "Produto encontrado no peça");
            }
        }
        
        
        
        $produtos_pecaagora = Produto::find()->all(); 
        
        foreach($produtos_pecaagora as $k => $produto_pecaagora){
            
            echo "\n".$k." - ".$produto_pecaagora->id;
            fwrite($arquivo_log_pecaagora, "\n".$produto_pecaagora->id.";");
            
            if(!array_key_exists("PA".$produto_pecaagora->id, $produtos_conta_principal)){
                
                echo " - produto não encontrado em SP";
                fwrite($arquivo_log_pecaagora, "Produto não encontrado em SP");
                //continue;
                
                $descricao = str_replace('"',"''",substr("".$produto_pecaagora->codigo_global." ".$produto_pecaagora->nome,0,100));
                
                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto_pecaagora->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
                
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => $APP_KEY_OMIE_SP,
                    "app_secret" => $APP_SECRET_OMIE_SP,
                    "param" => [
                        "codigo_produto_integracao" => "PA".$produto_pecaagora->id,
                        "codigo"                    => "PA".$produto_pecaagora->id,
                        "descricao"                 => str_replace(" ","%20",$descricao),
                        "ncm"                       => ($produto_pecaagora->codigo_montadora=="" ? "0000.00.00" : substr($produto_pecaagora->codigo_montadora,0,4).".".substr($produto_pecaagora->codigo_montadora,4,2).".".substr($produto_pecaagora->codigo_montadora,6,2)),
                        "valor_unitario"            => round($valor_produto,2),
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => round($produto_pecaagora->peso,2),
                        "peso_bruto"                => round($produto_pecaagora->peso,2),
                        "altura"                    => round($produto_pecaagora->altura,2),
                        "largura"                   => round($produto_pecaagora->largura,2),
                        "profundidade"              => round($produto_pecaagora->profundidade,2),
                        "marca"                     => ($produto_pecaagora->fabricante_id==null) ? "Peça Agora" : $produto_pecaagora->fabricante->nome,
                        "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                    ]
                ];
                $response = $omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response);
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - OK(Criar)";
                    fwrite($arquivo_log_pecaagora, " - OK(Criar)");
                }else{
                    echo " - Erro(Criar)";
                    fwrite($arquivo_log_pecaagora, " - Erro(Criar)");
                    print_r($response);
                }
            } 
            else{
                echo " - produto encontrado em SP";
                fwrite($arquivo_log_pecaagora, "Produto encontrado em SP");
            }
            
            
            if(!array_key_exists("PA".$produto_pecaagora->id, $produtos_conta_duplicada)){
                
                echo " - produto não encontrado DUPLICADA";
                fwrite($arquivo_log_pecaagora, " - Produto não encontrado em DUPLICADA");
                //continue;
                
                $descricao = str_replace('"',"''",substr("".$produto_pecaagora->codigo_global." ".$produto_pecaagora->nome,0,100));
                
                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto_pecaagora->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
                
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => $APP_KEY_OMIE_CONTA_DUPLICADA,
                    "app_secret" => $APP_SECRET_OMIE_CONTA_DUPLICADA,
                    "param" => [
                        "codigo_produto_integracao" => "PA".$produto_pecaagora->id,
                        "codigo"                    => "PA".$produto_pecaagora->id,
                        "descricao"                 => str_replace(" ","%20",$descricao),
                        "ncm"                       => ($produto_pecaagora->codigo_montadora=="" ? "0000.00.00" : substr($produto_pecaagora->codigo_montadora,0,4).".".substr($produto_pecaagora->codigo_montadora,4,2).".".substr($produto_pecaagora->codigo_montadora,6,2)),
                        "valor_unitario"            => round($valor_produto,2),
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => round($produto_pecaagora->peso,2),
                        "peso_bruto"                => round($produto_pecaagora->peso,2),
                        "altura"                    => round($produto_pecaagora->altura,2),
                        "largura"                   => round($produto_pecaagora->largura,2),
                        "profundidade"              => round($produto_pecaagora->profundidade,2),
                        "marca"                     => ($produto_pecaagora->fabricante_id==null) ? "Peça Agora" : $produto_pecaagora->fabricante->nome,
                        "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                    ]
                ];
                $response = $omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response);
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - OK(Criar)";
                    fwrite($arquivo_log_pecaagora, " - OK(Criar)");
                }else{
                    echo " - Erro(Criar)";
                    fwrite($arquivo_log_pecaagora, " - Erro(Criar)");
                    print_r($response);
                }
            }
            else{
                echo " - produto encontrado em DUPLICADA";
                fwrite($arquivo_log_pecaagora, " - Produto encontrado em DUPLICADA");
            }
            
            
            if(!array_key_exists("PA".$produto_pecaagora->id, $produtos_conta_minas_gerais)){
                
                echo " - produto não encontrado MG";
                fwrite($arquivo_log_pecaagora, " - Produto não encontrado em MG");
                //continue;
                
                $descricao = str_replace('"',"''",substr("".$produto_pecaagora->codigo_global." ".$produto_pecaagora->nome,0,100));
                
                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto_pecaagora->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
                
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => $APP_KEY_OMIE_MG,
                    "app_secret" => $APP_SECRET_OMIE_MG,
                    "param" => [
                        "codigo_produto_integracao" => "PA".$produto_pecaagora->id,
                        "codigo"                    => "PA".$produto_pecaagora->id,
                        "descricao"                 => str_replace(" ","%20",$descricao),
                        "ncm"                       => ($produto_pecaagora->codigo_montadora=="" ? "0000.00.00" : substr($produto_pecaagora->codigo_montadora,0,4).".".substr($produto_pecaagora->codigo_montadora,4,2).".".substr($produto_pecaagora->codigo_montadora,6,2)),
                        "valor_unitario"            => round($valor_produto,2),
                        "unidade"                   => "PC",
                        "tipoItem"                  => "99",
                        "peso_liq"                  => round($produto_pecaagora->peso,2),
                        "peso_bruto"                => round($produto_pecaagora->peso,2),
                        "altura"                    => round($produto_pecaagora->altura,2),
                        "largura"                   => round($produto_pecaagora->largura,2),
                        "profundidade"              => round($produto_pecaagora->profundidade,2),
                        "marca"                     => ($produto_pecaagora->fabricante_id==null) ? "Peça Agora" : $produto_pecaagora->fabricante->nome,
                        "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                    ]
                ];
                $response = $omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response);
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - OK(Criar)";
                    fwrite($arquivo_log_pecaagora, " - OK(Criar)");
                }else{
                    echo " - Erro(Criar)";
                    fwrite($arquivo_log_pecaagora, " - Erro(Criar)");
                    print_r($response);
                }
            }
            else{
                echo " - produto encontrado em MG";
                fwrite($arquivo_log_pecaagora, " - Produto encontrado em MG");
            }
        }
    }
}



