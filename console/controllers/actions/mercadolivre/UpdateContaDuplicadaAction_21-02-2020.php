<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateContaDuplicadaAction extends Action
{
    public function run()
    {
	$meli = new Meli(static::APP_ID, static::SECRET_KEY);

    	$filials = Filial::find()
            //->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [43]])
	    ->andWhere(['id' => [98]])
            //->andWhere(['<>','id', 92])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            //continue;

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
	    //$user = $meli->refreshAccessToken("TG-5cb495b0eefc400006279a24-390464083");
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

       		$meliAccessToken = $response->access_token;
		//print_r($meliAccessToken);
                $produtoFilials = $filial->getProdutoFilials()
					//->joinWith('produto')
					//->andWhere(['like','upper(produto.nome)','DEFLETOR'])
					//->andWhere(['like','upper(produto.nome)','TUBO'])
                                        ->andWhere(['is not','meli_id',null])
                                        ->andWhere(['is not','produto_filial_origem_id',null])
					//->andWhere(['=','id',123]])
                                        //->andWhere(['meli_id' => ["MLB1325907259","MLB1325894160"]])
                    		        //->andWhere(['=','produto_filial.meli_id','MLB1326646517'])
                                        //->andWhere(['produto_filial.id' => [148210,148423,148660,148682,156130,156142,156151,156153,156166,156223,156235,156238,156239,156264,156268,156272,156299,156346,156398,156399,156405,156408,156456,156463,156523,156533,156611,156630,156631,156636,156658,156661,156680,156693,156704,156721,156722,156727,156728,156761,156762,156771,156782,156790,156791,156797,156798,156802,156803,156807,156809,156827,156828,156838,156839,156840,156841,156842,156848,156857,156876,156880,156881,156883,156894,156898,156899,156900,156909,156910,156916,156917,156918,156920,156922,156924,156926,156927,156939,156942,156943,156944,156945,156947,156948,156976,156977,156979,156980,156981,156982,156984,156985,156986,156988,156989,156990,156991,156992,156993,156994,157009,157015,157031,157032,157047,157049,157050,157062,157064,157066,157072,157073,157074,157075,157084,157085,157088,157089,157093,157096,157097,157098,157100,157101,157103,157104,157105,157110,157111,157112,157113,157114,157116,157118,157122,157123,157124,157125,157138,157153,157160,157164,157168,157176,157178,157180,157188,157189,157190,157192,157196,157200,157207,157209,157219,157220,157232,157263,157264,157265,157281,157282,157285,157286,157291,157314,157315,157318,157327,157336,157337,157360,157364,157365,157370,157371,157372,157374,157375,157382,157396,157401,157429,157430,157431,157432,157433,157434,157438,157440,157458,157459,157462,157476,157477,157481,157488,157494,157511,157537,157538,157539,157540,157541,157542,157543,157544,157546,157547,157548,157549,157550,157552,157553,157554,157555,157557,157558,157559,157560,157568,157589,157616,157617,157630,157631,157632,157634,157635,157636,157637,157638,157642,157671,157682,157684,157691,157702,157704,157744,157745,157746,157764,157769,157774,157789,157800,157814,157815,157818,157824,157835,157836,157876,157877,157878,157899,157900,157901,157904,157906,157907,157908,157910,157932,157933,157938,157944,157979,157988,157989,158008,158009,158010,158011,158012,158014,158015,158017,158018,158021,158027,158030,158031,158052,158069,158074,158098,158125,158127,158129,158130,158131,158132,158134,158135,158136,158138,158140,158154,158155,158163,158175,158177,158180,158183,158189,158201,158203,158224,158225,158236,158262,158265,158266,158267,158268,158269,158271,158272,158274,158275,158293,158303,158305,158306,158312,158323,158339,158367,158373,158406,158408,158409,158410,158411,158416,158425,158432,158444,158453,158454,158478,158500,158501,158502,158506,158509,158511,158513,158532,158537,158541,158542,158552,158553,158558,158561,158564,158566,158608,158609,158616,158618,158619,158624,158625,158629,158630,158631,158632,158633,158634,158635,158636,158638,158639,158640,158643,158644,158645,158647,158651,158660,158671,158677,158679,158695,158697,158699,158700,158701,158706,158755,158757,158765,158767,158769,158770,158774,158775,158777,158778,158791,158792,158801,158802,158805,158807,158809,158816,158817,158820,158825,158840,158867,158869,158870,158871,158872,158875,158877,158878,158879,158880,158881,158882,158883,158905,158911,158914,158931,158932,158935,158943,158946,158952,158953,158962,158963,158966,158994,159006,159007,159008,159009,159010,159011,159012,159013,159014,159015,159046,159075,159076,159125,159126,159129,159130,159132,159134,159136,159137,159138,159139,159140,159141,159152,159155,159163,159167,159169,159172,159177,159179,159180,159183,159190,159222,159224,159225,159236,159237,159242,159252,159253,159255,159256,159257,159285,159287,159322,159330,159333,159334,159335,159336,159349,159350,159352,159353,159354,159356,159358,159363,159367,159376,159387,159388,159389,159390,159391,159392,159393,159395,159396,159397,159411,159415,159431,159451,159453,159454,159455,159463,159472,159474,159478,159490,159501,159502,159503,159504,159505,159506,159539,159542,159543,159544,159545,159546,159548,159549,159550,159576,159577,159582,159583,159594,159596,159598,159604,159610,159611,159626,159627,159628,159629,159631,159646,159670,159671,159672,159673,159674,159677,159678,159679,159704,159711,159729,159730,159731,159744,159745,159746,159747,159772,159775,159776,159777,159779,159781,159791,159801,159805,159808,159811,159817,159823,159826,159829,159847,159883,159884,159886,159887,159888,159889,159890,159891,159892,159893,159894,159895,159897,159898,159899,159900,159902,159903,159904,159905,159906,159907,159908,159911,159920,159928,159932,159942,159947,159949,159973,159974,159975,159976,159977,159978,159979,159980,159981,159982,159984,159985,159986,159987,159988,159990,159991,159994,159999,160001,160007,160009,160010,160016,160024,160033,160037,160041,160042,160043,160044,160047,160064,160071,160072,160073,160076,160133,160137,160138,160168,160186,160219,160225,160226,160276,160277,160278,160280,160282,160283,160284,160285,160286,160287,160289,160290,160292,160293,160295,160296,160298,160310,160326,160327,160330,160342,160345,160350,160352,160355,160373,160377,160378,160385,160386,160387,160388,160389,160390,160391,160392,160393,160394,160395,160396,160440,160441,160442,160471,160487,160490,160493,160497,160499,160500,160501,160503,160504,160508,160510,160511,160512,160513,160514,160515,160516,160519,160522,160523,160524,160525,160526,160527,160528,160530,160531,160535,160537,160538,160539,160540,160541,160542,160543,160544,160545,160548,160549,160551,160552,160553,160560,160561,160564,160605,160606,160608,160614,160620,160622,160624,160625,160627,160634,160637,160638,160651,160670,160687,160690,160692,160696,160697,160698,160713,160719,160720,160721,160722,160723,160724,160725,160726,160744,160752,160753,160755,160756,160757,160778,160779,160781,160783,160784,160785,160786,160787,160788,160789,160790,160791,160792,160793,160794,160795,160796,160797,160798,160799,160800,160801,160802,160803,160807,160822,160823,160824,160827,160828,161750,161839,161840,161914,161964,161966,161973,161987,162000,162005,162045,162086,162098,162154,162155,162178,162179,162189,162193,162199,162212,162249,162279,162291,162293,162312,162355,162520,162526,162531,162535,162536,162537,162606,162632,162633,162653,162654,162669,162842,162851,162856,162867,162893,162925,162930,162950,162971,162975,162991,162994,163002,163013,163143,163155,163189,163212,163218,163219,163244,163245,163264,163267,163288,163301,163488,177221,177236,181303,184059]])
					//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => []])
					//->andWhere(["(SELECT foo[1] ||' '|| foo[2] from string_to_array(produto.nome, ' ') as foo)" => ['COLA DE', 'COLA MADEIRA', 'COLA SPRAY', 'COLA TUDO', 'COLA VINIL']])
					//->andWhere(['meli_id' => ['MLB1235261649','MLB864747637','MLB864927013','MLB864928189','MLB864934418','MLB864927833','MLB864927830','MLB864740037','MLB864748980','MLB864931449','MLB1220136648','MLB1220129376','MLB1220136639','MLB1220129379','MLB1220132905','MLB1220129383','MLB1110918012','MLB1106932518','MLB878416191','MLB1106932602','MLB878403242','MLB1221780387','MLB1103768908','MLB878400374','MLB1248321746','MLB1248327404','MLB1248321421','MLB1248323875','MLB1248327610','MLB1248323938','MLB1248321536','MLB1248327689','MLB1248321612','MLB1248321626','MLB1248324070','MLB1248321773','MLB1248324082','MLB1248324093','MLB1248321680','MLB1248321688','MLB1248321693','MLB1248321700','MLB1248321779','MLB1248327854','MLB1248327858','MLB1248321812','MLB1248324243','MLB1248321834','MLB1248327890','MLB1248321854','MLB1248327918','MLB1248321892','MLB1248321935','MLB1248321940','MLB1248327994','MLB1248324352','MLB1248328020','MLB1248321997','MLB1248328035','MLB1248328077','MLB1248322044','MLB1248322047','MLB1248328150','MLB1248322122','MLB1248328199','MLB1248322156','MLB1248322206','MLB1248328366','MLB1248325242','MLB1248322597','MLB1248328494','MLB1248325274','MLB1248328514','MLB1248325307','MLB1248328526','MLB1248325418','MLB1248321192','MLB1248324087','MLB1248327783','MLB1248324187','MLB1248321785','MLB1248324237','MLB1248321904','MLB1248321925','MLB1248321985','MLB1248322008','MLB1248324444','MLB1248324452','MLB1248328142','MLB1248322118','MLB1248322129','MLB1248322132','MLB1248322145','MLB1248324688','MLB1248324784','MLB1248322585','MLB1248325266','MLB1248325395']])
					//->where(' produto_filial_origem_id in (select produto_filial.id from produto_filial where filial_id in (74,92,4,81,86,71)) ')
                                        ->orderBy('produto_filial.id DESC')
                                        ->all();
		$x = 0;
                foreach ($produtoFilials as $k => $produto_filial) {
		    /*if($k<=34786){
			echo $k." - pulou"; continue;
		    }*/

		    //echo "\n".$k." - ".$produto_filial->produto->nome; //continue;

		/*//Update
                        $body = [
                                "available_quantity"    => 0,
                        ];
                        $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);

                        if ($response['httpCode'] >= 300) {
                                echo "ERROR \n";
                        }
			else {
                                echo "ok - ";
                                print_r(ArrayHelper::getValue($response, 'body.permalink'));
                                echo "\n";
                        }
		    continue;*/

		    /*echo " - ".strpos($produto_filial->produto->nome, "ANTENA");
		    if (!strpos($produto_filial->produto->nome, "ANTENA")){
			continue;
		    }
		    else{
			if(strpos($produto_filial->produto->nome, "ANTENA") <> 0){
			    //print_r(strpos($produto_filial->produto->nome, "ANTENA")); 
			    continue;
			}
		    }*/

                    if ($produto_filial->produto_filial_origem_id == NULL){
                        continue;
                    }

                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'id', $produto_filial->produto_filial_origem_id])->one();
		    if (!$produtoFilial){
			continue;
		    }

		    echo "\n".$x." - ".$produtoFilial->id." - ".$produtoFilial->produto_id." - ".$produtoFilial->filial_id." - Produto a ser alterado";
		    $x++;
		    //continue;

		    if ($produtoFilial->filial_id <> 97 ){
                        continue;
                    }

		    if ($produtoFilial->produto->fabricante_id != null) {

   	                echo " - C??pia: ".$produto_filial->id." - ".$produto_filial->meli_id ." - Origem: ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - ";
			//continue;
                        //Aqui come??a o c??digo

			//PRE??O
                    	$preco = round($produtoFilial->getValorMercadoLivre(), 2);
                    	/*if ($preco > 500){
				$preco = round(($preco * 1.065), 2);
			}
			else{
				//continue;
				$preco = round(($preco * 0.95), 2);
			}*/
			/*if ($preco>120){
				continue;
			}*/
			//PRE??O

			/*//Update DESCRI????O
			$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
			$body = ['plain_text' => $page];
                        $response = $meli->put("items/{$produto_filial->meli_id}/description?access_token=" . $meliAccessToken, $body, []);
			print_r($response);*/

			$title = Yii::t('app', '{nome} ({code})', [
	                    'code' => $produtoFilial->produto->codigo_global,
	                    'nome' => $produtoFilial->produto->nome
	                ]);

			$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);

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

			//Update
			$body = [
				//"description" => ["plain_text" => utf8_encode($page)],
				//"title" 		=> utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
				//"pictures" 		=> $produtoFilial->produto->getUrlImagesML(),
				//"category_id"		=> utf8_encode("MLB191833"),//"MLB251640"),//MLB109308"),
				"available_quantity" 	=> $produtoFilial->quantidade,
				"price" 		=> $preco,
				//"pictures" 		=> $produtoFilial->produto->getUrlImagesML(),
				"shipping" => [
                                    "mode" => $modo,
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                        ];
	                $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, []);

	                if ($response['httpCode'] >= 300) {
				echo "ERROR \n";
				print_r($response);
        		}
        		else {
            			echo "ok - ";
            			//print_r($response);
				print_r(ArrayHelper::getValue($response, 'body.permalink'));
				echo "\n";
	                }

			//Update Descri????o
                        /*$body = ['plain_text' => $page];
                        $response = $meli->put("items/{$produto_filial->meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
			if ($response['httpCode'] >= 300) {
                                echo "ERROR \n";
                                //print_r($response);
                        }
                        else {
                                echo "ok ";
                                //print_r($response);
                                //print_r(ArrayHelper::getValue($response, 'body.permalink'));
                                echo "\n";
                        }*/

			//Update
/*                        $body = [
                                "id" => "gold_pro",
                        ];
                        $response = $meli->post("items/{$produto_filial->meli_id}/listing_type?access_token=" . $meliAccessToken, $body, []);

                        if ($response['httpCode'] >= 300) {
                                echo "ERROR \n";
                                print_r($response);
				print_r($meliAccessToken);
                        }
                        else {
                                echo "ok - ";
				print_r($response);
                                print_r(ArrayHelper::getValue($response, 'body.permalink'));
                                echo "\n";
				print_r($meliAccessToken);
                                die;
                        }
*/

			//Update Descri????o
			/*$body = ['plain_text' => $page];
                        $response = $meli->put("items/{$produto_filial->meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                                echo "ERROR";
				print_r($response);
                        }
                        else {
                                echo "ok";
                        }*/

                        //Aqui termina o c??digo
                    }
                }
            }
            echo "Fim da filial: " . $filial->nome . "\n";
        }
    }
}

