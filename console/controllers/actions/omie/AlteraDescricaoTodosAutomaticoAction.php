<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_decode;


class AlteraDescricaoTodosAction extends Action
{
    public function run()//$id)
    {
       
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";
        
        $produtos = Produto::find() ->andWhere(['=','id',231375])
				    //->andWhere(['id' => [1001,10052,10054,10056,10057,10058,10059,10060,10068,10074,10093,10094,10101,10109,10139,10178,10179,10188,10191,10207,10208,10209,10210,10211,10212,10213,10214,10215,10216,10217,10218,10269,10281,10282,10283,10284,10285,10287,10288,10289,10290,10291,10292,10293,10294,10295,10299,10300,10302,10303,10309,10319,10330,10338,10339,10342,10343,10345,10381,10387,10420,10421,10422,10424,10425,10437,10439,10440,10441,10442,10453,10454,10455,10456,10457,10505,10541,10542,10585,10658,10659,10660,10662,10667,10668,10669,10670,10671,10672,10673,10674,10675,10676,10677,10678,10679,10680,10681,10682,10683,10684,10685,10686,10687,10688,10689,10690,10691,10692,10693,10694,10696,10697,10698,10699,10700,10701,10702,10703,10704,10705,10706,10707,10708,10709,10710,10711,10712,10713,10714,10715,10716,10717,10718,10719,10720,10721,10722,10723,10724,10725,10726,10727,10728,10729,10730,10731,10732,10733,10734,10735,10736,10737,10738,10739,10740,10741,10742,10743,10745,10746,10747,10748,10750,10751,10752,10753,10754,10755,10756,10757,10758,10759,10760,10761,10762,10763,10764,10765,10766,10767,10768,10769,10770,10771,10772,10773,10774,10775,10776,10777,10778,10779,10780,10781,10782,10783,10784,10785,10786,10787,10788,10789,10790,10791,10792,10793,10794,10795,10796,10797,10798,10799,10800,10801,10802,10803,10804,10805,10806,10807,10808,10809,10810,10811,10815,10832,10833,10834,10835,10836,10837,10838,10839,10840,10841,10842,10843,10852,10853,10854,10882,10883,10884,10885,10886,10887,10888,10889,10890,10891,10892,10893,10894,10895,10896,10999,11015,11038,11072,11074,11076,11079,11080,11087,11111,11378,11564,11565,11572,11573,11580,11581,11700,11941,12039,12040,12245,12246,12248,12258,12265,12266,12271,12273,12274,12279,12287,12300,12301,12310,12322,12351,12355,12364,12371,12410,12411,12453,12455,12466,12467,12469,12481,12489,12515,12528,12538,12540,12550,12568,12580,12592,12605,12610,12611,12615,12619,12642,12649,12653,12654,12655,12673,12679,12688,12691,12699,12713,12714,12715,12721,12722,12729,12731,13018,13113,13114,13458,13559,13627,13763,14364,14414,14587,14601,14622,14662,14697,14746,14770,14771,14785,14810,14855,14857,14860,14871,14873,14879,14915,14916,14920,14934,14935,14936,14937,14949,14970,14971,14999,15000,15004,15008,15054,15062,15063,15074,15117,15122,15160,15189,15190,15249,15251,15255,15283,15383,15395,15419,15421,15427,15514,15515,15522,15525,15527,15632,15675,16157,16265,16546,21830,21831,21832,21833,21834,21835,21836,21837,21838,21839,21840,21841,21842,21843,21844,21845,21846,21847,222229,222232,222236,222243,222502,222504,222505,222506,222510,222512,222513,222514,222516,222522,226051,226393,226394,226395,226396,226397,226398,226399,226400,226401,226402,226403,226404,226405,226406,226408,226409,226410,226411,226412,226413,226414,226415,226416,226417,226418,226419,226420,226421,226422,226423,226425,226426,226427,226428,226436,226448,226450,226549,226593,226594,226595,226655,226656,226661,226671,226785,226788,226841,226843,226844,226859,226961,226980,227177,227202,227279,227281,227328,227340,227343,227345,227346,227387,227394,227412,227464,227465,227479,227481,227482,227483,227499,227503,227504,227505,227506,227509,227514,227538,227612,227649,227674,227742,227746,227769,227896,227986,228102,228179,228204,228206,228245,228246,228250,228308,228380,228389,228471,228473,228553,228567,228620,228628,228629,228660,228705,228743,228757,228758,228766,228767,228786,228789,228817,228818,228895,228947,229033,229098,229265,229325,229375,229515,229517,229518,229521,229522,229525,229528,229529,229533,229536,229542,229931,229964,229999,230168,230236,230238,230303,230346,230349,230396,230670,230755,230770,230798,230960,230976,231018,231063,231068,231140,231143,231159,231229,231230,231237,231254,231375,231376,231377,231393,231394,231401,231406,231458,231500,231504,231553,231583,231606,231646,231690,231693,231694,231695,231724,231752,231772,231785,231788,231829,231831,231839,231842,231843,231959,232440,232441,232443,232444,232446,232447,232480,232585,232690,233142,233188,233193,233194,233204,233208,233210,233239,233257,233258,234256,234257,234261,234262,234343,234366,234367,234512,234814,234905,235005,235082,235199,235260,235383,235599,235606,235607,235608,235916,235953,236010,236039,236072,236078,236079,236083,236106,236112,236130,236137,237324,238036,238047,238117,238683,239750,239756,239989,240007,240008,240032,240057,241964,241965,241966,241967,241968,241969,241970,241971,241972,241973,241974,243083,243085,243341,243522,243808,243812,243928,243947,243969,243986,245298,245299,245500,245505,245532,245537,245681,245687,245716,245910,245934,245989,245994,246072,246138,246201,246518,246523,246525,246611,246613,246614,246801,250513,250514,251358,251359,252368,255077,255078,255471,256072,256349,256919,256956,257069,257070,257691,258036,258765,260466,260659,260660,260893,261067,261112,261177,261584,261668,262066,262067,262323,262339,262340,262635,262636,262637,262683,262684,262928,263032,263273,263274,263302,263598,263599,263903,264153,264792,264793,265193,265194,265541,265617,265817,266174,266184,266954,267695,267696,267836,268799,269024,269505,270045,270652,270911,270912,271076,271078,271472,271488,271490,271492,271504,271656,27246,272478,272479,272482,272483,272484,272485,272486,272487,272490,272491,272492,272493,272494,272495,272496,272497,272498,272499,272500,273307,273316,273317,273318,273319,273320,273321,273322,273323,273329,273330,273333,273349,273399,273415,273639,273685,273721,273826,273837,273869,273921,273923,274199,274256,274461,274527,274589,274678,274688,274721,274789,274803,274829,274952,27498,275060,275381,27539,27540,275493,275713,275856,275961,275962,27622,27626,276275,27629,27661,276683,276791,277117,277123,277204,277474,277637,277906,278091,278187,278211,278238,278303,27838,27839,278613,27862,27866,278760,278823,278930,279288,279654,280114,280121,280162,280405,280459,280618,280916,280936,280987,281175,281188,281193,281258,281259,281261,282092,282452,282513,28312,28320,283378,283379,283380,283381,28340,28346,28349,28351,28385,284035,284063,284100,284198,284273,284301,28437,284409,284410,28457,284789,284865,28495,285026,285027,285075,285102,285139,28514,285140,285141,285160,285198,28521,28556,285715,28579,285797,285944,28602,286041,286092,286152,286154,286275,286674,286757,28684,287014,287047,28711,28726,28737,28747,287585,287633,287708,287861,287921,28793,287936,28795,288115,288609,288712,28884,28885,28888,288893,289068,289069,289196,289227,289235,289267,289282,289331,289564,289602,289772,289782,289817,289856,28986,28987,289994,290057,29015,29025,290285,290286,290582,290637,290734,29093,291006,291076,29116,291295,291328,291392,291531,291627,291692,291770,291819,29183,292020,292126,292132,292484,292494,292566,292571,292750,292773,292974,292982,293201,293436,293527,293653,293741,293784,293803,293804,294084,294099,294201,294251,294261,294315,294388,294784,294840,294885,294903,294941,294952,294976,295204,295320,295352,295388,295534,295541,295548,295586,295825,295880,295976,296016,296027,296277,296371,296419,296627,296727,296821,297023,297133,297180,297255,297265,297409,297485,297609,297634,297646,297663,297675,297715,298327,298366,298450,298773,298797,298894,299065,299093,299303,299454,299496,299505,299515,299564,299590,299773,30008,300271,300327,300662,300724,30099,30102,301329,301336,301386,301392,301663,301994,30207,302187,302536,302555,302602,302664,302752,302995,303109,303155,303227,303273,303294,303408,30345,303453,303513,303546,303588,303612,303801,303929,303936,304585,304665,304670,30471,304808,304853,30495,304957,30501,305293,305402,30542,305586,305587,305623,30565,30570,305734,305817,306230,306234,306270,306299,306371,30649,30651,306620,306643,306757,30676,306807,306837,306860,306874,306878,306884,306974,30715,307438,307451,307467,307490,307502,30751,307519,307568,307581,307600,307631,307653,307683,307937,307985,30804,308316,308357,308375,30846,308586,308604,308648,308726,308761,308777,308799,30880,308847,308865,308879,30892,309207,309228,309278,309286,309300,309314,309544,30956,309598,30967,309708,309750,310033,310215,31030,310625,310678,310696,310709,310738,310754,310898,310905,310934,311038,311096,311104,311116,311117,311118,311119,311120,311121,311124,31142,31143,31166,31175,31313,31477,31483,31559,31579,31640,31641,31644,31658,31667,31700,31703,31717,31718,31719,31720,31721,31722,31723,31734,31807,31863,31869,31924,31925,31991,32056,32071,32130,32131,32149,32151,32176,32187,32258,32287,32401,32488,32493,32494,32511,32512,32513,32663,32672,32683,32783,32800,32801,32881,32883,32885,32886,32887,32888,32929,32941,32942,32956,32970,33004,33025,33095,33186,33188,33224,33237,33238,33276,33278,33294,33310,33311,33312,33313,33314,33315,33508,33563,33603,33604,33622,33653,33689,33714,33735,33763,33766,33768,33770,33786,33803,33822,33854,33878,33997,34040,34113,34127,34244,34246,34247,36926,36955,37019,37026,37194,37198,37239,37247,37266,37275,37279,37297,37310,37334,37359,37424,37429,37439,37461,37552,37595,37625,37712,37717,37727,37748,37765,37793,37847,37959,37971,38000,38003,38017,38067,38086,38151,38209,38239,38249,38334,38344,38355,38360,38379,38442,38645,38774,38874,39048,39070,3955,40311,40333,40427,40445,40572,40647,40665,40673,40702,40703,40723,40803,40810,40850,40895,40940,40943,41029,41060,41064,41094,41125,41211,41267,41390,41426,41501,41517,41543,41554,41573,41596,41610,41614,41621,41631,41651,41694,41718,41755,41769,41803,41924,41945,41987,42026,42032,42035,42124,42146,42165,42180,42181,42188,42209,42269,42345,42351,42398,42414,42415,42419,42457,42499,42500,42506,42571,42594,42620,42712,42750,42767,42770,42773,42795,42796,42797,42798,42826,42827,42828,42846,42847,42848,42849,42850,42851,42852,42853,42854,42855,42856,42903,42949,42950,42961,42968,42974,42978,42979,42980,42981,42982,42983,42984,42985,42986,42987,42988,42989,42990,42991,42992,42994,42995,42996,42997,43000,43001,43002,43003,43004,43005,43006,43007,43008,43009,43010,43017,43042,43050,43079,43110,43314,43370,43383,43427,43488,43619,43631,43671,43686,43692,43746,43782,43786,43824,43825,43901,43903,43906,43912,43918,43919,43944,43953,44023,44036,44038,44093,44094,44116,44161,44278,44279,44283,44284,44330,44348,44432,44441,44485,44494,44499,44534,44544,44559,45358,45389,45462,45507,45515,45519,45571,45665,45715,45745,45773,45780,45802,45888,45979,47726,47740,47750,47785,48942,49077,49134,5,52353,52728,55855,55874,55875,55884,55887,55888,55904,55945,55976,55993,55996,56013,56029,56049,56073,56080,56090,56093,56094,56095,56141,56149,56196,56232,56233,56245,56272,56311,56344,56424,56432,56434,56435,56468,56510,56532,56543,56562,56567,56900,57355,57941,57987,6,6413,6428,6437,6438,6439,6440,6441,6442,6443,6447,6448,6449,6450,6451,6452,6453,6455,6456,6457,6458,6459,6461,6462,6463,6464,6465,6485,6487,6489,6541,6542,6654,6655,6656,6663,6664,6665,6666,6693,6694,6940,6945,6946,7017,7026,7027,7089,7091,7113,7114,7154,7155,7156,7193,7194,7245,7301,7310,7311,7401,7402,7433,7434,7435,7436,7450,7463,7464,7469,7625,7696,7697,7707,7741,7837,7888,7897,7943,7950,8468,8557,8586,8603,8604,8673,8811,8816,8887,8914,8975,9190,9282,9307,9311,9314,9354,9355,9372,9378,9385,9386,9387,9388,9391,9411,9418,9461,9476,9499,9581,9589,9693,9905,9925,9942,9950,9971,9983,237382,271974,272069,292735,294881,302659,306828,307374]])
                                    ->all();
        
        $arquivo_log = fopen("/var/tmp/log_omie_altera_produto_todos_automatico_".date("Y-m-d_H-i-s").".csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");
 
        foreach ($produtos as $k => $produto) {
            
            echo "\n".$k." - "; print_r($produto->id);
	    //continue; 
            //echo "Alterando produtos...\n\n";
            $body = [
                "call" => "ConsultarProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                ]
            ];
            $response = $criar_omie->consulta_produto("api/v1/geral/produtos/?JSON=",$body);
            //echo "\n"; print_r($response); echo "\n"; //die;
            //var_dump(ArrayHelper::getValue($response, 'body.codigo_produto_integracao'));echo "\n";
            
            $ncm = ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora, 0, 4).".".substr($produto->codigo_montadora, 4, 2).".".substr($produto->codigo_montadora, 6, 2));
            
            if (ArrayHelper::getValue($response, 'httpCode') == 200){
                //echo "\n\n 0000000\n\n";

                $body = [
                    "call" => "AlterarProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                        "codigo_produto"            => ArrayHelper::getValue($response, 'body.codigo_produto'),
                        "descricao"                 => substr("(".$produto->codigo_global.") ".$produto->nome, 0, 120),
                        "ncm"                       => $ncm,
                    ]
                ];
                $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
		//print_r($response);

                if (ArrayHelper::getValue($response, 'httpCode') == 200){
		    echo " - OK";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";"."Produto Alterado com Sucesso!"."\n");
                } else {
		    echo " - Error";
		    print_r($response);
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                }
            } else{
                echo "\n";
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
    }
}




