<?php

namespace console\controllers\actions\testeconsole;

use Yii;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use console\controllers\actions\omie\Omie;
use common\models\Produto;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\mail\AsyncMailer;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\ValorProdutoFilial;
use backend\models\PedidoCompraProdutoFilial;
use common\models\PedidoMercadoLivreShipments;
use common\models\PedidoProdutoFilial;
use mikehaertl\pdftk\Pdf;
use backend\functions\FunctionsML;
use backend\models\NotaFiscalProduto;
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;
use common\models\Funcao;
use common\models\MovimentacaoEstoqueDetalhe;
use backend\models\NotaFiscal;

class TesteAction extends Action
{
    //const APP_KEY_OMIE_SP              = '468080198586';
    //const APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';

    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

    public function run($q)
    {

        echo "INÍCIO TESTE\n\n";

	$nota_fiscal = NotaFiscal::find()->andWhere(["=", "id", 53567])->one();
	NotaFiscal::EmailNotaFiscalEntrada($nota_fiscal->id);
	die;

	//Remover texto da descrição dos anúncios do ML
	/*$meli = new Meli(static::APP_ID, static::SECRET_KEY);

	$produtos = Produto::find()	->andWhere(["=", "marca_produto_id", 1005])
					->orderBy(["id"=>SORT_ASC])
					->all();
	foreach($produtos as $k => $produto){
		echo "\n".$k." - ".$produto->id;

		$produto_filials = ProdutoFilial::find()->andWhere(["=", "produto_id", $produto->id])
							->andWhere(["is not", "meli_id", null])
							->andWhere(["<>", "meli_id", ""])
							->orderBy(["id"=>SORT_ASC])
							->all();
		foreach($produto_filials as $i => $produto_filial){
			echo "\n	".$i." - ".$produto_filial->id." - ".$produto_filial->filial_id;

			$filial		 = Filial::find()->andWhere(["=", "id", $produto_filial->filial_id])->one();
		        $user            = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
		        $response        = ArrayHelper::getValue($user, 'body');
			if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
			        $meliAccessToken = $response->access_token;
			        echo " - ".$meliAccessToken;

				$response_item = $meli->get("/items/".$produto_filial->meli_id."/description?access_token=" . $meliAccessToken);
				if($response_item["httpCode"] < 300){
					$descricao = $response_item["body"]->plain_text;
					$pos = strpos($descricao, "\nMATERIAL VAI NO PRIME - PRONTO PRA PINTURA");
					if ($pos === false) {
					    echo " - Descrição OK";
					} else {
					    echo " - Descrição ERRADA";
					    //print_r($descricao);
					    $descricao_limpa = str_replace("\nMATERIAL VAI NO PRIME - PRONTO PRA PINTURA", "", $descricao);

					    $body["plain_text"] = $descricao_limpa;
					    $response_item = $meli->put("items/".$produto_filial->meli_id."/description?access_token=" . $meliAccessToken, $body, []);
				            while ($response_item['httpCode'] == 429) {
					                echo " - ERRO";
					                $response_item = $meli->put("items/".$produto_filial->meli_id."/description?access_token=".$meliAccessToken, $body, []);
					            }
					            //	    print_r($response); die;
					            if ($response_item['httpCode'] >= 300) {
					                echo " - Descrição não alterada";
					            } else {
					                echo " - Descrição alterada";
            				     	    }
					     }
				}
				else{
					echo " - SEM descrição";
				}
			}
			else{
				echo " - Fora do ML";
			}
		}
	}
	die;*/

	/*$produtos_filial = ProdutoFilial::find()->andWhere(["=", "produto_id", $q])->all();
        foreach($produtos_filial as $k => $produto_filial){
                echo "\n".$k." - ".$produto_filial->id;
                print_r(FunctionsML::atualizarImagens($produto_filial->produto));
        }
        die;*/

	//Tratar preços GAUSS
	/*$produtos = Produto::find()	->andWhere(["like", "codigo_fabricante", "GL"])
					->andWhere(["=", "right(codigo_global, 1)", 0])
					->orderBy(["id" => SORT_ASC])
					->all();

	foreach($produtos as $k => $produto){
		echo "\n".$k." - ".$produto->id." - ".$produto->codigo_global;

		$produto_filial = ProdutoFilial::find()	->andWhere(["=", "filial_id", 8])
							->andWhere(["=", "produto_id", $produto->id])
							->one();

		if($produto_filial){
			echo " - ".$produto_filial->id." - ".$produto_filial->quantidade;
		}
	}
	die;*/

	//Excluir movimentações MG4->SP2
/*	$omie = new Omie(static::APP_ID, static::SECRET_KEY);

	$APP_KEY_OMIE_CONTA_SP2     = '468080198586';
        $APP_SECRET_OMIE_CONTA_SP2  = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_MG4     = '1758907907757';
        $APP_SECRET_OMIE_CONTA_MG4  = '0a69c9b49e5a188e5f43d5505f2752bc';

	$movimentacoes_estoque_detalhe = MovimentacaoEstoqueDetalhe::find()	->orderBy(["id" => SORT_ASC])
										->andWhere(["=", "id", 563])
										->all();
	foreach($movimentacoes_estoque_detalhe as $k => $movimentacao_estoque_detalhe){
		echo "\n".$k." - ".$movimentacao_estoque_detalhe->id;

                $body = [
                            "call" => "ExcluirAjusteEstoque",
                            "app_key" => $APP_KEY_OMIE_CONTA_SP2,
                            "app_secret" => $APP_SECRET_OMIE_CONTA_SP2,
                            "param" => [
                                "id_ajuste" => $movimentacao_estoque_detalhe->id_ajuste_omie_entrada
                            ]
                        ];

                $response_omie_excluir_ajuste_estoque_entrada = $omie->consulta("/api/v1/estoque/ajuste/?JSON=",$body);
                print_r($response_omie_excluir_ajuste_estoque_entrada); 
		if($response_omie_excluir_ajuste_estoque_entrada["httpCode"]==200){
			echo " - Ajuste entrada SP2 excluído";
		}
		else{
			echo " - Ajuste entrada SP2 não excluído";
		}

		$body = [
                            "call" => "ExcluirAjusteEstoque",
                            "app_key" => $APP_KEY_OMIE_CONTA_MG4,
                            "app_secret" => $APP_SECRET_OMIE_CONTA_MG4,
                            "param" => [
                                "id_ajuste" => $movimentacao_estoque_detalhe->id_ajuste_omie_saida
                            ]
                        ];

                $response_omie_excluir_ajuste_estoque_saida = $omie->consulta("/api/v1/estoque/ajuste/?JSON=",$body);
		print_r($response_omie_excluir_ajuste_estoque_saida); 
		if($response_omie_excluir_ajuste_estoque_saida["httpCode"]==200){ 
                        echo " - Ajuste saida MG4 excluído";
                }
                else{ 
                        echo " - Ajuste saida MG4 não excluído";
                }


		//die;
	}
	die;*/

	//BR
/*	$funcao       = Funcao::find()->andWhere(['=', 'id', 7])->one();
	$file_planilha        = "br_2021-10-05_precificado.csv";
	$parametros   = '{"coluna_codigo_fabricante":"5","coluna_estoque":"3","coluna_preco":"4","coluna_preco_compra":"2","coluna_nome":"1","coluna_capas":"","coluna_derivacao":"","file_planilha":""}';

	//VANNUCCI
	//$funcao 	= Funcao::find()->andWhere(['=', 'id', 14])->one();
	//$file_planilha 	= "RELACAO_OPT08-10-21.csv";
	//$parametros	= '{"coluna_codigo_fabricante":"4","coluna_estoque":"7","coluna_preco":"","coluna_preco_compra":"","coluna_descricao":"5","coluna_nome":"1","coluna_capas":"","coluna_derivacao":"","file_planilha":""}';

        //MORELATE
	//$funcao         = Funcao::find()->andWhere(['=', 'id', 1])->one();
	//$file_planilha  = "more19.csv";
	//$parametros     = '{"coluna_codigo_fabricante":"0","coluna_estoque":"1","coluna_preco":"","coluna_preco_compra":"2","coluna_nome":"","coluna_capas":"","coluna_derivacao":"","file_planilha":""}';

        $retorno = ($funcao->caminho.$funcao->funcao_nome)::run($parametros, $file_planilha);*/
//	die;*/

/*	$produtos_filial = ProdutoFilial::find()->andWhere(["=", "produto_id", $q])->all();
	foreach($produtos_filial as $k => $produto_filial){
		echo "\n".$k." - ".$produto_filial->id;
		print_r(FunctionsML::atualizarCategoria($produto_filial->produto));
	}
	die;*/

	/*$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", 328930])->one();
	print_r(FunctionsML::CriarAnuncioML($produto_filial));
	die;*/

	/*$produtos_gauss = Produto::find()->andWhere(["codigo_fabricante" => ['GL126H1','GL132H1','GL55H1','GL01H1','GL02H1','GL91H1','GL44H1','GL142H1','GL153H10','GL87H10','GL86H10','GL95H11','GL23H11','GL24H11','GL131H11','GL130H11','GL113H11B','GL112H11B','GL83H13','GL82H13','GL115H15','GL114H15','GL117H16','GL116H16','GL76H21W','GL49H21W','GL880H27B','GL880H27','GL880H27A','GL881H27B','GL881H27','GL881H27A','GL127H3','GL133H3','GL57H3','GL05H3','GL06H3','GL92H3','GL45H3','GL90H3C','GL67H4','GL09H4','GL10H4','GL128H4','GL59H4','GL11H4','GL12H4','GL68H4','GL93H4','GL46H4','GL15H5','GL16H5','GL47H5','GL0885','GL0886','GL77H6W','GL129H7','GL134H7','GL61H7','GL19H7','GL20H7','GL94H7','GL48H7','GL144H7','GL79H8','GL78H8','GL81H9','GL80H9','GL96HB1','GL27HB1','GL28HB1','GL97HB3','GL31HB3','GL32HB3','GL98HB4','GL35HB4','GL36HB4','GL99HB5','GL39HB5','GL40HB5','GL151HS1','GL50HS1','GL150HS5','GL157HS5X','GL63M5','GL43M5','GL64124MF','GL152H16','GL85H16','GL84H16','GL65Q5','GL64Q5','GL4540','GL5550','GL12728','GL143H4','GL905A','GL6461','GL7515','GL7515A','GL7505','GL3157A','GL3156','GL3156A','GL3157','GL6428','GL6418','GL6410','GL6411','GL6418A','GL6411A','GL0075','GL0075A','GL2473MFX6','GL2721MF','GL2721MF8','GL2721MFX','GL2351MF6','GL2752MF','GL2452MFX6','GL2722MF','GL2722MFX','GL2723MFX6','GL2741MF8','GL2741MF','GL2741MFX','GL2560','GL70','GL1034','GL1176','GL1034B','GL1176A','GL72','GL1141','GL1141B','GL12403','GL13401','GL12495','GL1056D','GL1056DC','GL135','GL1056A','GL1141A','GL1056B','GL1141C','GL0067A','GL0067C','GL74','GL0067','GL0067B','GL0067D','GL0053','GL0053A','GL137','GL0069','GL0069A','GL2721','GL2721B','GL921','GL2820','GL2723','GL2840','GL2722','GL2821','GL2841','GL136','GL2825','GL2825B','GL921A','GL7504','GL2827','GL158D1R','GL145D1S','GL155D2R','GL146D2S','GL159D3R','GL147D3S','GL149D4R','GL148D4S']])->all();
	foreach($produtos_gauss as $k => $produto_gauss){
		echo "\n".$k." - ".$produto_gauss->id." - ".$produto_gauss->nome;

		$produto_filial = ProdutoFilial::find()	->andWhere(["=", "produto_id", $produto_gauss->id])
							->andwhere(["=", "filial_id", 8])
							->one();
		if($produto_filial){
			echo " - ".$produto_filial->quantidade;

			$produto_filial->quantidade = 9999;
			if($produto_filial->save()){
				echo " - Quantidade salva";
			}
			else{
				echo " - Quantidade não salva";
			}
		}
	}*/

	/*//Obter access_token de uma filial
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial          = Filial::find()->andWhere(["=", "id", 100])->one();
        $user            = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response        = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        echo "\n\n".$meliAccessToken."\n\n";  
        die;*/

	$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
	print_r(FunctionsML::atualizarPreco(null, $produto_filial));
        die;

	/*$produtos = [
		0 => "Vazio",
		1 => "Xampu",
		2 => "Condicionador",
		3 => "Hidratante",
		4 => "Tintura",
		5 => "Demaquilante",
	];

	$pratilheiras = [];
	for($i=1;$i<=40;$i++){
		for($j=1;$j<=40;$j++){ 
			$produto_escolhido = ($j%6);
                	//$pratilheiras[$i][$j] = $produtos[$produto_escolhido];
			$pratilheiras[$i][$j] = $produto_escolhido;
        	}
	}

	$produtos_quantidade = [
                0 => ["Vazio", 0],
                1 => ["Xampu",0],
                2 => ["Condicionador",0],
                3 => ["Hidratante", 0],
                4 => ["Tintura", 0],
                5 => ["Demaquilante", 0],
        ];
	print_r($produtos_quantidade);
	for($i=1;$i<=40;$i++){
                for($j=1;$j<=40;$j++){ 
                        $produtos_quantidade[$pratilheiras[$i][$j]][1] += 1;
                }
        }

	print_r($pratilheiras);
	print_r($produtos_quantidade);

	die;*/

	//$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
        //print_r($produto_filial->atualizarMLPreco());
        //die;

	/*$produtos_filiais = ProdutoFilial::find()->andWhere(["filial_id" =>[88, 90]])
        					 //->andWhere(["is not", "meli_id", null])
				                 //->andWhere([">", "quantidade", 0])
						 //->innerJoin("produto", "produto_filial.produto_id = produto.id")
						 //->andWhere(["=", "id", $q])
						 ->orderBy(["filial_id" => SORT_ASC, "id" => SORT_ASC])
				                 ->all();
        foreach ($produtos_filiais as $k => $produto_filial) {

            echo "\n" . $k . " - " . $produto_filial->id." - ".$produto_filial->filial_id;
            //print_r(FunctionsML::atualizarQuantidade($produto_filial->produto));
            //print_r($produto_filial->atualizarMLPreco());
            //print_r(FunctionsML::atualizarCategoria($produto_filial->produto));
	    print_r(FunctionsML::atualizarDescricao($produto_filial->produto));
        }
        die;*/

        //Obter access_token de uma filial
	/*$meli = new Meli(static::APP_ID, static::SECRET_KEY);
	$filial		 = Filial::find()->andWhere(["=", "id", 72])->one();
	$user 		 = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response	 = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
	echo "\n\n".$meliAccessToken."\n\n";*/

	/*$body = [
		"from" => [
			"user_id" => "193724256",
		],
		"to" => [
			"user_id" => "267552213",
		],
		"text" => "OBRIGADO POR COMPRAR NO PEÇAAGORA.COM
PARA QUE POSSAMOS TER CERTEZA QUE VOCÊ COMPROU A PEÇA CORRETA, POR FAVOR NOS INFORME CHASSI MODELO E ANO DO VEICULO NO QUAL VAI APLICAR O PRODUTO.
ASSIM GARANTIMOS A ASSERTIVIDADE NO ENVIO DA PEÇA E SUA SATISFAÇÃO, QUE SÃO NOSSOS PRINCIPAIS OBJETIVOS!
PEÇAAGORA AGRADECE!",
	];

	$response_order = $meli->get("/orders/4884906804?access_token=" . $meliAccessToken);
        print_r($response_order);

	$response_message = $meli->post("messages/packs/4884906804/sellers/193724256?access_token=" . $meliAccessToken, $body);
        print_r($response_message);

	$response_message = $meli->get("/messages/packs/4884851971/sellers/193724256?access_token=" . $meliAccessToken);
        print_r($response_message);

	//$response_item = $meli->get("/items/MLB1884769350?access_token=" . $meliAccessToken);
	//print_r($response_item);

	//$response_categoria = $meli->get("/categories/MLB431271/attributes?access_token=" . $meliAccessToken);
        //print_r($response_categoria);

	die;*/

        //TESTE OMIE
	/*$omie = new Omie(static::APP_ID, static::SECRET_KEY);

	$APP_KEY_OMIE_SP            = '468080198586';
        $APP_SECRET_OMIE_SP         = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_CONTA_MG4     = '1758907907757';
        $APP_SECRET_OMIE_CONTA_MG4  = '0a69c9b49e5a188e5f43d5505f2752bc';

	$body = [
            "call" => "PosicaoEstoque",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                //"pagina" => 1,
                //"registros_por_pagina" => 1,
                //"apenas_importado_api" => "N",
                //"clientesFiltro" => [
                //    "cnpj_cpf" => "62180045115",
                //]
		"cod_int"	=> "PA280532",
		"data"		=> "08/10/2021"
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=", $body);
	print_r($response_omie);

	$body = [
            "call" => "ListarPosEstoque",
            "app_key" => $APP_KEY_OMIE_SP,
            "app_secret" => $APP_SECRET_OMIE_SP,
            "param" => [
                "nPagina"               => 1,
                "nRegPorPagina"         => 500,
                "dDataPosicao"          => date("d/m/Y"),
                "cExibeTodos"           => "N"
                //"codigo_local_estoque": 0
            ]
        ];
        $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        $quantidade_paginas = $response_omie["body"]["nTotPaginas"];

        for($i = 1;$i <= $quantidade_paginas;$i++){
            echo "\nPágina: ".$i;
            $body = [
                "call" => "ListarPosEstoque",
                "app_key" => $APP_KEY_OMIE_SP,
                "app_secret" => $APP_SECRET_OMIE_SP,
                "param" => [
                    "nPagina"               => $i,
                    "nRegPorPagina"         => 500,
                    "dDataPosicao"          => date("d/m/Y"),
                    "cExibeTodos"           => "N"
                    //"codigo_local_estoque": 0
                ]
            ];
            $response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
            foreach($response_omie["body"]["produtos"] as $k => $produto){
                echo "\n".$k." - ".$produto["cCodigo"]." - Quantidade: ".$produto["nSaldo"];
            }
        }

	die;*/
        //TESTE OMIE

        //JUNTAR ARQUIVOS PDF
        /*$pdf = new Pdf([
            'A' => '/var/tmp/4233684603.pdf',
            'B' => '/var/tmp/4237142035.pdf',     // B is alias for file2.pdf
        ]);
        
        // new.pdf will have pages A1, B3, A2, B4, A3, B5, ...
        $result = $pdf  ->shuffle(1, 1, 'A')    // pages 1-5 from A
                        ->shuffle(1, 1, 'B')    // pages 3-8 from B
                        ->saveAs('/var/tmp/new.pdf');
        if ($result === false) {
            $error = $pdf->getError();
            print_r($error);
        }
	die;*/

        //ta
        /*$arquivo_empresas = fopen("/var/tmp/teste/empresas_log.csv", "a");
	$arquivo_acumuladores = fopen("/var/tmp/teste/acumuladores_log.csv", "a");
        //fwrite($arquivo, "sku".chr(9)."price".chr(9)."minimum-seller-allowed-price".chr(9)."maximum-seller-allowed-price".chr(9)."quantity");

        $empresas        = array();
        $acumuladores    = array();

        $linhasArray = Array();
        $file = fopen("/var/tmp/acumuladores.csv", 'r'); //Abre arquivo com pre  os para subir

	$x = 0;

        while (($line = fgetcsv($file,null,';')) !== false)
        {
		if($x++ == 0){ continue; }

                if(!in_array($line[3], $acumuladores)){
                        $acumuladores[] = $line[3];
                }

		$empresas[$line[0]][$line[2]][] = $line[2];
		$empresas[$line[0]][$line[2]][] = $line[3];
		$x++;
        }
        fclose($file);

	fwrite($arquivo_acumuladores, "CODIGO;ACUMULADOR");
	foreach($acumuladores as $i => $acumulador){
		fwrite($arquivo_acumuladores, "\n".$i.";".$acumulador);
	}

	foreach($empresas as $k => $empresa){
		foreach($empresa as $j => $acumulador){
			$chave = array_search($acumulador[1], $acumuladores);
			//echo "\n".$k." - ".$j." - ".$chave." - ".$acumulador[1];
			$empresas[$k][$j][] = $chave;
			$empresas[$k][$j][] = $acumuladores[$chave];
		}
	}

	fwrite($arquivo_empresas, "CODIGO_EMPRESA;CODIGO_ACUMULADOR_ANTIGO;ACUMULADOR_ANTIGO;CODIGO_ACUMULADOR_NOVO");
        foreach($empresas as $h => $empresa){
		foreach($empresa as $t => $acumuladores_empresa){
			fwrite($arquivo_empresas, "\n".$h);
			foreach($acumuladores_empresa as $r => $acumulador){
				echo " - ".$acumulador;
				fwrite($arquivo_empresas, ";".$acumulador);
			}
		}
        }

	fclose($arquivo_empresas);
	fclose($arquivo_acumuladores);*/

        //$password = "123opt123";
        //var_dump(Yii::$app->security->generatePasswordHash($password));

        /*$APP_KEY_OMIE_SP              = '468080198586';
    	$APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';

    	$APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
	$APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';

	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/produtos/nfconsultar/?JSON={"call":"ConsultarNF","app_key":"'.$APP_KEY_OMIE_SP.'","app_secret":"'.$APP_SECRET_OMIE_SP.'","param":[{"cChaveNFe":"41181121195013000133550010000026071000302847"}]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $produtos = curl_exec($ch);
        $produtos_codigo = json_decode($produtos);
        curl_close($ch);
        print_r($produtos_codigo);


	die;


	$emails_destinatarios   = ['wagnercampaljf@yahoo.com.br', 'dev.pecaagora@gmail.com', 'nfe.pecaagora@gmail.com', 'compras.pecaagora@gmail.com'];
        $assunto                = "Teste Dev - Imagem 27";
        //$email_texto          = "https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2";
        //$email_texto                  = "<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>";
        $email_texto            = "<div>Boa tarde, Como vai?</div>

<br><div>Segue dados de um novo pedido a ser faturado. Nosso financeiro ira realizar o pagamento conforme combinado anteriormente.<div>

<br><div><img  width='15%' heigth='15%' src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'></div>

<br><div>Cod.:</div>
<div>Descricao: SUPER BONDER LOCTITE PRECISAO 10G DOBRO DE PRODUTO (BONDER10)</div>
<div>Quantidade:  * 1  Unidade</div>
<div>Valor: estoque</div>

<br><div>Envio: Carm  polis de Minas, 963, Vila Maria.</div>


<br></br><div>Atenciosamente,</div>


<br></br><div>Peca Agora</div>
<div>Site: https://www.pecaagora.com/</div>
<div>E-mail: compras.pecaagora@gmail.com</div>
<div>Setor de Compras:(32)3015-0023</div>
<div>Whatsapp:(32)988354007</div>
<div>Skype: pecaagora</div>";

        var_dump(\Yii::$app->mailer   ->compose()
                                      ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                                      //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                                      ->setTo($emails_destinatarios)
                                      //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                                      ->setSubject($assunto)
                                      //->setTextBody($email_texto)
                                      ->setHtmlBody($email_texto)
                                      ->send());
	*/

        /*$results = ProdutoFilial::find()
        ->select(['produto_filial.id', "(coalesce((filial.nome),'') || coalesce((produto.nome),'')) as text"])
        ->joinWith(['produto', 'filial'])
        ->where([
            'like',
            'lower(produto.nome)',
            strtolower($q)
        ])
        ->orWhere([
            'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
        ])
        ->limit(10)
        ->createCommand()
        ->queryAll();
        $out['results'] = array_values($results);
        print_r($results); print_r($out);*/

        /*$caminhoImagem   = "/home/dev_peca_agora/Downloads/predador.jpg";
            if (file_exists($caminhoImagem)) {
             $imagem = Imagens::findOne(['id'=>5371]);
             $imagem->produto_id         = 6661;
             $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
             $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagem));
             $imagem->ordem              = 1;
             $imagem->save();
        }*/

        /*//Teste investimento
        $valor_mensal   = 1250*0.08;
        $valor_corrente  = 0;
        for($x=1;$x<=12;$x++){
           
            $valor_corrente += 0.0025*$valor_corrente + $valor_mensal;
            echo "\n".$valor_corrente;

            if(($x%18)==0){
                $valor_mensal = $valor_mensal*1.04;
                echo " - ".$valor_mensal;
            }
            
        }
        
        echo "\n".$valor_corrente."\n";*/


        /*//Teste simulação salários e impostos
        $inss = [[0, 8], [1751.82, 9], [2919.73,11]]; 
        $ir = [[0, 0], [1903.99, 7.5], [2826.66,15], [3751.06,22.5], [4664.68,27.5]]; 
        
        for($x=900;$x<=30000;$x++){
            echo "\n".$x." - ";
            
            $inss_desconto = 0;
            $ir_desconto = 0;
            
            foreach($inss as $k => $faixa){
                if(isset($inss[($k+1)])){
                    if($x >= $faixa[0] && $x< $inss[($k+1)][0]){
                        $inss_desconto = ($x*($faixa[1]/100));
                    }
                }
                else{
                    if($x >= $faixa[0]){
                        $inss_desconto = ($x*($faixa[1]/100));
                    }
                }
            }
            
            foreach($ir as $k => $faixa){
                if(isset($inss[($k+1)])){
                    if($x >= $faixa[0] && $x< $ir[($k+1)][0]){
                        $ir_desconto = ($x*($faixa[1]/100));
                    }
                }
                else{
                    if($x >= $faixa[0]){
                        $ir_desconto = ($x*($faixa[1]/100));
                    }
                }
            }
            
            $valor_final = $x - $inss_desconto - $ir_desconto;
            echo $valor_final;

        }*/

        /*unlink("/var/tmp/amazon_precos_quantidade.csv");
        $arquivo = fopen("/var/tmp/amazon_precos_quantidade.csv", "a");
        fwrite($arquivo, "sku".chr(9)."price".chr(9)."minimum-seller-allowed-price".chr(9)."maximum-seller-allowed-price".chr(9)."quantity");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/produtos_pecaagora_11-01-2020.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];
            
            if($i == 0){
                continue;
            }
            
            $preco = round($linhaArray[13], 2);
            $preco = str_replace(".",",",$preco);
            
            if($preco == 0){
                echo " - ZERADO";
                continue;
            }
            
            fwrite($arquivo, "\n".$linhaArray[1].chr(9).$preco.chr(9)."".chr(9)."".chr(9).$linhaArray[12]);
        }
        
        fclose($arquivo);*/

        /*$valor_inicial      = 0;
        $valor_final        = 1000000;
        $valor_mensal       = 2000;
        $rendimento_mensal  = 1.005;
        $valor_corrente     = $valor_inicial;
        $quantidade_meses   = 0;
        
        while($valor_corrente <= $valor_final){
            echo "\n".$quantidade_meses." - ".$valor_corrente;    
            $quantidade_meses++;
            $valor_corrente = ($valor_corrente * $rendimento_mensal) + $valor_mensal;
        }
        
        echo "\nValor Inicial:      ".$valor_inicial;
        echo "\nValor Final:        ".$valor_final;
        echo "\nValor Mensal:       ".$valor_mensal;
        echo "\nRendimento Mensal:    ".(($rendimento_mensal-1)*100)."%";
        echo "\nQuantidade de Meses:  ".$quantidade_meses;
        echo "\nValor Total:          ".$valor_corrente;
        
        $valor_x = pow($rendimento_mensal,$quantidade_meses)*$valor_inicial;
        $valor_y = 0;
        
        for($i=0 ; $i < $quantidade_meses ; $i++){
            $valor_y += pow($rendimento_mensal,$i) * $valor_mensal;
        }
        
        $valor_z = $valor_x + $valor_y;
        echo "\nValor por fórmula:  ".$valor_z;

        $quantidade_meses = 120;
        $valor_x = pow($rendimento_mensal,$quantidade_meses)*$valor_inicial;
        $valor_y = 0;
        
        for($i=0 ; $i < $quantidade_meses ; $i++){
            $valor_y += pow($rendimento_mensal,$i);
        }
        
        $valor_z = (($valor_final - $valor_x) / $valor_y);
        echo "\nValor mensal para chegar a ".$valor_final.":  ".$valor_z;
       
        /*$valor_inicial      = 0;
        $valor_final        = 100000;
        $valor_mensal       = 2000;
        $rendimento_mensal  = 1.005;
        $valor_corrente     = $valor_inicial;
        $quantidade_meses   = 0;*/

        //$produto = Produto::find()->andWhere(['=','id',3055])->one();
        //echo "\n\n".$produto->getUrlImageML()."\n\n";*/

        /*$imagem     = Imagens::find()->andWhere(['=','produto_id',58093])->one();
        if($imagem){
            echo "Imagem encontrada";
            
            $caminho    = "https://www.pecaagora.com/site/get-link?produto_id=".$imagem->produto_id."&ordem=".$imagem->ordem;
            copy($caminho, '/var/www/html/pecaagora/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".webp" );
        
            $resultado = shell_exec('cd /var/www/html/pecaagora/frontend/web/assets/img/imagens_temporarias ; mogrify -format jpg *.webp');
            echo $resultado;
        }
        else{
            echo "Imagem não encontrada";
        }*/

        //Verifica Vannucci, se existem os produtos
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_vannucci_produtos_nao_existem_02-07-2020.csv", "a");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];

	    fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'"');

            if($i == 0){
                continue;
            }

	    //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
	    $produto = Produto::find()->andWhere(['like', 'codigo_global', $linhaArray[4]])->one();

            if($produto){
		fwrite($arquivo, ';"Produto encontrado"');
            }
	    else{
		fwrite($arquivo, ';"Produto nao encontrado"');
	    }
        }

        fclose($arquivo);*/



        //Verifica Vannucci, se existem os produtos
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_produto_b2w_conferir_ean_07-07-2020.csv", "a");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/produto_b2w_conferir_ean_07-07-2020.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];

            fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'"');

            if($i == 0){
                continue;
            }

            //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $linhaArray[2]])->one();

            if($produto_filial){
                fwrite($arquivo, ';"'.$produto_filial->produto->nome.'";"Produto encontrado"');
            }
            else{
		 fwrite($arquivo, ';;"Produto nao encontrado"');
            }
        }

        fclose($arquivo);

        print_r(date("Y-m-d_H-i-s"));
        

        echo "\n\nFIM TESTE";*/


        //Gera planilha dos produtos da Amzon, para zerar as filiais sem ser a Vannucci, BR, Dib, Morelate e LNG
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_produtos_amazon.csv", "a");
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/produtos-amazon.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];
            
            fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'"');
            
            if($i == 0){
                fwrite($arquivo, ";quantidade_atual;status;filial_id");
                continue;
            }
            
            //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $linhaArray[0]])->one();
            
            if($produto_filial){
                
                $quantidade = 0;
                if($produto_filial->filial_id == 38 || $produto_filial->filial_id == 43 || $produto_filial->filial_id == 60 || $produto_filial->filial_id == 72 || $produto_filial->filial_id == 97){
                    $quantidade = $produto_filial->quantidade;
                }

                fwrite($arquivo, ';'.$quantidade.';"Produto encontrado";'.$produto_filial->filial_id);
            }
            else{
                fwrite($arquivo, ';0;"Produto nao encontrado"');
            }
        }
        
        fclose($arquivo);
        
        print_r(date("Y-m-d_H-i-s"));*/


        //CRIAR CAIXAS GAUSS
        /*$caixas = [10, 20, 50, 100, 150, 200];
    	
    	$produtos_gauss = Produto::find()  ->andWhere(['=','fabricante_id', 86])
                                           ->andWhere(['not like','codigo_fabricante','CX.']) 
    	                                   ->orderBy('codigo_fabricante')
    	                                   ->all();
    	
        foreach($produtos_gauss as $k => $produto_gauss){
            
            echo "\n".$k." - ".$produto_gauss->id." - ".$produto_gauss->codigo_fabricante;
            
            foreach($caixas as $caixa){
                
                echo "\n".$caixa;
                
                $produto_caixa = Produto::find()->andWhere(['=', "codigo_fabricante", "CX.".$produto_gauss->codigo_fabricante."-".$caixa])->one();
                
                if($produto_caixa){
                    echo " - Produto caixa encontrado - ".$produto_caixa->codigo_fabricante;
                    
                    $produto_filial_caixa = ProdutoFilial::find()->andWhere(['=','produto_id', $produto_caixa->id])->one();
                    if($produto_filial_caixa){
                        echo " - Estoque caixa encontrado";
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_gauss->id])
                                                                ->andWhere(['=','filial_id', 96])
                                                                ->one();
                        
                        if($produto_filial){
                            $produto_filial_caixa->quantidade = $produto_filial->quantidade;
                        }
                        else{
                            $produto_filial_caixa->quantidade = 0;
                        }
                        
                        if($produto_filial_caixa->save()){
                            echo " - Estoque Alterado";
                        }
                        else{
                            echo " - Estoque NÃO Alterado";
                        }
                        
                    }
                    else{
                        echo " - Estoque caixa NÃO encontrado";
                    }
                }
                else{
                    echo " - Produto caixa não encontrado";
                    
                    $produto_novo                           = new Produto;
                    //$produto_novo                         = $produto_gauss;
                    $produto_novo->codigo_global            = "CX.".$produto_gauss->codigo_global."-".$caixa;
                    $produto_novo->codigo_fabricante        = "CX.".$produto_gauss->codigo_fabricante."-".$caixa;
                    $produto_novo->nome                     = "CAIXA ".$caixa." ".$produto_gauss->nome;
                    $produto_novo->multiplicador            = $caixa;
                    $produto_novo->peso                     = $produto_gauss->peso * $caixa;
                    $produto_novo->largura                  = $produto_gauss->largura * $caixa;
                    $produto_novo->altura                   = $produto_gauss->altura;
                    $produto_novo->profundidade             = $produto_gauss->profundidade;
                    $produto_novo->codigo_montadora         = $produto_gauss->codigo_montadora;
                    $produto_novo->fabricante_id            = $produto_gauss->fabricante_id;
                    $produto_novo->slug                     = $produto_gauss->slug;
                    $produto_novo->subcategoria_id          = $produto_gauss->subcategoria_id;
                    $produto_novo->aplicacao                = $produto_gauss->aplicacao;
                    $produto_novo->texto_vetor              = $produto_gauss->texto_vetor;
                    $produto_novo->codigo_similar           = $produto_gauss->codigo_similar;
                    $produto_novo->aplicacao_complementar   = $produto_gauss->aplicacao_complementar;
                    $produto_novo->video                    = $produto_gauss->video;
                    $produto_novo->codigo_barras            = $produto_gauss->codigo_barras;
                    $produto_novo->cest                     = $produto_gauss->cest;
                    $produto_novo->ipi                      = $produto_gauss->ipi;
                    $produto_novo->e_usado                  = $produto_gauss->e_usado;
                    $produto_novo->e_medidas_conferidas     = $produto_gauss->e_medidas_conferidas;
                    $produto_novo->e_ativo                  = $produto_gauss->e_ativo;
                    $produto_novo->e_valor_bloqueado        = $produto_gauss->e_valor_bloqueado;
                    $produto_novo->marca_produto_id         = $produto_gauss->marca_produto_id;
                    $produto_novo->produto_condicao_id      = 1;
                    $produto_novo->codigo_fornecedor        = $produto_gauss->codigo_fornecedor;
                    
                    
                    //print_r($produto_gauss); 
                    //print_r($produto_novo);
                    if($produto_novo->save()){
                        echo " - Produto CRIADO";

                        $produto_filial_novo                = new ProdutoFilial;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_gauss->id])
                                                                ->andWhere(['=','filial_id', 96])
                                                                ->one();
                        
                        if($produto_filial){
                            echo " - Estoque encontrado";
                            $produto_filial_novo->quantidade = $produto_filial->quantidade;
                        }
                        else{
                            echo " - Estoque não encontrado";
                            $produto_filial_novo->quantidade = 0;
                        }
                        
                        $produto_filial_novo->produto_id    = $produto_novo->id;
                        $produto_filial_novo->filial_id     = 96;
                        $produto_filial_novo->envio         = 1;
                        if($produto_filial_novo->save()){
                            echo " - Estoque CRIADO";
                        }
                        else{
                            echo " - Estoque NÃO CRIADO";
                        }
                    }
                    else{
                        echo " - Produto NÃO CRIADO";
                    }
                    //die;
                }
            }
        }*/

        /*$emails_destinatarios	= ['wagnercampaljf@yahoo.com.br', 'dev.pecaagora@gmail.com'];
	$assunto		= "Teste Imagem";
	//$email_texto		= "https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2";
	//$email_texto          	= "<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>";
	$email_texto		= "Boa tarde, Como vai?

Segue dados de um novo pedido a ser faturado. Nosso financeiro ira realizar o pagamento conforme combinado anteriormente.

<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>

Cod.:
Descricao: SUPER BONDER LOCTITE PRECISAO 10G DOBRO DE PRODUTO (BONDER10)
Quantidade:  * 1  Unidade
Valor: estoque

Envio: Carmópolis de Minas, 963, Vila Maria.


Atenciosamente,


Peca Agora
Site: https://www.pecaagora.com/
E-mail: compras.pecaagora@gmail.com
Setor de Compras:(32)3015-0023
Whatsapp:(32)988354007
Skype: pecaagora";

	var_dump(\Yii::$app->mailer   ->compose()
	         	              ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
        	 	              //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
        	 	              ->setTo($emails_destinatarios)
        	 	              //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
        	 	              ->setSubject($assunto)
        	 	              //->setTextBody($email_texto)
        	 	              ->setHtmlBody($email_texto)
        	 	              ->send());*/

        /*$emails_destinatarios = ["wagnercampaljf@yahoo.com.br"];
        $assunto = "teste remetente";
        $email_texto = "teste remetente texto";
        
        var_dump(\Yii::$app->mailer   ->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
            // ->setTo(["dev2.pecaagora@gmail.com"])
            ->setTo($emails_destinatarios)
            //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
            ->setSubject($assunto)
            ->setTextBody($email_texto)
            //->setHtmlBody($email_texto)
            ->send());*/

        //	$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
        //        var_dump(FunctionsML::atualizarImagens($produto_filial->produto));
        //        die;

        //$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
        //var_dump(FunctionsML::atualizarCategoria($produto_filial->produto));
        //die;

        //	$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
        //        var_dump(FunctionsML::atualizarQuantidade($produto_filial->produto));
        //        die;

        //$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
        //print_r($produto_filial->atualizarMLPreco());
        //die;

        /*$produtos_filiais = ProdutoFilial::find()->andWhere(["=", "filial_id", 8])
            //->andWhere(["is not", "meli_id", null])
            ->andWhere([">", "quantidade", 0])
            ->all();
        foreach ($produtos_filiais as $k => $produto_filial) {

            echo "\nProduto Filial Corrente:" . $k . " - " . $produto_filial->id;
            //print_r(FunctionsML::atualizarQuantidade($produto_filial->produto));
            print_r($produto_filial->atualizarMLPreco());

            //print_r(FunctionsML::atualizarCategoria($produto_filial->produto));
        }
        die;*/

        /*	$imagem = Imagens::find()->andWhere(['=', 'produto_id', 381989])->one();
	print_r($imagem);

	$imagem_nova = new Imagens();
	$imagem_nova->produto_id = $imagem->produto_id;
	$imagem_nova->imagem = $imagem->imagem;
	$imagem_nova->imagem_sem_logo = $imagem->imagem_sem_logo;
	$imagem_nova->ordem = 2;
	var_dump($imagem_nova->save());*/

        /*$imagens = Imagens::find()->select(["id", "produto_id"])->all();

	foreach($imagens as $k => $imagem){
		if(substr(sprintf('%o', fileperms('/var/www/imagens_produto/produto_'.$imagem->produto_id)), -4) != "0777"){
			echo "\n".$imagem->id." - ".substr(sprintf('%o', fileperms('/var/www/imagens_produto/produto_'.$imagem->produto_id)), -4);
		}
	}*/
        //echo substr(sprintf('%o', fileperms('/var/www/imagens_produto')), -4);

        //echo PedidoMercadoLivre::baixarPedidoML($q);

        //$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 1005])->all(); //KFIBRAQ
        //$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 493])->all(); //FIBRAAS
        //$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 951])->all(); //PUES
        /*$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 1176])->all(); //KFIBRAQ
	foreach($produtos as $produto){
		echo "\n".$produto->id;

		$produto_filial = ProdutoFilial::find() ->andwhere(["=", "produto_id", $produto->id])
							->andWhere(["=", "filial_id", 96])
							->one();
		if($produto_filial){
			echo " - ".$produto_filial->id;
			$valor_produto_filial = ValorProdutoFilial::find()->andwhere(["=", "produto_filial_id", $produto_filial->id])
									  ->orderBy(["dt_inicio" => SORT_DESC])
									  ->one();
			if($valor_produto_filial){
				echo " - ".$valor_produto_filial->valor;
				$produto_filial_fibra = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto->id])
										//->andwhere(["=", "filial_id", 87])
										//->andwhere(["=", "filial_id", 88])
										//->andwhere(["=", "filial_id", 89])
										->andwhere(["=", "filial_id", 90])
										->one();
				echo " - ".$produto_filial_fibra->id;
				$valor_produto_filial_novo = new ValorProdutoFilial;
				$valor_produto_filial_novo->valor = $valor_produto_filial->valor;
				$valor_produto_filial_novo->valor_cnpj = $valor_produto_filial->valor_cnpj;
				$valor_produto_filial_novo->valor_compra = $valor_produto_filial->valor_compra;
				$valor_produto_filial_novo->produto_filial_id = $produto_filial_fibra->id;
				$valor_produto_filial_novo->dt_inicio = date("Y-m-d H:i:s");
				var_dump($valor_produto_filial_novo->save());
			}
		}
	}*/

        /*$produtos_filiais = ProdutoFilial::find()->where(" produto_id in (select produto_id from produto_filial where filial_id in (87, 88, 89, 90)) and filial_id = 8 ")->all();
        
        foreach($produtos_filiais as $k => $produto_filial){
            echo "\n".$k." - ".$produto_filial->meli_id;
            
            $produto_filial_fibra = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                                                            ->andWhere(["filial_id" => [87, 88, 89, 90]])
                                                            ->one();

            if($produto_filial_fibra){
                echo " - Produto fibra encontrado";
                
                $produto_filial_fibra->meli_id = $produto_filial->meli_id;
                if($produto_filial_fibra->save()){
                    echo " - Fibra alterado";
                    
                    $pedidos_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_mercado_livre_produtos as $i => $pedido_mercado_livre_produto){
                        echo "\n".$i." - ".$pedido_mercado_livre_produto->id;
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_mercado_livre_produto->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

                    $pedidos_mercado_livre_produtos_produtos_filiais = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_mercado_livre_produtos_produtos_filiais as $i => $pedido_mercado_livre_produto_produto_filial){
                        echo "\n".$i." - ".$pedido_mercado_livre_produto_produto_filial->id;
                        $pedido_mercado_livre_produto_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_mercado_livre_produto_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

		    $pedidos_compra_produtos_filiais = PedidoCompraProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_compra_produtos_filiais as $i => $pedido_compra_produto_filial){
                        echo "\n".$i." - ".$pedido_compra_produto_filial->id;
                        $pedido_compra_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_compra_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }
                    
                    $pedidos_produtos_filiais = PedidoProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_produtos_filiais as $i => $pedido_produto_filial){
                        echo "\n".$i." - ".$pedido_produto_filial->id;
                        $pedido_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

                    if($produto_filial->delete()){
                        echo "\nProduto Casada deletado";
                    }
                    else{
                        echo "\nProduto Casada deletado";
                    }
                }
                else{
                    echo "\nFibra não alterado";
                }
            }
            else{
                echo " - Produto fibra não encontrado";
            }
        }*/


        /*$pedidos_mercado_livre_shipments = PedidoMercadoLivreShipments::find()  ->andWhere(["<>", "status", "delivered"])
										->andWhere([">=", "id", 21543])
										->orderBy(["id"=>SORT_DESC])
										->all();

        foreach($pedidos_mercado_livre_shipments as $k => $pedido_mercado_livre_shipment){
            echo "\n".$k." - ".$pedido_mercado_livre_shipment->id." - ".$pedido_mercado_livre_shipment->status;

	    $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "id", $pedido_mercado_livre_shipment->pedido_mercado_livre_id])->one();

	    echo " - ".$pedido_mercado_livre->id;

	    echo PedidoMercadoLivre::AtualizarPedidoML($pedido_mercado_livre->pedido_meli_id);
	    //echo PedidoMercadoLivre::baixarPedidoML($pedido_mercado_livre->pedido_meli_id);
        }*/

        /*$arquivo_origem = fopen("/var/tmp/preco_fibras_venda_casada_2021-07-02_02.csv", 'r');
        
        $x = 0;
        
        while (($line = fgetcsv($arquivo_origem,null,';')) !== false){
            
            echo "\n".$x++." - ".$line[0];
            
            if($x <= 1){
                continue;
            }
            
            $produtos_filiais = ProdutoFilial::find() ->andWhere(['=', 'produto_id', $line[1]])
                                                    ->andWhere(['filial_id' => [87, 88, 89, 90]])
                                                    ->all();
            foreach($produtos_filiais as $k => $produto_filial){

                $valor_produto_filial_verificacao = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->one();
                
                if($valor_produto_filial_verificacao){
                    continue;
                }
                
                echo "\n    ".$produto_filial->id; 
                
                $valor_produto_filial                       = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                $valor_produto_filial->valor                = $line[16];
                $valor_produto_filial->valor_cnpj           = $line[21];
                $valor_produto_filial->valor_compra         = $line[22];
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                if($valor_produto_filial->save()){
                    echo " - Valor criado";
                }
            }
        }
        
        fclose($arquivo_origem);*/

        var_dump(PedidoMercadoLivre::baixarPedidoML($q, false));
        die;

        $pedidos_mercado_livre = PedidoMercadoLivre::find()    //->andWhere(["pedido_meli_id" => ['4687601653','4688817681','4691368554','4691558403','4691517510']])
            ->andWhere(["=", "pedido_meli_id", $q])
            ->all();

        foreach ($pedidos_mercado_livre as $k => $pedido_mercado_livre) {
            echo "\n" . $k . " - " . $pedido_mercado_livre->id . " - " . $pedido_mercado_livre->pedido_meli_id;
            var_dump(PedidoMercadoLivre::baixarPedidoML($pedido_mercado_livre->pedido_meli_id, false));
        }
    }
}
