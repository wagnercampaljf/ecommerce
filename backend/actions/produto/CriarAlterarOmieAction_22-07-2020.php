<?php

namespace backend\actions\produto;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;

class CriarAlterarOmieAction extends Action
{

    public function run($id)
    {
        $model = $this->controller->findModel($id);
        echo "Criando produtos...\n\n";

        $criar_omie = new Omie(1, 1);

        $produto = Produto::find()->andWhere(['=','id',$model->id])->one();

	if (substr($produto->codigo_global,0,3) != 'CX.'){

	        $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
	        //$valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
		$valor_produto  = ($minValue==NULL) ? "1" : $minValue->getValorFinal();
		//var_dump($valor_produto); die;
		//$descricao = substr($produto->nome." (".$produto->codigo_global.")",0,100);
		$descricao = substr($produto->codigo_global." ".$produto->nome,0,120);


	        //echo "Inserindo produtos...SP\n\n";
	        $body = [
	            "call" => "IncluirProduto",
	            "app_key" => '468080198586',
	            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
	            "param" => [
	                "codigo_produto_integracao" => "PA".$produto->id,//$produto->codigo_global,
			//"codigo"                    => $produto->codigo_global,
                        "codigo"                    => "PA".$produto->id,
                        //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
                        "descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
        	        "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
	                "unidade"                   => "PC",
	                "valor_unitario"            => round($valor_produto,2),
	                "tipoItem"                  => "99",
	                "peso_liq"                  => round($produto->peso,2),
	                "peso_bruto"                => round($produto->peso,2),
	                "altura"                    => round($produto->altura,2),
	                "largura"                   => round($produto->largura,2),
	                "profundidade"              => round($produto->profundidade,2),
	                "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
	                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]             
	            ]
	        ];
	        $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
		print_r($response); echo "<br><br><br>"; //die;

	        //echo "Alterando produtos...SP\n\n";
	        $body = [
	            "call" => "AlterarProduto",
	            "app_key" => '468080198586',
	            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
	            "param" => [
	                "codigo_produto_integracao" => "PA".$produto->id,//$produto->codigo_global,
			//"codigo"                    => $produto->codigo_global,
                        "codigo"                    => "PA".$produto->id,
                        //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
                        "descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
	                "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
	                "unidade"                   => "PC",
	                "valor_unitario"            => round($valor_produto,2),
	                "tipoItem"                  => "99",
	                "peso_liq"                  => round($produto->peso,2),
	                "peso_bruto"                => round($produto->peso,2),
	                "altura"                    => round($produto->altura,2),
	                "largura"                   => round($produto->largura,2),
	                "profundidade"              => round($produto->profundidade,2),
	                "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
	                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
	            ]
	        ];
	        $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
		print_r($response); echo "<br><br><br>"; //die;

		//echo "Inserindo produtos...MG\n\n";
	        $body = [
	            "call" => "IncluirProduto",
	            "app_key" => '469728530271',
	            "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
	            "param" => [
	                "codigo_produto_integracao" => "PA".$produto->id,
	                //"codigo"                    => $produto->codigo_global,
			"codigo"                    => "PA".$produto->id,
	                //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
			"descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
	                "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
	                "unidade"                   => "PC",
	                "valor_unitario"            => round($valor_produto,2),
	                "tipoItem"                  => "99",
	                "peso_liq"                  => round($produto->peso,2),
	                "peso_bruto"                => round($produto->peso,2),
	                "altura"                    => round($produto->altura,2),
	                "largura"                   => round($produto->largura,2),
	                "profundidade"              => round($produto->profundidade,2),
	                "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
	                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]             
	            ]
	        ];
	        $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
		print_r($response); echo "<br><br><br>"; //die;

		 //echo "Alterando produtos...MG\n\n";
	        $body = [
	            "call" => "AlterarProduto",
	            "app_key" => '469728530271',
	            "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
	            "param" => [
	                "codigo_produto_integracao" => "PA".$produto->id,
	                "codigo"                    => "PA".$produto->id,
	                //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
			"descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
	                "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
	                "unidade"                   => "PC",
	                "valor_unitario"            => round($valor_produto,2),
	                "tipoItem"                  => "99",
	                "peso_liq"                  => round($produto->peso,2),
        	        "peso_bruto"                => round($produto->peso,2),
	                "altura"                    => round($produto->altura,2),
	                "largura"                   => round($produto->largura,2),
	                "profundidade"              => round($produto->profundidade,2),
	                "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
	                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
	            ]
	        ];
	        $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
		print_r($response); echo "<br><br><br>"; die;

        }
       /*//echo "Consultando produtos...\n\n";
       $body = [
            "call" => "ConsultarProduto",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "codigo_produto" => "",
                //"codigo_produto" => 344218142,
                "codigo_produto_integracao" => "",
                "codigo" => $produto->codigo_global
            ]
        ];

        $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);*/

        //Retorno         
	$url="update?id=".$model->id;

	return Yii::$app->getResponse()->redirect($url)->send();
    }
}

