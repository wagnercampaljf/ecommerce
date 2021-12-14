<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateTituloAction extends Action
{
    public function run()
    {

        $nome_arquivo = "/var/tmp/log_update_titulo_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [84]])
    	    ->andWhere(['<>','id', 43])
            ->andWhere(['<>','id', 98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->id ." - ". $filial->nome . "\n";

	    /*if($filial->id <= 38){
		continue;
	    }*/

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial   ->getProdutoFilials()
					    //->joinWith('produto')
			                    //->andWhere(['like','upper(produto.nome)','KIT EMBREAGEM'])
                                            //->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            ->andWhere(['produto_filial.produto_id' => [308141,308673,309071,310210,310347,310387,300602,306669,22374,22375,22377,22380,22381,22384,22387,22388,22391,27349,16685,16686,16687,18380,18426,18821,300886,22373,22376,22378,22379,22382,22390,26793,251831,26806,26807,24846,24849,16683,16684,266442,22385,16691,27341,27339,16692,27338,310402,270834,262030,263468,262354,262972,261493,263467,262355,262188,263111,250920,265396,261225,262016,262498,250921,257683,18445,18446,18447,18448,18449,18450,24382,24383,24381,16688,16689,248405,270835,270836,255967,248904,249070,11375,26794,22386,250131,249393,249535,18822,249769,249193,249601,250390,22006,26649,22018,26795,22389,252520,252569,252570,26808,26800,26790,26791,26792,27186,27346,22383,24379,24380,24384,24806,24807,24844,16690,18451,22026,22844,26805,267189,252404,27347,252571,252572,252521,253711,253783,253872,253914,256280,256368,256400,256532,263797,290664,290970,232134,291694,257580,292419,292574,259530,260647,263773,263774,263787,263790,264306,264978,264979,264977,264980,265198,265220,266093,268700,268937,270625,251828,294610,255826,252003,295297,275865,276951,277307,277598,296138,278191,278618,278814,279376,279385,279514,279543,279536,279545,279552,279572,279576,279608,279676,279683,279695,279701,279751,279743,279783,279796,279827,279805,279825,279852,279848,279849,279855,279984,248975,250516,248796,249739,250130,250298,251771,253925,253929,251830,251829,269934,261877,309176,279787,278326,298841,299002,299037,295431,294043,295385,304865,305107,316126,314983,315809,279696,279704,278164,279976,278184,279310,279747,278018,279140,279513,279553,279587,279620,279650,279682,279873,277627,278747,315338,315842,315872,316112,316189,316493,316494,316511,316549,316553,316561,316562,316636,316822,318096,318578,318948,320085,320454,320721,321343,322847,323237,323473,324078,323706,264979,264977,264980,265198,270625,279385,279536,279743,279796,279805,279849,279855,248975,248796,250130,8472,251771,262354,261493,262355,262188,250920,261225,262016,262498,253929,251830,251829,269934,261877,279696,279704,278164,278184,279310,279873,279747,278018,279513,279553,279587,279620,279650,279682,263774,263790,264306,264978,252570,253711,253872,256400,260647,263773,277627,278747,251831,250921,270836,249601,250390,310525,276223,266442,270834,262030,263468,262972,263467,263111,265396,257683,248405,270835,255967,248904,249070,250131,249393,249535,249769,249193,252520,252569,267189,252404,252571,252572,303119,252521,253783,253914,256280,256368,256532,263797,232134,257580,259530,263787,265220,259530,263787,265220,266093,268700,268937,251828,252003,275865,276951,277307,277598,278191,296362,278618,278814,279376,279514,279543,279545,279552,279572,279576,279608,279676,279683,279695,279701,279751,279783,279827,279825,279852,279848,279984,250516,249739,250298,253925,279787,278326,294513,297227,297510,305887,319784,279976,291880,237825,279140]])
                                            //->andWhere(['produto_filial.id' => [34637]])
					    //->andWhere(['produto_filial.produto_id' => [236162]])
					    //->andWhere(['=','e_nome_alterado',true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - filial_id: ".$filial->id." - produto_id: ".$produtoFilial->produto_id." - produto_filial_id: ". $produtoFilial->id ." - ".$produtoFilial->meli_id;//." - ".$produtoFilial->produto->nome;
		    fwrite($arquivo_log, "\n".$produtoFilial->id);

		    /*if($k <= 18809 && $filial->id == 72){
			echo " - pular";
			continue;
		    }*/

       		    if ($produtoFilial->produto->fabricante_id != null) {

                        //Aqui começa o código
                        if (is_null($produtoFilial->valorMaisRecente)) {
                            continue;
                        }

                        //$title = Yii::t('app', '{nome} cod {cod}', ['cod' => $produtoFilial->produto->codigo_global, 'nome' => $produtoFilial->produto->nome]);
			$title = Yii::t('app', '{nome}', ['nome' => $produtoFilial->produto->nome]);
			$nome = $produtoFilial->produto->nome;
			if(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                            $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@11@', $nome);
                        }
                        elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                            $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@10@', $nome);
                        }
                        elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                            $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@9@', $nome);
                        }
                        elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                            $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@8@', $nome);
                        }
                        
                        echo "\n==>"; print_r($title); echo "<==";

                    	$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
			$page = str_replace("'", "", $page);
                        $page = str_replace("<p>", " ", $page);
                        $page = str_replace("</p>", " ", $page);
                        $page = str_replace("<br>", "\n", $page);
                        $page = str_replace("<BR>", "\n", $page);
                        $page = str_replace("<br/>", "\n", $page);
                        $page = str_replace("<BR/>", "\n", $page);
                        $page = str_replace("<strong>", " ", $page);
                        $page = str_replace("</strong>", " ", $page);
			$page = str_replace('<span class="redactor-invisible-space">', " ", $page);
			$page = str_replace('</span>', " ", $page);
			$page = str_replace('<span>', " ", $page);
			$page = str_replace('<ul>', " ", $page);
			$page = str_replace('</ul>', " ", $page);
			$page = str_replace('<li>', "\n", $page);
			$page = str_replace('</li>', " ", $page);
			$page = str_replace('<p style="margin-left: 20px;">', " ", $page);
			$page = str_replace('<h1>', " ", $page);
                        $page = str_replace('</h1>', " ", $page);
			$page = str_replace('<h2>', " ", $page);
                        $page = str_replace('</h2>', " ", $page);
			$page = str_replace('<h3>', " ", $page);
                        $page = str_replace('</h3>', " ", $page);
			$page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
			$page = str_replace('>>>', "(", $page);
			$page = str_replace('<<<', ")", $page);
			$page = str_replace('<u>', " ", $page);
                        $page = str_replace('</u>', "\n", $page);
			$page = str_replace('<b>', " ", $page);
                        $page = str_replace('</b>', " ", $page);
			$page = str_replace('<o:p>', " ", $page);
                        $page = str_replace('</o:p>', " ", $page);
			$page = str_replace('<p style="margin-left: 40px;">', " ", $page);
			$page = str_replace('<del>', " ", $page);
                        $page = str_replace('</del>', " ", $page);
			$page = str_replace('/', "-", $page);
			$page = str_replace('<em>', " ", $page);
                        $page = str_replace('<-em>', " ", $page);

                        $status_descricao	= false;
                        $status_titulo 		= false;

			$status_condicao	= false;

			//Atualizar Categoria
                        /*$subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                        //if (!isset($subcategoriaMeli)) {
                        //        continue;
                        //} else {
                        //        if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
                        //               $subcategoriaMeli = "MLB191833";
                        //        }
                        //}
                        $body = ["category_id" => utf8_encode($subcategoriaMeli),];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, []);
                        if ($response['httpCode'] >= 300) {
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }
                        else{
                                echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');
                        }*/

                        //Atualização Descrição
                        $body = ["plain_text" => $page];
                        $response = $meli->put("items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Descricao";
			    //print_r($body);
			    //print_r($response);
			    //die;
                            fwrite($arquivo_log, ";Descricao nao alterada");
                        }
                        else{
                            $status_descricao = true;
                            echo " - OK Descrição";
			    //echo "\nPermalink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Descricao alterada");
                        }

                        //Atualização Título
                        $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Título";
			    //print_r($response);
                            fwrite($arquivo_log, ";Titulo não alterado");
                        }
                        else {
                            $status_titulo = true;
                            echo " - OK Título";
			    echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";Titulo alterado");
                        }
                        
                        //Atualização Condição
                        $condicao = ($produtoFilial->produto->e_usado)? "used" : "new";
                        $body = ["condition" => $condicao];
                        $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - ERROR Condição";
                            fwrite($arquivo_log, ";Condição não alterada");
                        }
                        else {
			    echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            echo " - OK Condição";
			    $status_condicao = true;
                            fwrite($arquivo_log, ";Condição alterada");
                        }
                        //Aqui termina o código
                        
                        //Alteração SEM JUROS
                        if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
                            //Atualização Descrição
                            echo " - meli_id_sem_juros: ".$produtoFilial->meli_id_sem_juros;
			    $body = ["plain_text" => $page];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}/description?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR Descrição";
                                fwrite($arquivo_log, ";SEM JUROS Descrição não alterado");
                            }
                            else{
                                $status_descricao = true;
                                echo " - SEM JUROS OK Descrição";
				//echo "\nPermalink SEM JUROS: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Descrição alterada");
                            }

			    //Atualizar Categoria
                            /*$subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                            //if (!isset($subcategoriaMeli)) {
                            //    continue;
                            //} else {
                            //    if ($produtoFilial->filial_id == 70 or $produtoFilial->filial_id == 82){
                            //            $subcategoriaMeli = "MLB191833";
                            //    }
                            //}
                            $body = ["category_id" => utf8_encode($subcategoriaMeli),];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                            if ($response['httpCode'] >= 300) {
                                echo "|Erro - ";//.ArrayHelper::getValue($response, 'body.permalink');
                            }
                            else{
                                echo " |Ok - ".ArrayHelper::getValue($response, 'body.permalink');
                            }*/

                            //Atualização Título
                            $body = ["title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60))];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR Título";
				print_r($response);
                                fwrite($arquivo_log, ";SEM JUROS Titulo não alterado");
                            }
                            else {
                                $status_titulo = true;
                                echo " - SEM JUROS OK Título";
				echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Titulo alterado");
                            }
                            
                            //Atualização Condição
                            $condicao = ($produtoFilial->produto->e_usado)? "used" : "new";
                            $body = ["condition" => $condicao];
                            $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - SEM JUROS ERROR Condição";
                                fwrite($arquivo_log, ";SEM JUROS Condição não alterada");
                            }
                            else {
                                $status_condicao = true;
                                echo " - SEM JUROS OK Condição";
				echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Condição alterada");
                            }
                        }
                        //Alteração SEM JUROS

            			/*if($status_descricao && $status_titulo && $status_condicao){
            				$produtoFilial->e_nome_alterado = false;
            				if($produtoFilial->save()){
            					echo " - OK Status";
            					fwrite($arquivo_log, ";Status alterado");
            				}
            				else{
            					echo " - ERROR Status";
            					fwrite($arquivo_log, ";Status não alterado");
            				}
            			}*/
                    }
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
