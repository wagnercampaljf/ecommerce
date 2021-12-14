<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateTituloContaDuplicadaAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";
        
        $nome_arquivo = "/var/tmp/log_update_titulo_con_duplicada_ml_".date("Y-m-d_H-i-s").".csv";
        
        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial_duplicada   = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro         = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
        $response_outro     = ArrayHelper::getValue($user_outro, 'body');
	print_r($response_outro);
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->andWhere(['<>','id',98])
                            	    ->andWhere(['<>','id',43])
                            	    ->orderBy('id')
                                    ->all();
        
        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
            $meliAccessToken_outro = $response_outro->access_token;

            foreach ($filials as $filial) {
                echo "\n\nFilial: ".$filial->id." - ".$filial->nome."\n\n";
		/*if($filial->id < 72){ 
                	continue;
            	}*/

                $produtoFilials = $filial->getProdutoFilials()  ->andWhere(['IS NOT', 'meli_id', NULL])
								//->joinWith('produto')
		                                                //->andWhere(['like','upper(produto.nome)','KIT EMBREAGEM'])
                                                                //->andWhere(['id' => [53136,53407,53408,53426,56736,57489,57655,57656,57796,57800,57801,57802,57924,57927,57928,57929,57930,57932,72285,113079,113344,113375,113446,113530,132348,133341,134002,134187,136804,137201,137508,137640,138197,139572,139612,139622,139629,140069,140597,184504,192043,215050,241760,242082,242538,243346,243494,245931,245934,246282,246352,246875,246933,247019,247282,247927,248420,249408,250612,253199,253309,253386,314268,314301,315691,317207,317209,317212,317245,317271,321013,327634,327997,340597,342680,390921]])
								//->andWhere(['produto_id' => [236162]])
								->andWhere(['=', 'id', 137913])
                                                        	->orderBy('id')
                                                                ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
       		    echo "\n".$k." - Origem: ".$produtoFilial->id." - ".$produtoFilial->meli_id; 
		    //continue;
        
        	    /*if($k <= 6150 && $produtoFilial->filial_id == 72){
            		echo " - pulou";
            		continue;
        	    }*/

                    $produto_filial_outro = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                    ->andWhere(['=', 'filial_id', 98])
                                                                    ->andWhere(['=', 'e_nome_alterado', true])
                                                                    ->one();

       		    if($produto_filial_outro){
//echo "\n".$o++; continue;
                        echo " - Destino: ".$produto_filial_outro->id." - ".$produto_filial_outro->meli_id;
                        
                        if($produto_filial_outro->meli_id == "" || $produto_filial_outro->meli_id == null){
			    echo " - Produto duplicado ainda não criado";
                            continue;
                        }
                        
                        //$title = Yii::t('app', '{nome} cod {cod}', ['cod' => $produtoFilial->produto->codigo_global, 'nome' => $produtoFilial->produto->nome ]);
			$title = Yii::t('app', '{nome}', ['nome' => $produtoFilial->produto->nome ]);
                        
                        $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
			$page = str_replace("'", "", $page);
		    	$page = str_replace("<p>", "", $page);
		    	$page = str_replace("</p>", "", $page);
		    	$page = str_replace("<br>", "\n", $page);
		    	$page = str_replace("<BR>", "\n", $page);
		    	$page = str_replace("<br/>", "\n", $page);
		    	$page = str_replace("<BR/>", "\n", $page);
		    	$page = str_replace("<strong>", "", $page);
			$page = str_replace("</strong>", "", $page);
                        
                        $status_descricao	= false;
                        $status_titulo 		= false;
                        $status_condicao    	= false;

			//Atualizar Categoria
			/*$subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                        //if (!isset($subcategoriaMeli)) {
                        //        continue;
                        //} else {
                        // 	if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
                        //                $subcategoriaMeli = "MLB191833";
                        //        }
                        //}
			$body = ["category_id" => utf8_encode($subcategoriaMeli),];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                        if ($response['httpCode'] >= 300) {
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }
			else{
                                echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');
			}*/

                        //Atualização Descrição
                        $body = ["plain_text" => $page];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}/description?access_token=" . $meliAccessToken_outro, $body, [] );
			//print_r($response);
                        if ($response['httpCode'] >= 300) {
			    print_r($body);
			    print_r($response);
                            echo " - ERROR Descrição";
                            fwrite($arquivo_log, "\n".$produto_filial_outro->id.";Descrição não alterado");
                        }
                        else{
                            $status_descricao = true;
                            echo " - OK Descrição";
			    //echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, "\n".$produto_filial_outro->id.";Descrição alterada");
                        }
                        
                        //Atualização Título
                        /*$body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Título";
                            fwrite($arquivo_log, ";Titulo não alterado");
                        }
                        else {
                            $status_titulo = true;
                            echo " - OK Título";
			    echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Titulo alterado");
                        }*/
                        
                        //Atualização Condição
                        $condicao = ($produto_filial_outro->produto->e_usado)? "used" : "new";
                        $body = ["condition" => $condicao];
                        $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Condição";
			    //print_r($response);
                            fwrite($arquivo_log, ";Condição não alterada");
                        }
                        else {
			    //echo " - ".ArrayHelper::getValue($response, 'body.permalink');
                            $status_condicao = true;
                            echo " - OK Condição";
			    //echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Condição alterada");
                        }
                        
                        //Aqui termina o código
                        
                        /*if($status_descricao && $status_titulo && $status_condicao){
                            $produto_filial_outro->e_nome_alterado = false;
                            if($produto_filial_outro->save()){
                                echo " - OK Status";
                                fwrite($arquivo_log, ";Status alterado");
                            }
                            else{
                                echo " - ERROR Status";
                                fwrite($arquivo_log, ";Status não alterado");
                            }
                        }*/

        		    }
        		    else{
        		        echo " - Produto não encontrado";
        		    }
	            }
            }
        }
        
        fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
        fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
        fclose($arquivo_log);
    }
}


