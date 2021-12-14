<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Imagens;
use common\models\ProdutoFilial;

class VerificarExisteImagemAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de verificação das imagens BR: \n\n";
        
        if (file_exists("/var/tmp/log_verificar_imagens_br.csv")){
            unlink("/var/tmp/log_verificar_imagens_br.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_verificar_imagens_br.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_id;codigo_fabricante;status\n");
        
        $produto_filiais = ProdutoFilial::find()->andWhere(['=','filial_id',72])->andwhere(['produto_id'=> [228414,228433,229301,228825,228342,228289,230900,230762,229752,230895,228812,228813,228817,228849,228850,228851,228629,228852,228853,228854,228858,227963,228861,228862,228066,228864,228865,228866,228867,228868,228869,228871,228872,228054,30469,230726,228411,230735,228486,230996,228517,230759,230764,229216,229218,230776,230832,230262,230921,230872,228190,227790,47335,228877,229681,228102,228814,230767,227641,228856,228857,41869,42635,228874,228875,43345,37417,38461,44758,44760,42529,42281,42524,58025,43234,57795,57770,57836,57032,43274,58029,43231,43320,43313,43290,42793,42350,42923,45398,42750,43911,30834,30767,45354,46522,58016,41407,58017,56207,56204,56210,56261,56277,56322,56355,56349,56233,56367,56226,56244,56296,56276,56334,56336,56302,56228,56313,56420,56441,56475,56531,56402,56404,56415,56519,56546,56473,56510,56555,56517,56537,56456,56533,56459,56556,56560,55858,56583,55865,55851,55891,55870,56557,56581,55892,55918,56018,56026,56020,56035,55904,56027,55957,55980,55934,55908,55897,55913,55940,56042,56011,55985,56070,56178,56120,56177,56162,56148,56147,56180,56055,56171,56095,56154,56080,56137,56184,56022,56393,56410,56337,56201,56372,56218,56232,56236,41750,38333,38345,33062,43953,30722,12950,28369,28605,38419,38319,34400,33813,33132,37359,33144,33147,33158,33656,33179,33134,36910,36966,37223,35767,38314,38325,38327,38340,38336,38381,38273,36570,38339,38465,38463,38471,38441,37079,38401,38417,38429,38428,38323,38343,38414,38422,38392,38486,38479,38446,38395,37479,36711,41534,41533,41597,41585,41847,41681,41337,40333,40700,41685,41270,47459,47297,47906,46988,47894,47900,47848,46175,46446,46475,46202,46017,47830,46061,47769,41633,45743,45855,41243,47760,47354,48910,47328,47178,47376,46622,47816,45382,46373,45699,41307,41536,47818,45675,47805,46233,46047,40378,34084,41559,34391,47862,39641,34388,39151,34392,39650,34178,39523,33136,40178,39806,45341,40280,47886,39132,39953,38511,38990,39281,38585,39040,39312,38474,38484,39376,39341,38460,38476,37221,38459,38470,38472,38393,31205,31901,31813,31809,31988,32014,46260,32166,32268,34265,47794,34270,47766,32935,34289,47770,34351,34322,34314,34329,34306,34371,33131,34349,33461,34304,46113,32646,37792,38551,40302,39313,38989,47329,47421,39113,38791,39408,39498,38678,39617,31878,38349,38352,38359,38356,38364,32800,32786,34267,34273,34275,34280,32925,32934,45590,58018,34292,45672,46943,33150,47821,47086,34298,34302,34308,34328,34327,34403,34326,41689,33800,33752,34376,34377,34023,34356,34385,34384,34395,34375,33976,34379,34215,33822,42433,43932,43140,43156,42236,43456,43587,42256,43979,42796,44222,44466,43128,43444,42850,43445,42536,44182,44132,43416,42235,43955,44264,42376,42963,43436,44442,42060,40730,34290,34399,28310,41430,28585,28512,28528,28319,41047,41690,31029,40102,39569,39248,31206,30535,30544,30745,30868,30558,222271,222256,222266,222265,222242,222270,56384,222272,222244,222246,56979,222269,227198,227194,56500,227373,227387,227403,227374,227372,227388,227417,227411,227422,228360,228535,228954,227633,229125,229130,229135,227588,229172,228114,228299,228366,228464,229874,228580,230142,230143,228237,228290,228365,228241,228306,228440,228466,228500,230582,230733,230812,230933,230965,231023,231030,231035,231041,227800,227801,228834,227443,228315,230551,228501,229606,230657,229127,229225,229231,230851,229265,229484,230597,230612,230669,230684,230755,230797,230846,230884,227701,227850,228118,228126,228476,228504,228509,227577,227576,228203,228182,229520,229667,230565,229810,229940,230001,230166,227927,230122,230138,228240,228301,228439,228498,230009,230309,228497,230376,229409,227922,227902,231088,228722,31626,227600,228195,229917,229509,231084,228894,228961,230006,230575,229510,228160,228164,229986,227921,229189,229419,229210,230858,229291,229062,229348,229437,229320,229656,229601,230753,229151,230449,229834,229913,229955,231069,231060,230844,230256,230317,230382,230405,230513,231082,230545,230586,230497,229592,230754,230770,229521,229518,230977,230999,231025,227549,227524,231077,228768,231083,227589,231117,230409,227820,227851,228096,229353,228234,228370,228453,231058,228052,230877,231065,231073,228759,230948,230815,230414,227713,230964,228045,229236,230535,230772,229655,227731,228117,227920,227787,229719,227882,228977,229358,228116,227978,228582,228038,230361,228050,228053,227776,228065,229774,229870,228602,229597,230276,230377,230640,230648,230654,229383,44883,57079,44753,56736,57805,228139,51749,230012,228206,230708,229117,227914,228494,228536,228570,228631,228634,52092,228640,228686,228878,229259,51872,228310,229141,229180,230067,228662,229736,228043,229595,230714,228887,228886,229880,229876,230998,57458,227511,227534,227658,227666,227692,230773,229093,227841,227852,227871,227930,228136,228131,228028,228077,228097,228119,228130,228144,228162,228183,228211,230705,228246,228300,228309,228510,228533,228575,229322,229253,229372,228664,230250,228645,228648,228884,228920,230029,228932,228964,230847,51419,51630,51631,227502,227564,227565,227566,227567,230471,230788,227755,227756,227848,229668,230190,230304,228562,229239,228132,229235,230379,230419,230529,230599,230601,230695,230745,230750,230798,230025,230894,227736,231066,44862,45163,228095,229929,227518,228198,228210,229252,228273,228514,228525,228587,228600,228638,227706,227531,228907,229191,229414,230816,229202,228668,229930,229636,229790,230081,229659,228870,230099,230133,229362,229145,229146,229274,231116,229040,229299,230019,229346,229350,230394,229454,228667,229507,229549,229755,230320,230347,228573,230469,230480,226981,227505,227757,230000,228192,228071,230840,228352,228357,228426,227686,228597,229196,228898,229251,228976,230531,230536,230542,230546,230547,230558,230564,230569,230655,227940,227925,230749,230863,230903,230913,230918,231031,229522,231054,51336,227208,227938,231095,229754,229156,229741,229161,230947,229717,228879,227714,229442,230402,230510,230515,230572,230578,230620,230625,230637,230681,230765,230795,230801,229264,229262,229049,229044,230889,230896,230904,228615,230966,229430,227989,231015,228696,227481,227667,227681,229846,227819,228700,228177,228275,228277,228093,229203,229175,229170,227703,230219,230134,229109,229113,231132,229118,229124,229129,229134,230154,230217,229176,230125,51856,230333,230362,228381,230595,229884,230615,230732,230763,230769,230075,230934,230971,229446,227998,231052,44810,57403,227339,227967,227768,231093,228308,228362,228965,229100,229211,229217,229222,231107,229909,229260,228788,228802,229882,229939,228012,228021,228019,228022,229400,229984,229996,228013,228023,228016,228720,228803,228002,228484,228738,228734,227640,230252,230267,230897,229254,230078,51488,227918,230544,228386,230594,228709,230882,228250,228281,230775,228488,227919,228528,228563,228564,228565,229497,228929,228933,229952,229108,229275,229270,229247,228774,227815,227527,229243,228438,228222,229944,228009,230127,230151,228379,230836,230598,230658,227929,230777,230783,230828,230215,230991,231009,231034,227548,231075,231086,230258,57208,51861,227473,227602,227705,227758,229077,228061,230555,230717,227872,228465,228568,228773,227945,229240,228165,228225,228692,227774,230026,230554,230588,230607,230643,230162,230696,230169,230961,229132,228475,51665,230585,231021,228534,228896,228959,229242,228018,51452,230290,227506,230628,227591,227626,227654,228840,229154,229205,229325,229412,230245,230210,229769,229887,228089,228873,228024,230017,228060,230172,228574,227685,230518,230540,230553,227210,230821,230962,230265,231000,227476,231017,227596,231074,231085,227493,227498,227499,227509,227512,227514,227515,227516,229016,227538,227551,229554,231014,227601,227603,227607,227610,227614,227649,227650,229029,227668,227676,227678,231007,227684,227698,227741,227772,227780,227794,227804,227805,229020,227807,229010,229007,229013,231005,229804,227827,229563,231063,228267,227821,231053,231012,230985,231070,231062,227458,227448,231064,231067,230232,227459,228451,229550,227475,231081,228512,230909,231046,230988,228569,227846,227822,227844,228601,227823,228795,229025,227826,227824,227829,228085,227861,227830,227831,227837,227838,227862,229018,227847,227856,227858,227860,227895,227492,228101,230989,227911,229001,227975,228084,227751,229720,228091,230624,230813,231092,230817,229022,229008,228231,230845,230886,230867,230983,227489,227490,227497,227445,231008,231018,227608,231048,227494,227508,230117,227791,227816,228086,228087,228255,228314,228523,230330,230085,228973,230234,227965,228983,228986,228996,229019,229023,229050,230917,229114,229122,229131,229139,229155,230919,229174,229182,230805,230261,230802,229258,229283,230666,229304,229342,230086,229371,231059,230257,229775,231022,230334,229379,231027,229415,229424,229531,229429,229791,229527,229545,231020,229748,229759,229763,230792,229767,230100,231100,230794,229985,229785,229889,229798,231043,230111,229802,230329,229803,229836,229873,229221,229903,230823,231101,229907,231109,230740,230241,229932,230862,227447,230269,230188,230113,230118,230136,229970,230963,230228,227885,229215,230275,230337,230701,230401,230434,230476,230641,228251,230727,227556,230752,229213,229208,231011,230781,230787,231103,230105,230814,231102,231108,228906,230266,228356,227892,228980,230326,230839,229017,229511,229046,228170,229508,229142,227883,230286,227797,228945,228942,229214,229219,227786,227888,229711,229306,229309,229333,229337,228592,229366,227879,229443,229455,229700,229499,229501,227810,229735,229523,227484,231038,230253,230742,230930,229551,229555,230442,230685,229569,229633,229589,230567,231016,230129,229338,229904,229733,230470,229737,229604,229945,230831,227792,229906,229867,230091,229709,229739,229905,230084,229632,230043,230008,230417,230557,230350,229704,229853,228846,229696,231105,229885,229892,230489,231024,227891,229914,229953,230982,229961,229969,230227,227875,231115,230698,230089,230170,230621,228848,230672,227671,230161,229858,230178,227813,230273,230313,228169,231004,230331,228989,230809,231040,228168,229856,231006,229524,228202,229472,228800,227903,230336,228579,228974,228979,229000,229032,228236,228288,228364,228437,227904,229256,229312,228394,227833,227849,229456,227781,227870,229488,229778,229777,229776,229481,229485,230338,229530,229538,229553,230743,229562,230231,229898,228361,229602,229394,229564,229915,229881,230976,229487,229559,230455,230374,230284,230381,230367,228855,227573,229622,229630,228228,229158,229724,229683,230667,229725,228359,229928,230804,229886,229875,230484,229734,229781,229801,229149,230841,227544,227547,229861,229878,230302,230053,229587,229883,228173,227752,230906,228994,229918,229968,228434,228529,230264,229552,230490,230132,230280,228209,230923,229786,229159,229427,230485,228039,230090,229389,230652,228492,227572,228343,230799,228860,229517,229153,229428,229447,229451,230920,227886,229557,229637,227767,229869,227973,228134,227558,228223,228270,227709,229410,228329,228783,229002,229053,229092,229102,229119,229061,229165,229185,228256,230969,228470,228508,228253,228324,228445,228506,227832,229405,230486,228322,229494,229534,230935,228519,230556,228526,228349,229646,228421,229721,227864,227853,230411,227662,230548,228036,230869,230868,228520,229959,228515,229598,228348,229649,228435,230277,228539,230819,229690,228455,229694,228271,229245,228493,229339,229705,230675,229710,230109,229591,230200,228269,228483,228262,230186,230432,228334,228257,228037,228538,228410,227959,230023,228452,229851,229958,228457,227593,228776,227958,228428,228489,230115,229999,228333,230550,228460,228398,230094,230114,228456,228487,230422,230216,230272,230627,228448,228480,228531,230328,230424,228430,229910,229916,229500,230931,230936,229660,229699,230061,227630,227634,227653,227727,227664,230325,230324,227803,229380,227765,229310,231096,227996,228153,228197,229593,228586,229980,228771,229989,228554,228781,229476,227949,228901,228971,229388,229094,229107,229128,229060,228113,228446,229082,229413,229351,227759,230429,230279,229758,228441,229406,228503,229422,228243,229528,229864,230916,230689,230332,229610,228112,229673,228388,230408,228429,228468,230649,229150,228103,229533,228442,230433,228461,228276,227557,229503,228358,229514,229541,228244,228323,227574,228205,229993,228444,230418,228469,229623,230392,228242,229628,231072,228495,228948,229665,229805,229685,229714,228317,229452,227884,230278,230010,230719,229261,228502,230102,230106,230204,230207,228401,230395,230300,229334,230635,228458,228522,230709,230747,230856,230883,229347,229311,227625,227695,227845,229912,228963,229812,227997,228949,228326,228141,229638,229588,228280,227599,228283,228661,228679,228693,228083,228859,228649,228943,229048,229344,229090,229116,229160,229257,229168,228711,229318,230661,229417,229460,229479,229783,229491,229492,227546,229395,229746,230223,227569,230430,229722,229768,229519,227762,229596,228847,230065,230047,229612,229618,229654,230119,227954,230243,229137,229732,230389,231049,229788,230135,230707,230351,229611,229844,229987,229934,229850,229453,229367,228035,229297,229574,228547,230506,229990,230710,230165,230528,230107,230613,230124,230539,230339,229572,228717,229600,229076,231078,230248,230295,230312,231039,230426,227652,229962,230700,230450,229657,230458,230730,230353,230512,230576,229679,230609,230616,230468,228598,229677,227507,230915,229766,230593,230378,229795,227704,227712,227880,227742,227889,229824,227942,227950,230007,228491,227984,230002,228107,228127,227877,227529,228224,228327,228376,228467,229096,227646,228532,230939,228161,228159,227468,229083,229343,228218,229277,229295,230688,229336,229382,230020,229542,228548,229434,229444,229478,229104,229526,229539,229956,229631,229943,230477,229693,230163,231079,227809,230876,230461,229747,229891,230677,229782,229787,229822,229872,230508,229068,229899,227694,229924,230501,229966,230734,229983,228656,229069,230973,230070,228915,230130,229045,230147,227522,229370,230168,230589,230959,230739,231047,229387,229979,229965,230202,230318,230268,229571,230387,230464,230552,230619,230496,230703,230704,229585,228710,229244,230618,231055,227526,229075,229480,229106,227843,230195,229960,227532,228278,228284,229369,228737,229433,229157,228690,228702,228707,228714,228726,228727,228733,230699,229792,229609,228106,229166,227539,229349,229355,229392,229404,229502,229326,229516,229099,228033,228048,228064,228712,229976,228549,230444,228926,229543,229575,229576,229619,229676,229744,229603,229827,229894,230561,229926,229973,229974,230683,228760,229544,227915,229641,229620,229771,229582,229866,230879,228655,229147,230514,227970,227969,230573,228589,227635,230663,227638,230721,227689,227699,230854,230013,229058,227854,227470,227779,230967,230888,230623,227868,227559,227582,229726,227586,229817,230345,227764,227993,227994,230293,227770,229843,228133,228146,227966,228208,229089,230893,231036,228298,230287,228560,228590,230673,228917,230660,228940,230192,230191,229845,229701,229761,229779,229839,228178,230051,228995,230076,230384,230453,230359,230864,230927,230052,231050,230591,230960,228908,230711,230034,228518,230587,229331,229363,229560,229580,230517,230523,227817,228665,227462,227477,230504,227682,227690,227746,227561,227563,227568,227735,227980,227990,227763,228105,230706,228611,228104,228613,229828,230522,227657,227642,227675,228049,230349,228072,230346,230980,227795,227798,227842,228609,228075,228537,227878,230242,228157,228186,229384,228100,228545,230425,228046,229650,229661,230520,229957,227873,227876,229441,230062,228981,227999,230849,228123,228124,228129,228155,228176,228185,229086,228229,228239,228674,230040,230035,228482,230810,228524,229581,229064,229376,228924,229740,228604,228607,228626,228885,230003,229066,230671,230680,228939,228947,229364,229133,229729,229793,230048,227545,229298,230198,230466,230472,230292,229391,230855,228030,230494,230492,228047,228055,228187,230233,228196,228216,229512,227693,227711,227488,228059,227533,227453,227510,229425,227896,227469,228880,228747,229431,229586,230063,230482,229329,227612,227637,228040,228953,230150,227647,227749,228978,227753,230371,228191,230420,229814,228088,227953,228056,228121,228527,227651,231098,228261,228264,227924,228511,228558,228567,228576,229308,229385,228608,228921,228919,228704,228705,229773,227628,230678,228958,229036,229121,229293,229418,229614,229605,229548,229635,229644,229671,229688,229760,229908,229937,227782,230187,230459,230610,229224,230842,230905,228032,227961,228058,229457,227656,229138,227865,228122,228138,228163,228166,228172,229566,230957,227663,229757,228889,230153,227465,227482,227670,227503,227535,227562,227899,228905,230463,227900,227598,227609,229947,227907,227660,227661,228990,227674,227715,227738,227496,227910,227771,229314,230060,229323,227645,227783,227955,227775,227632,228029,228204,230087,228031,228928,228701,230203,227748,227855,227898,230487,227991,230761,228110,228137,228143,228147,230246,228175,228207,228462,227928,227501,227936,228838,229356,228605,228618,228941,227571,227766,228651,228902,228925,229374,227734,229111,229201,229206,229288,229911,227814,229489,229403,227683,230926,227901,228017,228238,228673,228140,230478,228057,229967,229278,229640,229515,227777,229493,230723,230407,228904,230674,227480,230288,229408,229608,230416,230423,228098,230428,227995,230690,230247,230180,228750,229465,227528,227688,230942,227455,230713,227466,227578,228642,230912,227721,227587,230943,229925,227659,227665,227926,228042,227773,229477,227908,227913,228967,230050,227677,228639,228076,227747,230404,228530,228079,228115,228156,228158,228194,230873,228477,228478,230438,228481,227939,228753,228603,228594,228903,230682,228970,229823,229105,229115,230271,230500,229226,229263,229267,230209,230149,227952,228844,227976,227979,229237,229232,228233,228741,229938,230549,229642,229707,229738,229789,229809,229833,230080,230560,229890,228227,229963,229972,230004,229923,229697,230121,228719,230224,230148,230244,231029,230282,227943,230398,230406,230440,229584,228732,230632,230687,228937,230516,228588,230955,230791,230853,227450,230992,227818,229730,231061,227454,228723,227487,228888,227525,230744,230222,227624,227680,230771,230782,227724,227909,229818,229821,230456,229975,228073,227619,228744,227874,228010,228011,228067,228167,228201,228254,230064,227799,228337,228347,228997,229375,228542,228578,228911,228944,228946,228952,230731,228992,230274,229126,229163,229192,229220,229241,229273,229280,230631,229316,230702,230796,229359,229365,230303,229794,229402,230211,229765,229328,230605,230366,229615,229140,230537,228584,229532,230651,229041,229712,229647,227944,230984,229652,230368,230360,229664,229706,227639,228691,229594,229811,230146,229340,229868,229397,230412,230030,230664,229249,230056,228841,230103,230436,228220,230131,230526,230181,228969,227622,230413,230577,230451,230483,229981,230427,230270,230344,230778,230617,230626,229227,230818,230803,230824,230850,228966,228796,230997,230238,229294,229702,230230,227912,230108,230785,230079,229038,228809,228972,227931,227937,227992,230018,228051,228069,228330,228353,228355,228400,228473,230748,229250,228546,230046,228628,229459,229171,230907,228938,228094,230372,229026,230323,229051,229070,229095,229713,230157,229289,229321,227672,230953,229416,229458,229461,228666,229505,230184,228801,231001,227530,227519,230297,228663,228913,230968,230263,229540,230633,231090,229863,229556,228730,230175,230431,231002,231099,230208,230580,230925,229590,229682,228975,229716,229723,230171,230566,230183,230096,227594,229927,230296,229950,230447,229772,230156,227555,230110,230193,229537,228703,229052,230445,230281,228630,230437,230499,230559,230568,229223,230725,230715,230807,228807,230645,228672,229439,230644,230015,230676,227968,227700,230780,227740,227788,229849,228962,227964,228044,228062,228063,227636,228005,229081,228003,228521,227617,228620,229187,228677,228678,228823,228899,228998,228999,229014,229021,229031,229037,229065,230139,229073,230144,228617,230123,229097,229120,229136,229144,229167,229238,229190,230137,230189,229193,227917,230952,228676,229922,229290,229373,229473,227673,227540,229547,230495,230316,230342,230940,227495,230126,229749,227679,230757,230235,229770,229808,230692,230251,229832,229854,230260,230074,231003,229829,229871,228881,229877,230016,230870,230073,228914,230120,229466,230285,227449,230301,230343,230305,230388,230393,230397,230521,230590,230611,230647,230665,230716,227452,229266,229271,230741,230653,230642,230875,228213,228214,230956,228068,228128,229087,228616,229727,229815,227725,227834,231028,228006,228026,228027,228811,227716,228833,228472,227580,228272,228008,228499,228671,228697,228882,228892,228912,228960,229028,229034,229059,228619,229074,229080,229103,228624,229272,229292,229360,228819,228815,230878,229426,227988,228000,230636,228993,229470,230348,230375,228145,229936,229718,227737,230789,229625,231045,229669,228657,229680,227739,229684,230240,229689,227543,229715,229043,229813,229819,229855,227730,230460,228816,229920,229942,229971,229230,229229,230306,227623,230055,227553,227579,228890,228001,227719,228805,230322,230369,230385,230746,230403,230435,230452,230457,230474,230479,230829,230608,230491,230581,229692,227985,230584,230604,230066,228149,230830,230826,230837,230880,227707,230970,228895,229009,230140,229674,229462,229015,227550,227605,227643,231013,228581,227793,227796,229005,227828,227835,227857,227859,227932,230986,227962,229011,228221,228248,231097,228553,230213,227513,230255,231076,228659,228956,229027,227732,229035,229831,230505,230910,228034,229691,229838,229098,227839,227483,230068,229731,230311,229057,227517,227710,229469,230990,230104,230093,230987,230972,228909,229162,227717,227446,231068,230391,230630,230174,228610,229686,229420,230352,228804,230978,230182,229919,229378,229212,230629,230562,227982,230112,230473,230358,227486,230365,230167,231112,230827,227761,229799,230602,230639,230718,231037,230914,230077,231114,230995,229841,230036,230321,230386,230415,230519,230532,227722,230860,230835,228014,231044,227597,227729,231104,230259,231110,231113,231118,228893,227836,229825,227606,230448,231091,227743,227894,230908,228321,230874,228772,230335,228174,229330,228764,229865,228922,229486,230092,229004,229006,229012,227629,230922,229112,227584,229178,229184,229209,228463,229951,227890,229313,230975,228479,229361,227869,227760,230924,229411,229423,227789,229450,227867,229475,229498,227840,229626,228393,229902,229634,227648,228363,229852,229672,229687,227460,229703,228387,228338,228454,229797,229490,229842,229857,229896,229900,229901,230866,230979,229948,228274,229964,230738,230509,228285,230028,230033,230101,230141,228424,230199,230205,227806,228540,228543,229529,230283,227825,229525,228784,230475,230488,229807,230493,229583,230592,230871,230622,228282,228459,228490,228263,230722,230737,229830,230825,230843,229471,231026,231056,231087,231089,228780,227537,230341,227785,229448,229570,230786,230164,229639,227957,227581,227592,227604,229101,228614,227745,227613,229204,230098,230088,230736,230014,228770,227893,227583,230603,227983,227986,227987,228020,227520,227631,228279,228230,228252,230044,228474,228513,228751,229091,229496,228583,229467,228647,230728,230380,228951,229988,227463,229164,229152,229186,228090,230206,231057,229072,228041,228654,229357,229401,230072,230502,230932,230370,229616,229561,229567,228863,229617,229627,229651,229377,229666,229110,229743,229613,230058,229897,229977,229995,230031,230691,229464,230152,231051,231071,230237,229991,230865,229629,230373,228193,230421,230443,230974,230527,230534,230600,230467,230670,229670,230694,230779,230859,230054,227491,227521,229495,229436,229506,228108,227552,228181,229319,228212,228226,228550,229599,230811,229173,230570,229317,230533,228931,227784,228729,227897,228660,230315,227935,227778,229234,229315,229698,228957,228080,228148,227523,228111,228120,228125,228135,230254,228151,228595,228171,229300,229949,229281,228694,230911,228918,228775,228791,228695,228821,229893,230729,229421,229764,229079,227916,229197,229199,229998,229296,229332,230774,229393,228670,229398,229435,230212,228797,229354,231033,229535,229558,230574,230937,229756,229800,229662,229658,230221,230524,230541,230614,230693,230686,229954,228810,230638,229056,231094,227461,228650,227464,227467,227472,227479,227541,227542,227554,227595,227644,227720,228591,227500,227750,229282,228217,228485,228507,229504,229307,229568,228930,228715,228074,228988,230145,230697,228740,228923,228606,229933,227590,229820,229826,227946,230790,228883,230941,228987,230128,229078,228621,228622,228787,229269,229276,229287,228235,228260,230498,229042,229047,227697,228653,228876,229368,229390,230650,230954,229565,229579,230201,229663,230159,229678,229708,230994,229148,229745,228633,227808,228808,229816,229645,229931,229445,229978,230039,228985,230071,229835,230095,230097,229246,227728,230579,228599,230327,230355,230363,230364,228154,228721,230396,230439,227723,230446,230454,230462,227718,230571,230662,228839,228652,229840,230833,230646,230938,230950,230656,229463,231010,229432,227504,227536,227570,227627,227687,227726,228142,229233,228092,228152,227702,228471,228505,229468,228551,52757,228328,228891,227769,228555,231715,56166,43143,227199,222267,231524,47782,229946,228150,230340,227456,228200,56198,56329,42964,30671,229992,230596,229860,229143,227585,227941,228496,230668,226362,231080,56567,229607,227905,229624,228232,228287,229750,228516,229780,229806,229847,230543,226359,230236,222295,229643,228669,230806,227439,32318,56485,56213,230993,231199,32317,43330,228015,227956,230041,229228,229396,229573,230298,227974,230310,230314,230319,227655,228577,58065,230583,230724,226363,230892,230634,228099,229279,231019,231516,230218,36955,44873,228025,229848,230059,230069,56554,56731,45600,228245,228247,230239,44622,231111,228265,228268,228286,228266,227933,229063,228292,228293,56034,228295,228296,228297,230289,228303,228304,228305,228307,228311,228312,228313,228316,228318,228319,228294,228302,227802,227866,230822,228325,229054,228331,228332,228004,228335,228339,56117,228354,228340,228341,227951,228345,228346,227744,229327,228350,228351,227560,228336,228344,227971,227863,228367,228369,228371,228372,228373,228375,228377,228378,229474,227811,228382,228897,230299,228380,228383,228384,56406,228385,56319,227474,227471,230160,228389,228390,228391,228392,230057,228395,228397,56383,228399,229695,228402,228403,228405,228406,228407,228408,228409,230155,230768,228415,228416,228418,228419,228420,228404,228422,228423,230399,228425,228427,230356,227972,228081,228078,227934,230712,227948,228443,229483,229386,228447,230958,228449,228450,228765,228436,229513,229482,228627,227615,228544,222243,228552,228556,228561,228739,228566,228571,228572,55889,228585,56474,228593,222361,228916,228612,228646,229352,228623,228625,229055,227575,228632,228635,228636,228641,228070,228935,227923,228936,229169,229179,230481,228643,228779,229177,56544,55954,56502,56353,56330,55919,229198,229194,228675,228680,228681,228682,228683,228684,228685,230196,228688,228689,229200,229039,229181,229195,230606,229345,56381,228189,228699,56141,229071,228708,230249,228735,230861,228713,228557,228718,230197,230307,228724,228927,228725,228687,56501,230758,228736,228007,227621,228742,228743,230049,230027,228731,228746,228748,228644,230383,228749,227478,228752,228184,228754,228756,228757,56390,228758,228761,228762,228763,230185,228766,228767,228109,228769,229862,228950,230022,228188,228777,228778,230820,228082,228782,230503,227947,228786,230179,230177,228789,228755,227981,228793,56548,56553,230507,228799,227611,230176,230173,230042,230045,228806,230037,228818,228790,228792,56121,228820,55997,56110,228822,56304,230194,228824,230011,228826,228827,229751,228829,228830,228831,228832,229796,229728,230511,55903,228836,228837,230082,229762,230083,229753,228842,228843,228215,56005,228982,228991,228180,231106,228249,228258,228320,228396,228412,228413,230563,230354,228431,228432,229879,228968,229188,57525,56854,229381,228716,228745,229302,45287,228835,231634,44913,44649,56857,57864,222308,231155]])->all();
        
        foreach ($produto_filiais as $produto_filial){  
            $arquivo_imagem = "/var/tmp/fotos_br/com_logo/".$produto_filial->produto->codigo_fabricante." cópia.jpg";
            
            if (file_exists($arquivo_imagem)){
                fwrite($arquivo_log, $produto_filial->produto->id.";".$produto_filial->produto->codigo_fabricante.";Imagem Encontrada\n");
                //rename($arquivo_imagem,"/var/tmp/fotos_atualizadas_br/mudar/".$produto_filial->produto->codigo_fabricante.".jpg");
                
                $caminhoImagem          = "/var/tmp/fotos_br/com_logo/".$produto_filial->produto->codigo_fabricante." cópia.jpg";
                $caminhoImagemSemLogo   = "/var/tmp/fotos_br/sem_logo/".$produto_filial->produto->codigo_fabricante.".jpg";
                
                if (file_exists($caminhoImagem)) {
                    echo $caminhoImagem." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produto_filial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    $imagem->save();
                    
                    //var_dump(rename($caminhoImagem, "/var/tmp/vnc1200_900/".str_replace("/","-",$produtoFilial->produto->nome).".jpg"));
                } else {
                    echo $caminhoImagem." - NÃO EXISTE\n";
                    continue;
                }
            } else{
                fwrite($arquivo_log, $produto_filial->produto->id.";".$produto_filial->produto->codigo_fabricante.";Imagem Não Encontrada\n");
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_log);
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de verificação das imagens BR!\n\n";
    }
}