<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;


class CriaProdutoDibTodosAction extends Action
{
    public function run()
    {

        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";

        $produtos = Produto::find() ->andWhere(['=', 'fabricante_id', 123])
                                    //->andWhere(['=', 'codigo_global', '446786'])
				    ->andWhere(['id' => [246613,246614,246618,246620,246625,246632,246641,246650,246656,246678,246680,246682,246687,246690,246710,246711,246722,246725,246727,246755,246763,246767,246790,246791,246795,246796,246798,246799,246800,246801,246802,246803,246808,246812,246932,246976,246990,247008,247012,247016,247020,247024,247028,247033,247037,247050,247052,247056,247060,247062,247063,247064,247065,247106,247178,247182,247215,247216,247243,247338,247374,247375,247387,247406,247423,247459,247467,247537,247539,247540,247559,247564,247567,247572,247575,247578,247582,247583,247584,247585,247586,247587,247588,247593,247595,247600,247603,247604,247606,247608,247610,247615,247619,247623,247625,247627,247629,247635,247683,247684,247685,247690,247691,247697,247698,247700,247721,247733,247781,247782,247841,247857,247915,247916,247937,247994,248014,248056,248081,248127,248128,248147,248182,248208,248289,248290,248291,248292,248324,248364,248365,248366,248373,248374,248516,248517,248589,248601,248606,248638,248654,248739,248740,248763,248881,248882,248883,248926,248939,248958,248975,248981,249027,249037,249096,249148,249201,249270,249271,249272,249273,249274,249385,249451,249475,249488,249491,249569,249593,249651,249652,249658,249659,249685,249714,249769,249798,249801,249815,249867,249868,249938,249974,249981,249985,249986,250001,250015,250029,250081,250102,250162,250168,250174,250314,250315,250342,250348,250374,250382,250512,250516,250602,250616,250641,250642,250715,250730,250766,250785,250787,250804,250809,250810,250816,250817,250956,250957,250968,250978,251016,251017,251073,251096,251102,251118,251162,251190,251191,251192,251193,251194,251195,251196,251197,251198,251262,251299,251300,251311,251320,251327,251358,251359,251364,251375,251376,251377,251378,251379,251380,251381,251382,251383,251388,251393,251415,251422,251438,251461,251462,251464,251465,251532,251533,251534,251535,251536,251537,251538,251539,251541,251542,251644,251645,251678,251790,251791,251792,251793,251795,251798,251809,251823,251865,251873,251874,251875,251879,251880,251903,251904,251925,251926,251927,251928,251933,252026,252027,252034,252038,252075,252076,252091,252092,252093,252094,252098,252128,252129,252130,252131,252136,252137,252138,252139,252140,252141,252142,252143,252144,252145,252146,252147,252148,252149,252150,252151,252156,252161,252168,252170,252182,252211,252234,252247,252251,252254,252255,252333,252409,252410,252454,252455,252465,252467,252685,252689,252718,252722,252830,252831,252832,252871,252873,252874,252875,252876,252885,252909,252910,252922,252973,253206,253214,253218,253219,253220,253221,253307,253315,253381,253431,253432,253441,253445,253469,253470,253553,253555,253621,253622,253671,253705,253734,253750,253881,253900,253902,253903,253904,253905,253906,253907,253908,253909,253910,253914,253916,253981,254260,254274,254275,254276,254277,254278,254385,254386,254387,254396,254418,254419,254422,254427,254504,254569,254570,254571,254572,254606,254607,254608,254654,254655,254660,254729,254839,254840,254849,254877,254878,254917,254996,255024,255025,255040,255069,255080,255267,255396,255397,255405,255424,255425,255426,255427,255438,255522,255523,255531,255636,255640,255641,255645,255646,255647,255648,255649,255762,255763,255770,255811,255967,255982,256030,256031,256105,256168,256169,256170,256171,256193,256194,256195,256206,256227,256237,256253,256311,256312,256380,256381,256382,256383,256384,256400,256401,256402,256420,256421,256426,256435,256436,256451,256452,256513,256514,256523,256524,256597,256601,256626,256627,256629,256655,256656,256769,256802,256823,256864,256865,256879,256882,256888,256890,256968,256987,257011,257012,257029,257030,257031,257032,257033,257074,257144,257188,257190,257191,257212,257241,257285,257316,257341,257402,257435,257437,257438,257439,257443,257555,257556,257557,257558,257580,257593,257715,257729,257903,257904,257905,257906,257907,257908,257909,257910,257911,257916,257941,257942,257977,258075,258113,258122,258123,258124,258125,258186,258189,258272,258273,258280,258281,258454,258455,258456,258457,258717,258747,258748,258749,258751,258754,258755,258762,258763,258765,258779,258783,258806,258833,258858,258859,258864,258928,258933,258965,258978,258980,258983,259093,259243,259253,259254,259344,259346,259347,259372,259373,259374,259375,259399,259545,259573,259574,259575,259578,259581,259582,259670,259682,259747,259764,259800,259801,259802,259829,259830,259831,259832,259846,259847,259883,259889,259890,259891,259892,260028,260064,260093,260122,260156,260157,260202,260302,260303,260304,260305,260306,260307,260308,260309,260408,260409,260451,260456,260457,260470,260471,260490,260527,260676,260677,260819,260820,260821,260822,260823,260824,260825,260826,260893,260959,261047,261048,261105,261106,261112,261113,261114,261146,261147,261148,261149,261249,261253,261338,261392,261393,261398,261424,261492,261493,261494,261495,261496,261497,261498,261548,261585,261586,261668,261720,261763,261804,261828,261896,261943,261945,261969,261999,262000,262058,262126,262127,262222,262228,262234,262235,262243,262284,262291,262305,262308,262310,262322,262401,262442,262443,262478,262580,262615,262616,262668,262708,262729,262730,262731,262732,262794,262812,262862,262863,262887,262911,262933,262934,262935,262967,262977,263033,263034,263063,263104,263105,263124,263223,263224,263302,263309,263325,263334,263442,263525,263590,263609,263655,263718,263796,263815,263844,263859,263872,263893,263894,263913,263932,263938,263961,263962,263967,263970,263971,263973,264000,264006,264007,264011,264090,264119,264257,264414,264426,264434,264485,264506,264542,264575,264595,264664,264665,264666,264710,264719,264788,264809,264955,265025,265082,265083,265200,265232,265263,265264,265348,265455,265491,265492,265511,265575,265577,265578,265631,265719,265720,265722,265725,265726,265751,265777,265785,265820,265836,265876,265885,265892,265897,265901,265909,265910,265941,265954,265964,265965,265989,266014,266023,266073,266074,266075,266076,266101,266136,266173,266174,266185,266200,266223,266231,266322,266425,266557,266595,266610,266635,266661,266669,266670,266671,266736,266738,266739,266790,266791,266804,266831,266832,266861,266898,266899,266902,266903,266904,266905,266954,266967,266989,266991,267033,267040,267046,267102,267177,267218,267288,267302,267406,267409,267414,267451,267452,267453,267454,267461,267463,267464,267505,267506,267581,267592,267605,267644,267673,267684,267686,267695,267696,267697,267698,267729,267731,267732,267736,267740,267741,267755,267756,267768,267769,267773,267816,267817,267827,267858,267892,267908,267921,267925,267939,267940,267954,267973,267977,267979,268002,268025,268046,268092,268139,268151,268156,268165,268182,268226,268305,268373,268374,268375,268376,268408,268435,268540,268541,268583,268663,268698,268699,268710,268711,268712,268736,268772,268793,268799,268843,268868,268911,268951,268959,268960,268961,268962,269002,269006,269007,269013,269019,269024,269025,269027,269040,269041,269087,269144,269186,269209,269220,269230,269240,269251,269268,269301,269302,269303,269304,269308,269327,269384,269450,269451,269466,269489,269505,269506,269512,269573,269574,269634,269658,269665,269675,269740,269914,269999,270028,270045,270088,270140,270144,270177,270182,270219,270220,270258,270259,270311,270353,270375,270383,270426,270427,270430,270466,270467,270487,270497,270522,270608,270612,270624,270638,270639,270640,270652,270653,270675,270683,270692,270709,270752,270753,270769,270799,270809,270812,270880,270911,270912,270913,270941,270944,270945,270972,270989,270995,270996,271007,271123,271128,271408,271409,271410,271411,271412,271448,271449,271450]])
                                    ->addOrderBy('id')
                                    ->all();

        if (file_exists("/var/tmp/log_omie_cria_produtos_dib_todos.csv")){
            unlink("/var/tmp/log_omie_cria_produtos_dib_todos.csv");
        }

        $arquivo_nome = "/var/tmp/log_omie_cria_produtos_dib_todos_".date("Y-m-d_H-i-s").".csv";
        $arquivo_log = fopen($arquivo_nome, "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");

        foreach ($produtos as $k => $produto) {

            echo "\n".$k." - ".$produto->id." - ".$produto->nome;
            //continue;

            if (substr($produto->codigo_global,0,3) != 'CX.'){

                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());

                $descricao = str_replace('"',"''",substr($produto->nome." (".$produto->codigo_global.")",0,100));
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                            "codigo_produto_integracao" => $produto->codigo_global,
                            "codigo"                    => $produto->codigo_global,
                            "descricao"                 => $descricao,//substr($produto->nome." (".$produto->codigo_global.")",0,100),
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
                //print_r($body);
                $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                //print_r($response); echo "<br><br><br>"; //die;

                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - OK";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";Ok\n");
                }else{
                    echo " - Erro";
                    //print_r($response);
		    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";Error\n");

                    /*$body = [
                        "call" => "ConsultarProduto",
                        "app_key" => '468080198586',
                        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                        "param" => [
                            "codigo_produto"            => $produto->codigo_global,
                            "codigo_produto_integracao" => $produto->codigo_global,
                            "codigo"                    => $produto->codigo_global,
                        ]
                    ];
                    $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
                    echo "\n"; print_r($response); echo "\n";

                    if (ArrayHelper::getValue($response, 'body.codigo_produto_integracao') == null){
                        $body = [
                            "call" => "AlterarProduto",
                            "app_key" => '468080198586',
                            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                            "param" => [
                                "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                                "codigo_produto_integracao" => $produto->codigo_global,
                                "codigo"                    => ArrayHelper::getValue($response, 'body.codigo'),
                            ]
                        ];
                        $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
                        echo "\n"; print_r($response); echo "\n";

                        if (ArrayHelper::getValue($response, 'httpCode') == 200){
                            fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                        } else {
                            fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                        }
                    } */
                }
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);
    }
}