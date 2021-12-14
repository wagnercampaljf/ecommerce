<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class CreateContaDuplicadaAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";

	$meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial_duplicada   = Filial::find()->andWhere(['=','id',98])->one();
        $user_outro         = $meli->refreshAccessToken($filial_duplicada->refresh_token_meli);
        $response_outro     = ArrayHelper::getValue($user_outro, 'body');

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['=','id',38])
            ->andWhere(['<>','id',98])
            ->andWhere(['<>','id',43])
            ->all();

        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
            $meliAccessToken_outro = $response_outro->access_token;

            foreach ($filials as $filial) {

                $produtoFilials = $filial->getProdutoFilials()  ->andWhere(['IS NOT', 'meli_id', NULL])
                                                                ->andWhere(['>', 'quantidade', 0])
                                            			        /*->joinWith('produto')
                                                                ->andWhere(['not like','upper(produto.nome)','SEMI NOV'])
                                                                ->andWhere(['not like','upper(produto.nome)','SEMI-NOV'])
                                                                ->andWhere(['not like','upper(produto.nome)','SEMINOV'])
                                                                ->andWhere(['not like','upper(produto.nome)','REMAN'])
                                                                ->andWhere(['not like','upper(produto.nome)','RECOND'])
								                                ->andWhere(['not like','upper(produto.nome)','REFORMAD'])
                                                                ->andWhere(['not like','upper(produto.aplicacao)','SEMI NOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao)','SEMI-NOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao)','SEMINOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao)','REMAN'])
                                                                ->andWhere(['not like','upper(produto.aplicacao)','RECOND'])
								                                ->andWhere(['not like','upper(produto.aplicacao)','REFORMAD'])
                                                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMI NOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMI-NOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMINOV'])
                                                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','REMAN'])
                                                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','RECOND'])
								                                ->andWhere(['not like','upper(produto.aplicacao_complementar)','REFORMAD'])
                                                                ->andWhere(['not like','upper(produto.descricao)','SEMI NOV'])
                                                                ->andWhere(['not like','upper(produto.descricao)','SEMI-NOV'])
                                                                ->andWhere(['not like','upper(produto.descricao)','SEMINOV'])
                                                                ->andWhere(['not like','upper(produto.descricao)','REMAN'])
                                                                ->andWhere(['not like','upper(produto.descricao)','RECOND'])
								                                ->andWhere(['not like','upper(produto.descricao)','REFORMAD'])*/
                                                                //->andWhere(['produto_filial.produto_id' => [12408,12659,12678,15071,15222,15389,15390,15557,15558,15561,15563,15688,15691,15692,15693,15694,15695,28321,44623,56037,56068,56150,56236,222586,226359,227517,227624,227841,228684,228857,230581,231463,231786,231799,231802,231807,240929,248466,271472,273340,273662,274118,274926,275074,277510,277513,277861,277931,278512,278598,278861,279506,279999,280990,281257,282201,284787,284965,284989,285025,286326,287843,287845,287882,287905,291647,298632,313327,314111]])
                                                                //->andWhere(['=', 'filial_id', 97])
                                                                //->joinWith('produto')
                                                                //->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'MACANETA EXTERNA'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'CAPA PELUCIA'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'LANTERNA TRASEIRA'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'LANTERNA LATERAL'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'LAMPADA IMPORTADA'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'LANTERNA SETA'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'MAQUINA VIDRO'])
                                                        		//->andWhere(['=',"(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)",'TAPETE VINIL'])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['ABRACADEIRA CHARUTO', 'ABRACADEIRA DO', 'ABRACADEIRA FITA', 'ABRACADEIRA INOX', 'ABRACADEIRA KLIPER', 'ABRACADEIRA MIOLO', 'ABRACADEIRA PARA-LAMA', 'ABRACADEIRA RESERVATORIO', 'ABRACADEIRA ROSCA', 'ABRACADEIRA SEGURANCA', 'ABRACADEIRA SUPORTE', 'ABRACADEIRA TELA', 'ABRACADEIRA TIRANTE', 'ABRACADEIRA TUBO', 'ABRACADEIRA VARAO']])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => []])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['INTERRUPTOR ACIONAMENTO', 'INTERRUPTOR ALAVANCA', 'INTERRUPTOR BREAK-AR', 'INTERRUPTOR CAIXA', 'INTERRUPTOR DA', 'INTERRUPTOR DE', 'INTERRUPTOR DO', 'INTERRUPTOR EMERGENCIA', 'INTERRUPTOR ESTACIONARIO', 'INTERRUPTOR FAROL', 'INTERRUPTOR FREIO', 'INTERRUPTOR GERAL', 'INTERRUPTOR ILUMINACAO', 'INTERRUPTOR LAVADOR', 'INTERRUPTOR LIMPADOR', 'INTERRUPTOR LUZ', 'INTERRUPTOR PAINEL', 'INTERRUPTOR PRESSAO', 'INTERRUPTOR QUADRADO', 'INTERRUPTOR REDUZIDA', 'INTERRUPTOR ROTATIVO', 'INTERRUPTOR TECLA', 'INTERRUPTOR TEMPORIZADOR', 'INTERRUPTOR UNIVERSAL', 'INTERRUPTOR VOLVO', 'INVERSAO DE', 'INVERSOR 12V', 'INVERSOR 24V', 'INVERSOR DE', 'ISOLADOR DE', 'JUNTA EXPANSORA']])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['LAMPADA GE', 'LAMPADA IMPORT', 'LAMPADA IMPORTADA', 'LAMPADA LED', 'LAMPADA TESLLA', 'LANTERNA BARRA', 'LANTERNA BAU', 'LANTERNA BRAKE', 'LANTERNA BREAK', 'LANTERNA CROMADA', 'LANTERNA DE', 'LANTERNA DELIMITADORA', 'LANTERNA DIANTEIRA', 'LANTERNA DIANTEIRO', 'LANTERNA DIRECIONAL', 'LANTERNA DRL', 'LANTERNA EMERGENCIA', 'LANTERNA ESTRIBO', 'LANTERNA FOGUINHO', 'LANTERNA FRONTAL', 'LANTERNA GROW', 'LANTERNA GUIA', 'LANTERNA ILUMINACAO', 'LANTERNA IMPLOSAO', 'LANTERNA INTERNA', 'LANTERNA LAMINA', 'LANTERNA LATERAL', 'LANTERNA LIMITADORA', 'LANTERNA LUMINARIA', 'LANTERNA MANUAL', 'LANTERNA OLHO', 'LANTERNA P/CARRO', 'LANTERNA PENDENTE', 'LANTERNA P/ILUMINACAO', 'LANTERNA PLACA', 'LANTERNA PLACA/DELIMITADORA', 'LANTERNA POSICAO', 'LANTERNA QUADRADA', 'LANTERNA REATOR', 'LANTERNA REFLETVA', 'LANTERNA SETA', 'LANTERNA SINALSUL', 'LANTERNA TAPA-SOL', 'LANTERNA TETO', 'LANTERNA TETO/LATERAL', 'LANTERNA TRASEIRA', 'LANTERNA TRASEUIRA', 'LANTERNA TRASSEIRA', 'LANTERNA UNIVERSAL', 'LATERAL SPOILER', 'LAVA AUTOS', 'LAVA CARROS', 'LAVADOR EJETOR', 'LENTE AF', 'LENTE ASPOCK', 'LENTE CARRETA', 'LENTE DE', 'LENTE ESTRIBO', 'LENTE FAROL', 'LENTE GROW', 'LENTE INTERNA', 'LENTE LANTERNA', 'LENTE LATERAL', 'LENTE LUMINARIA', 'LENTE MERCEDES-BENZ', 'LENTE PAINEL', 'LENTE PALITAO', 'LENTE PLACA', 'LENTE SETA', 'LENTE SINALSUL', 'LENTE TETO', 'LENTE TRASEIRA', 'LIMITADOR DA', 'LIMPA AR', 'LIMPA CONTATO', 'LIMPADOR E', 'LIMPA ESTOFADOS', 'LIMPA PLASTICOS/ESTOFADOS', 'LIMPA PNEUS', 'LIMPA RADIADOR', 'LIMPA RODAS', 'LIMPA VIDROS', 'LINGUETA TRAVA', 'LIXADEIRA ORBITAL']])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['SENSOR DA', 'SENSOR ESTACIONAMENTO', 'SENSOR NIVEL', 'SENSOR PONTO', 'SENSOR PRESSAO', 'SENSOR RESERVATORIO', 'SEPARADOR MB', 'SEPARADOR VW', 'SERRA COPO', 'SHAMPOO AUTOMOTIVO', 'SILENCIOSO VOLVO', 'SILICONE AEROSOL', 'SILICONE DE', 'SILICONE FRASCO', 'SILICONE GEL', 'SILICONE LIQUIDO', 'SILICONE PARA', 'SILICONE SPRAY', 'SILICONE SUPER', 'SIMULADOR DE', 'SINALIZADOR ACUSTICO', 'SINALIZADOR COM', 'SINALIZADOR ELETRONICO', '"SINALIZADOR ""GIROFLEX"";27', '""SINALIZADOR SONORO"', 'SIRENE DEDICADA', 'SIRENE ELETRONICA', 'SIRENE MARCHA', 'SIRENE PIEZOELETRICA', 'SIRENE RE', 'SIRENE TWEETER', 'SIRENE VOZ', 'SOBRE COLUNA', 'SOBRE TAMPA', 'SOLENOIDE DE', 'SOLENOIDE VALVULA', 'SOQUETE 1', 'SOQUETE 2', 'SOQUETE 3', 'SOQUETE CAPO', 'SOQUETE CHICOTE', 'SOQUETE CV', 'SOQUETE DE', 'SOQUETE ENCAIXE', 'SOQUETE FAROL', 'SOQUETE LANTERNA', 'SOQUETE LANTERNAS', 'SOQUETE LUZ', 'SOQUETE PAINEL', 'SOQUETE PISCA', 'SOQUETE TORX', 'SPRAY DE', 'SUPER COLA', 'SUPER EPOXI', 'TAMPA ALOJAMENTO', 'TAMPA BOIA', 'TAMPA CAIXA', 'TAMPA CAPA', 'TAMPA CEGA', 'TAMPA COBERTURA', 'TAMPA CONEXAO', 'TAMPA COROTE', 'TAMPA CUICA', 'TAMPA DA', 'TAMPA DE', 'TAMPA DIFERENCIAL', 'TAMPA DISTRIBUICAO', 'TAMPA DO', 'TAMPA DOBRADICA', 'TAMPA DOS', 'TAMPA DUTO', 'TAMPA ELEMENTO', 'TAMPA ENTRADA', 'TAMPA ESTRIBO', 'TAMPA EXTERNA', 'TAMPA FAROL', 'TAMPA FILTRO', 'TAMPA FRONTAL', 'TAMPA GRADE', 'TAMPA INFERIOR', 'TAMPA INSPECAO', 'TAMPA INSTRUMENTOS', 'TAMPA LACRE', 'TAMPA LATERAL', 'TAMPA LIMPADOR', 'TAMPA MAGNETICO', 'TAMPAO AUXILIAR', 'TAMPA OLEO', 'TAMPAO P/', 'TAMPAO P/INTERLIGACAO', 'TAMPAO TERMOSTATO', 'TAMPA PAINEL', 'TAMPA PARA-CHOQUE', 'TAMPA PIVO', 'TAMPA PLASTICA', 'TAMPA PORTA', 'TAMPA PROTECAO', 'TAMPA PROTETORA', 'TAMPA RADIADOR', 'TAMPA RESERVAORIO', 'TAMPA RESERVATORIO', 'TAMPA RESERVVATORIO', 'TAMPA SUPERIOR', 'TAMPA SUPORTE', 'TAMPA TANQUE', 'TAMPA TETO', 'TAMPA TRANSFERENCIA', 'TAMPA TRASEIRA', 'TAMPA VALVULA', 'TAMPA VOLANTE', 'TAMPA VOLANTE/BUZINA', 'TAMPINHA BOLA', 'TAMPINHA VALVULA']])
                                                        		//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['TRENA 5M', 'TRIANGULO SEGURANCA', 'TRINCO CORREDICO', 'TRINCO DA', 'TRINCO INFERIOR', 'TRINCO JANELA', 'TRINCO QUEBRA-VENTO', 'TUCHO CINTA', 'TUCHO DO', 'VALVULA APU', 'VALVULA BUZINA', 'VALVULA CABECOTE', 'VALVULA DA', 'VALVULA DE', 'VALVULA DECARGA', 'VALVULA DESCARGA', 'VALVULA REDUZIDA', 'VALVULA REGULADORA', 'VALVULA-RELE RE', 'VALVULA SOLENOIDE', 'VALVULA SUSPENSAO', 'VALVULA TOP', 'VALVULA TROCA', 'VASELINA SPRAY', 'VEDACAO INTERNA', 'VEDACAO P/TAMPA', 'VEDADOR RADIADOR', 'VEDA JUNTAS', 'VEDA ROSCAS', 'VERNIZ AEROSOL', 'VIVA VOZ']])
                                                        		//->andWhere(['=','produto_filial.id',390842])
                                                    		    ->orderBy('id')
                                                                ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {

                    /*if($k < 15313){
                        continue;
                    }*/

                    echo "\n".$k." - ".$produtoFilial->id." - ".$produtoFilial->produto->nome;
                    //continue;

                    $produto_filial_outro = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                    ->andWhere(['=', 'filial_id', 98])
                                                                    ->andWhere(['IS', 'meli_id', NULL])
                                                                    ->one();

        		    if($produto_filial_outro){
            			echo " - produto encontrado";
             			//continue;
        		    }
        		    else{
               			echo " - produto não encontrado";
        		      	continue;
        		    }

                    if($produto_filial_outro->meli_id <> "" and $produto_filial_outro->meli_id <> null){
                        //print_r($produto_filial_outro); die;
                        //echo "\n\n com meli_id \n\n";
                        continue;
                    }

                    echo "\n".$k." - Origem: ".$produtoFilial->id . " - Destino: ".$produto_filial_outro->id. " - ". $produtoFilial->produto->nome;

                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        echo "\n\n sem subcategoria \n\n";
                        continue;
                    }
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        echo "\n\n sem valor \n\n";
                        continue;
                    }

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
                    
                    $page = substr($page,0,5000);

                    $title = Yii::t('app', '{nome} ({code})', ['code' => $produto_filial_outro->produto->codigo_global,'nome' => $produto_filial_outro->produto->nome]);

                    $preco = round($produtoFilial->getValorMercadoLivre(), 2);

                    $condicao = ($produtoFilial->produto->e_usado)? "used" : "new";

                    $body = [
                        "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                        "category_id" => utf8_encode($subcategoriaMeli),//MLB251640"),//MLB191833"),//MLB212930"),//MLB191833"),//MLB46694"),//MLB251640"),//MLB431130"),//MLB115922"),//MLB191714"),//MLB191727"),//MLB22645"),//MLB116445"),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode($preco),
                        "available_quantity" => utf8_encode($produtoFilial->quantidade),
                        "seller_custom_field" => utf8_encode($produtoFilial->id),
                        "condition" => $condicao,
                        "description" => ["plain_text" => $page],
                        "pictures" => $produtoFilial->produto->getUrlImagesML(),
                        "shipping" => [
                            "mode" => "me2",
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
            		    "sale_terms" =>   [
                            [    "id" => "WARRANTY_TYPE",
                                 "value_id" => "2230280"
                            ],
                            [    "id" => "WARRANTY_TIME",
                                 "value_name" => "3 meses"
                            ]
                        ]
                    ];
                    //print_r($body);
                    //continue;

                    $response_outro = $meli->post("items?access_token=" . $meliAccessToken_outro,$body);
                    //print_r($response_outro);
                    if ($response_outro['httpCode'] >= 300) {
            			print_r($response_outro);
            			print_r($body);
                        echo " - Não Publicado \n";
            			//die;
                    }
                    else {
                        $produto_filial_outro->meli_id = ArrayHelper::getValue($response_outro, 'body.id');
                        echo " - ";print_r(ArrayHelper::getValue($response_outro, 'body.permalink'));
                        if (!$produto_filial_outro->save()) {
                            echo " - Meli_ID não salvo";
                        }
                        //print_r($response_outro);
                        //print_r($body);
                        //die;
                        echo "\n";
                    }
                }
            }
        }
    }
}


