<?php


namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
//rGzp7N*s
//1000$pecas
class TesteAction extends Action
{
    public function run()
    {

	$meli = new Meli(static::APP_ID, static::SECRET_KEY);
	$filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');

	$filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', 98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');

        $produtos_meli_id = ['MLB1505689305','MLB1572968699','MLB1572968866','MLB1572965136','MLB1572965388'];
        //$produtos_meli_id = ["MLB1296240805"];

        foreach($produtos_meli_id as $k => $meli_id){

	    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'meli_id', $meli_id])->one();

            echo "\n".$k." - ".$meli_id;

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
		if($produto_filial->filial_id == 98){
			$meliAccessToken = $response_conta_duplicada->access_token;
		}


		$response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
		//print_r($response_item);
		if(isset($response_item["body"]->permalink)){
			echo "\n".$response_item["body"]->permalink;
		}

		continue;
		die;

                $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                //print_r($response_item); die;
                $titulo = ArrayHelper::getValue($response_item, 'body.title');
                
                $lado_name	= null;
                if(strpos($titulo,"Esquerd")){
                    $lado_name  = "Esquerdo";
                }
                else{
                    if(strpos($titulo,"Direit")){
                        $lado_name  = "Direito";
                    }
                }
                
                $posicao_name	= null;
                if(strpos($titulo,"Diant")){
                    $posicao_name  = "Dianteira";
                }
                else{
                    if(strpos($titulo,"Tras")){
                        $posicao_name  = "Traseira";
                    }
                }

                $gtin_id = "-1";
                $gtin_name = "" ;

                $oem_id = "-1";
                $oem_name = "";

                $mpn_id = "-1";
                $mpn_name = "";
                //print_r(ArrayHelper::getValue($response_item, 'body.attributes')); die;

                foreach (ArrayHelper::getValue($response_item, 'body.attributes') as $atributo){
                    //print_r($atributo);
                    if (ArrayHelper::getValue($atributo, 'id') == 'GTIN'){
                        if (ArrayHelper::getValue($atributo, 'value_id') != '-1'){
                            $gtin_name = ArrayHelper::getValue($atributo, 'value_name' );
                            $gtin_id = "";
                            break;
                        }
                    }

                    if (ArrayHelper::getValue($atributo, 'id') == 'OEM'){
                        if (ArrayHelper::getValue($atributo, 'value_id') != '-1'){
                            $oem_name = ArrayHelper::getValue($atributo, 'value_name' );
                            $oem_id = "";
                            break;
                        }
                    }

                    if (ArrayHelper::getValue($atributo, 'id') == 'MPN'){
                        if (ArrayHelper::getValue($atributo, 'value_id') != '-1'){
                            $mpn_name = ArrayHelper::getValue($atributo, 'value_name' );
                            $mpn_id = "";
                            break;
                        }
                    }
                }
                
                $body = [
                    'attributes' =>[
                        [
                            'id'            => 'GTIN',
                            'name'          => 'C??digo universal de produto',
                            'value_id'      => $gtin_id,
                            'value_name'    => $gtin_name,
                            'value_struct'  => null,
                            'values'        => [
                                0   => [
                                    'id' => $gtin_id,
                                    'name' => $gtin_name,
                                    'struct' => null,
                                ]
                            ],
                            'attribute_group_id' => 'OTHERS',
                            'attribute_group_name' => 'Outros',
                        ],
                        [
                            'id' => 'OEM',
                            'name' => 'OEM',
                            'value_id' => $oem_id,
                            'value_name' => $oem_name,
                            'value_struct' => null,
                            'values' => [
                                0 => [
                                    'id' => $oem_id,
                                    'name' => $oem_name,
                                    'struct' => null,
                                ]
                            ],
                            'attribute_group_id' => 'OTHERS',
                            'attribute_group_name' => 'Outros',
                        ],
                        [
                            'id' => 'MPN',
                            'name' => 'MPN',
                            'value_id' => $mpn_id,
                            'value_name' => $mpn_name,
                            'value_struct' => null,
                            'values' => [
                                0 => [
                                    'id' => $oem_id,
                                    'name' => $mpn_name,
                                    'struct' => null,
                                ]
                            ],
                            'attribute_group_id' => 'OTHERS',
                            'attribute_group_name' => 'Outros',
                        ],
                        [
                             'id'			        => 'BRAND',
                             'name'			        => 'Marca do produto',
                             'value_name'		    => 'OPT',
                             'attribute_group_name'	=> 'Outros'
                        ],
                        /*[
                            'id'                    => 'MODEL',
                            'name'                  => 'Modelo do produto',
                            'value_name'            => 'CHAVE',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        [
                            "name"			            => "N??mero de pe??a",
                            "value_name"		        => "10102020",
                            "attribute_group_name"	    => "Outros",
                        ],
                        [
                            "name"			            => "Origem",
                            "value_name"		        => "IMPORTADO",
                            "attribute_group_name"	    => "Outros",
                        ],
                        [
                            "id"			            => "MATERIAL",
                            "name"			            => "Material",
                            'value_id'                  => '2748301',
                            "value_name"		        => "Alum??nio",
                            "attribute_group_name"	    => "Outros",
                        ],
                        /*[
                            "id"			            => "CARBURETOR_CHOKE_CABLE_LENGTH",
                            "name"			            => "Comprimento do cabo afogador",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        [
                            "id"			            => "MATERIAL",
                            "name"			            => "Material",
                            'value_id'                  => '2748301',
                            "value_name"		        => "Alum??nio",
                            "attribute_group_name"	    => "Outros",
                        ],
                        /*[
                            "id"			            => "LENGTH",
                            "name"			            => "Comprimento",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CONNECTORS_NUMBER",
                            "name"			            => "Quantidade de conectores",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "POSITION",
                            "name"			            => "Posi????o",
                            'value_id'                  => '405827',
                            "value_name"		        => "Dianteira",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "POSITION",
                            "name"			            => "Posi????o",
                            'value_id'                  => '405827',
                            "value_name"		        => "Dianteira",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUMPER_BRACKET_LENGTH",
                            "name"			            => "Comprimiento do soporte de p??ra-choques",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "OIL_FILTER_TYPE",
                            "name"			            => "Tipo de filtro de ??leo",
                            'value_id'                  => '2342577',
                            "value_name"		        => "Monoblock",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HIGHT",
                            "name"			            => "Comprimento",
                            "value_name"		        => "1 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /**/
                        /*[
                            "id"			            => "INCLUDES_GASKETS",
                            "name"			            => "Inclui juntas",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "MATERIAL",
                            "name"			            => "Material",
                            'value_id'                  => '112648',
                            "value_name"		        => "Metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "FLOAT_LENGTH",
                            "name"			            => "Comprimento da boia",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "POSITION",
                            "name"			            => "Posi????o",
                            'value_id'                  => '405827',
                            "value_name"		        => "Dianteira",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BRAKE_DISC_THICKNESS",
                            "name"			            => "Espessura do disco de freio",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "PISTONS_NUMBER",
                            "name"			            => "N??mero de pist??os",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_PISTON_RING_SET_MATERIAL",
                            "name"			            => "Material",
                            'value_id'                  => '2307548',
                            "value_name"		        => "Metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "COMPATIBLE_PISTON_BORE_DIAMETER",
                            "name"			            => "Di??metro do furo do pist??o compat??vel",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BLEEDER_HOSES_INCLUDED",
                            "name"			            => "Mangueiras de purga inclu??das",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BRAKE_BOOSTER_INCLUDED",
                            "name"			            => "Servo-freio inclu??do",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "RESERVOIR_INCLUDED",
                            "name"			            => "Dep??sito de l??quido inclu??do",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "PRIMARY_OUTLET_THREAD_DIAMETER",
                            "name"			            => "Di??metro da rosca da sa??da principal",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SECONDARY_OUTLET_THREAD_DIAMETER",
                            "name"			            => "Di??metro da rosca da sa??da secund??ria",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_VALVE_TYPE",
                            "name"			            => "Tipo de v??lvula de motor",
                            'value_id'                  => '4683802',
                            "value_name"		        => "De admiss??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HEAD_DIAMETER",
                            "name"			            => "Di??metro da cabe??a",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HEAD_DIAMETER",
                            "name"			            => "Di??metro da cabe??a",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "LENGTH",
                            "name"			            => "Comprimento",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "STEM_DIAMETER",
                            "name"			            => "Di??metro da haste",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "USE_TYPE",
                            "name"			            => "Tipo de uso",
                            'value_id'                  => '2210135',
                            "value_name"		        => "Regular",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CONNECTOR_GENDER",
                            "name"			            => "G??nero do conector",
                            'value_id'                  => '2210104',
                            "value_name"		        => "Macho",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TERMINAL_QUANTITY",
                            "name"			            => "Quantidade de terminais",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WIRE_LENGTH",
                            "name"			            => "Comprimento do cabo",
                            "value_name"		        => "30 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "FRONT_EMBLEM_CAR",
                            "name"			            => "Emblema dianteiro",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BACK_EMBLEM_CAR",
                            "name"			            => "Emblema traseiro",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "MATERIAL_CAR_EMBLEM",
                            "name"			            => "Emblema traseiro",
                            'value_id'                  => '1140776',
                            "value_name"		        => "Alum??nio",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUMPER_BRACKET_LENGTH",
                            "name"			            => "Comprimiento do soporte de p??ra-choques",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CAR_DOOR_HINGE_MATERIAL",
                            "name"			            => "Material da dobradi??a de porta para carro",
                            'value_id'                  => '2747294',
                            "value_name"		        => "Metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CAR_DOOR_HINGE_POSITION",
                            "name"			            => "Posi????o da dobradi??a de porta para carro",
                            'value_id'                  => '2747315',
                            "value_name"		        => "Traseira dereita",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "PIN_AND_BUSHINGS_INCLUDED",
                            "name"			            => "Pasador e buchas inclu??dos",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "USE_TYPE",
                            "name"			            => "Tipo de uso",
                            'value_id'                  => '2210135',
                            "value_name"		        => "Regular",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CRANKSHAFT_SEALS_INCLUDED",
                            "name"			            => "Selos de virabrequim inclu??dos",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_TAPPET_GUIDE_HOLD_LENGTH",
                            "name"			            => "Comprimento do tucho hidr??ulico",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_TAPPET_GUIDE_HOLD_WIDTH",
                            "name"			            => "Largura do tucho hidr??ulico",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_TAPPET_GUIDE_HOLD_DIAMETER",
                            "name"			            => "Di??metro do tucho hidr??ulico",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SUSPENSION_CONTROL_ARM_BUSHING_TYPE",
                            "name"			            => "Tipo de bucha de controle de suspens??o",
                            'value_id'                  => '4636342',
                            "value_name"		        => "De bandeja",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WEIGHT",
                            "name"			            => "Peso",
                            "value_name"		        => "20 g",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "POSITION",
                            "name"			            => "Posi????o",
                            'value_id'                  => '2262160',
                            "value_name"		        => "Direito",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SIDE_POSITION",
                            "name"			            => "Lado",
                            'value_id'                  => '6813072',
                            "value_name"		        => "Traseiro",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WITH_LIGHT",
                            "name"			            => "Com luz",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WITH_LIGHT",
                            "name"			            => "Com luz",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WITH_MIRROR",
                            "name"			            => "Com espelho",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "NUMBER_OF_OPERABLE_WINDOWS",
                            "name"			            => "Quantidade de janelas oper??veis",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "NUMBER_OF_OPERABLE_WINDOWS",
                            "name"			            => "Quantidade de janelas oper??veis",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUMPER_BRACKET_LENGTH",
                            "name"			            => "Comprimiento do soporte de p??ra-choques",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CAR_FUEL_TANK_CAPACITY",
                            "name"			            => "Capacidade",
                            "value_name"		        => "20 L",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                    => 'MATERIAL',
                            "name"			        => "Material",
                            "value_id"		        => "-1",
                            "value_name"		    => null,
                            'value_struct'          => null,
                            'values' => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            "attribute_group_id"	=> "OTHERS",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			            => "MATERIAL_FINISH",
                            "name"			            => "Acabamento do material",
                            "value_name"		        => "Ferro",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "LENGTH",
                            "name"			            => "Comprimento",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WIDTH",
                            "name"			            => "Largura",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HEIGHT",
                            "name"			            => "Altura",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "PRESSURE_CAPACITY",
                            "name"			            => "Capacidade de press??o",
                            "value_name"		        => "100 N/mm??",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "FUEL_TYPE",
                            "name"			            => "Tipo de combust??vel",
                            'value_id'                  => '60406',
                            "value_name"		        => "Diesel",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ROTATION_TYPE",
                            "name"			            => "Tipo de rota????o",
                            'value_id'                  => '2511486',
                            "value_name"		        => "Padr??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_BLOCK_LENGTH",
                            "name"			            => "Comprimento do bloco de motor",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_BLOCK_WIDTH",
                            "name"			            => "Largura do bloco de motor",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_BLOCK_HEIGHT",
                            "name"			            => "Alto do bloco de motor",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CYLINDERS_QUANTITY",
                            "name"			            => "Quantidade de cilindros",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_ROCKER_ARM_TYPE",
                            "name"			            => "Tipo de balancim de motor",
                            'value_id'                  => '2251688',
                            "value_name"		        => "Oscilante",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "STUD_THREAD_DIAMETER",
                            "name"			            => "Di??metro da rosca do parafuso",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SELF_ALIGNING",
                            "name"			            => "Auto-compensador",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SHIMS_INCLUDED",
                            "name"			            => "Cal??os inclu??dos",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "USE_TYPE",
                            "name"			            => "Tipo de uso",
                            'value_id'                  => '2210135',
                            "value_name"		        => "Regular",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SUSPENSION_BALL_JOINT_POSITION",
                            "name"			            => "Posi????o do piv?? de suspens??o",
                            'value_id'                  => '2262141',
                            "value_name"		        => "Superior traseira",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SUSPENSION_BALL_JOINT_OUTSIDE_DIAMETER",
                            "name"			            => "Di??metro externo do piv?? de suspens??o",
                            //'value_id'                  => '2262141',
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "STUD_THREAD_DIAMETER",
                            "name"			            => "Di??metro da rosca do parafuso",
                            //'value_id'                  => '2262141',
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CASTLE_NUT_INCLUDED",
                            "name"			            => "Porca inclu??da",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_OIL_PAN_CAPACITY",
                            "name"			            => "Capacidade do c??rter de motor",
                            "value_name"		        => "10 L",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TOTAL_DEPTH",
                            "name"			            => "Profundidade total",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "DRAIN_PLUG_INCLUDED",
                            "name"			            => "Buj??o de drenagem inclu??do",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "OIL_LEVEL_SENSOR_PORT_INCLUDED",
                            "name"			            => "Porta do sensor do n??vel de ??leo inclu??da",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "GASKETS_INCLUDED",
                            "name"			            => "Juntas inclu??das",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CLUTCH_BEARING_INSIDE_DIAMETER",
                            "name"			            => "Di??metro interno do rolamento de embreagem",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CLUTCH_BEARING_OUTSIDE_DIAMETER",
                            "name"			            => "Di??metro externo do rolamento de embreagem",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "LENS_MATERIAL",
                            "name"			            => "Material da lente",
                            'value_id'                  => '3700859',
                            "value_name"		        => "Vidro",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BULBS_INCLUDED",
                            "name"			            => "L??mpadas inclu??das",
                            'value_id'                  => '242085',
                            "value_name"		        => "Sim",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BULB_TYPE",
                            "name"			            => "Tipo de l??mpada",
                            'value_id'                  => '3700860',
                            "value_name"		        => "Incandescente",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BULBS_NUMBER",
                            "name"			            => "Quantidade de l??mpadas",
                            "value_name"		        => "01",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BULB_SIZE",
                            "name"			            => "Modelo de l??mpadas",
                            "value_name"		        => "PECAAGO",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "WATTAGE",
                            "name"			            => "Watts",
                            "value_name"		        => "0 W",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "CAR_AXIS_POSITION",
                            "name"			            => "Eixo",
                            'value_id'                  => '405827',
                            "value_name"		        => "Dianteira",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "IDLER_ARM_SIDE",
                            "name"			            => "Lado do bra??os de suspens??o",
                            'value_id'                  => '476939',
                            "value_name"		        => "Esquerdo",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUSHING_INCLUDED",
                            "name"			            => "Buchas inclu??das",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "MATERIAL",
                            "name"			            => "Material",
                            'value_id'                  => '4837600',
                            "value_name"		        => "A??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "INLET_HOLE_NUMBER",
                            "name"			            => "Quantidade de dutos de entrada",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "OUTLET_HOLE_NUMBER",
                            "name"			            => "Quantidade de dutos de sa??da",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "USE_TYPE",
                            "name"			            => "Tipo de uso",
                            'value_id'                  => '2210135',
                            "value_name"		        => "Regular",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			        => "ENGINE_OIL_GRADE",
                            "name"		            => "Grau do ??leo de motor",
                            'value_id'              => '2204393',
                            'value_name'            => '5w40',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "ENGINE_OIL_TYPE",
                            "name"		            => "Tipo de ??leo de motor",
                            'value_id'              => '2204394',
                            'value_name'            => 'Sint??tico',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "SERVICE_CATEGORY",
                            "name"		            => "Categor??a de servi??o",
                            'value_id'              => '2204397',
                            'value_name'            => 'Automotivo',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "ENGINE_OIL_VOLUME",
                            "name"		            => "Volume do ??leo de motor",
                            //'value_id'              => '2204397',
                            'value_name'            => '20 L',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "CONTAINER_TYPE",
                            "name"		            => "Tipo de recipiente",
                            'value_id'              => '2204404',
                            'value_name'            => 'Tambor',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "BENDIX_GEAR_TEETH",
                            "name"		            => "Dentes bendix",
                            'value_name'            => '2',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "DIRECTION_ROTATION",
                            "name"		            => "Dentes bendix",
                            'value_id'              => '428853',
                            'value_name'            => 'Hor??rio',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "STARTER_VOLTAGE",
                            "name"		            => "Voltagem",
                            //'value_id'              => '428853',
                            'value_name'            => '20 V',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /**/
                        /*[
                            'id'                        => 'ENGINE_CONNECTING_ROD_LENGTH',
                            "name"			            => "Comprimento da biela de motor",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BIG_HOLE_DIAMETER',
                            "name"			            => "Di??metro do orif??cio grande",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SMALL_HOLE_DIAMETER',
                            "name"			            => "Di??metro do orif??cio pequeno",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BEARINGS_INCLUDED',
                            "name"			            => "Bronzinas inclu??das",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_CONNECTING_ROD_TYPE',
                            "name"			            => "Tipo de biela de motor",
                            'value_id'                  => '2282569',
                            "value_name"		        => "S??lida",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SUSPENSION_CONTROL_ARM_BUSHING_TYPE',
                            "name"			            => "Tipo de bucha de controle de suspens??o",
                            'value_id'                  => '4636342',
                            "value_name"		        => "De bandeja",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTAKE_DURATION_AT_0_50_INCH_LIFT',
                            "name"			            => "Dura????o da admiss??o em eleva????o de 0,50 polegadas",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'EXHAUST_DURATION_AT_0_50_INCH_LIFT',
                            "name"			            => "Dura????o do escape em eleva????o de 0,50 polegadas",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTAKE_VALVE_LIFT',
                            "name"			            => "Eleva????o da v??lvula de admiss??o",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'EXHAUST_VALVE_LIFT',
                            "name"			            => "Eleva????o da v??lvula de escape",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INSIDE_DIAMETER',
                            "name"			            => "Di??metro interno",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'LENGTH',
                            "name"			            => "Comprimento",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'OUTSIDE_DIAMETER',
                            "name"			            => "Di??metro externo",
                            "value_name"		        => "50 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SUSPENSION_CONTROL_ARM_BUSHING_TYPE',
                            "name"			            => "Tipo de bucha de controle de suspens??o",
                            'value_id'                  => '4636342',
                            "value_name"		        => "De bandeja",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAX_SPEED_MEASURED',
                            "name"			            => "Velocidade m??xima medida",
                            'value_id'                  => 'km/h',
                            "value_name"		        => "50 km/h",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAX_RPM_MEASURED',
                            "name"			            => "RPM m??ximas medidas",
                            "value_name"		        => "10",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'WITH_LIGHT',
                            "name"			            => "Com luz",
                            'value_id'                  => '242084',
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'TECHNOLOGY',
                            "name"			            => "Tecnologia",
                            'value_id'                  => '6492844',
                            "value_name"		        => "Anal??gico",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_COLOR',
                            "name"			            => "Cor do cinto de seguran??a",
                            'value_id'                  => '2215576',
                            "value_name"		        => "Preto",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INJECTION_TYPE',
                            "name"			            => "Tipo de inje????o",
                            'value_id'                  => '515926',
                            "value_name"		        => "Monoponto",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'FUEL_TYPE',
                            "name"			            => "Tipo de combust??vel",
                            'value_id'                  => '64364',
                            "value_name"		        => "Gasolina",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_COLOR',
                            "name"			            => "Cor do cinto de seguran??a",
                            'value_id'                  => '2215576',
                            "value_name"		        => "Preto",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_CONFIGURATION',
                            "name"			            => "Configura????o do cinto de seguran??a",
                            'value_id'                  => '2215582',
                            "value_name"		        => "Uma pe??a",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_TYPE',
                            "name"			            => "Tipo de cinto de seguran??a",
                            'value_id'                  => '2215585',
                            "value_name"		        => "Regular",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_LENGTH',
                            "name"			            => "Comprimento do cinto de seguran??a",
                            'value_id'                  => 'cm',
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SEAT_BELT_WIDTH',
                            "name"			            => "Largura do cinto de seguran??a",
                            'value_id'                  => 'cm',
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAXIMUM_TORQUE',
                            "name"			            => "Torque m??ximo",
                            "value_name"		        => "10 Nm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'OIL_CAPACITY',
                            "name"			            => "Capacidade",
                            "value_name"		        => "10 L",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'TRANSFER_CASE_TYPE',
                            "name"			            => "Tipo de caixa de transfer??ncia",
                            "value_name"		        => "Engrenagem",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'OUTPUT_SHAFT_POSITION',
                            "name"			            => "Posi????o do eixo de sa??da",
                            'value_id'                  => '2230571',
                            "value_name"		        => "Frente",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'DRIVE_TYPE',
                            "name"			            => "Tipo de tra????o",
                            'value_id'                  => '2230573',
                            "value_name"		        => "4x4",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                    => 'PUMP_TYPE',
                            'name'                  => 'Tipo de bomba',
                            'value_id'              => '2108527',
                            'value_name'            => 'Eletr??nica',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INJECTION_TYPE',
                            'name'                  => 'Tipo de inje????o',
                            'value_id'              => '2108528',
                            'value_name'            => 'Bomba rotativa',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'BOLT_DIAMETER',
                            'name'                  => 'Di??metro do parafuso',
                            'value_name'            => '1 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'BOLT_LENGTH',
                            'name'                  => 'Comprimento do parafuso',
                            'value_name'            => '1 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ROTATING_HEAD',
                            'name'                  => 'Cabe??a rotativa',
                            'value_id'              => '242084',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'POSITION',
                            'name'                  => 'Posi????o',
                            'value_id'              => '405827',
                            'value_name'            => 'Dianteira',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'BRAKE_DISC_TYPE',
                            'name'                  => 'Tipo de disco de freio',
                            'value_id'              => '361632',
                            'value_name'            => 'Ventilado',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'OUTSIDE_DIAMETER',
                            'name'                  => 'Di??metro externo',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'IS_SLOTTED',
                            'name'                  => '?? ranhurado',
                            'value_id'              => '242084',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'CONNECTOR_GENDER',
                            'name'                  => 'G??nero do conector',
                            'value_id'              => '2210105',
                            'value_name'            => 'F??mea',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ABS_SENSOR_LENGTH',
                            'name'                  => 'Comprimento do sensor ABS',
                            'value_name'            => '25 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ABS_SENSOR_POSITION',
                            'name'                  => 'Posi????o do sensor ABS',
                            'value_id'              => '2210115',
                            'value_name'            => 'Traseiro',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'TERMINAL_QUANTITY',
                            'name'                  => 'Quantidade de terminais',
                            'value_name'            => '3',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'WIRE_LENGTH',
                            'name'                  => 'Comprimento do cabo',
                            'value_name'            => '30 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'FUEL_TYPE',
                            'name'                  => 'Tipo de combust??vel',
                            'value_name'            => 'Gasolina',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INSTALLATION_PLACEMENT',
                            'name'                  => 'Lugar de coloca????o',
                            'value_name'            => 'Exterior',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'MAX_PRESSURE',
                            'name'                  => 'Press??o m??xima',
                            'value_name'            => '20 psi',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'OUTLETS_NUMBER',
                            'name'                  => 'Quantidade de tomadas',
                            'value_name'            => '2',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_FILTER',
                            'name'                  => 'Inclui filtro',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'PRESSURE_CAPACITY',
                            'name'                  => 'Capacidade de press??o',
                            'value_name'            => '101 N/mm??',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'COLOR',
                            'name'                  => 'Cor',
                            'value_name'            => 'Preto',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'NUMBER_OF_UNITS_BY_KIT',
                            'name'                  => 'Unidades por pacote',
                            'value_name'            => '2',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'MOUNTING_TYPE',
                            'name'                  => 'Tipo de montagem',
                            'value_name'            => 'Dianteiro',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'REAR_WIPER_MEASURE',
                            'name'                  => 'Medida da palheta traseira',
                            'value_name'            => '2 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'DRIVER_WIPER_MEASURE',
                            'name'                  => 'Medida da palheta condutor',
                            'value_name'            => '2 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'PASSENGER_WIPER_MEASURE',
                            'name'                  => 'Medida da palheta acompanhante',
                            'value_name'            => '2 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ALTERNATOR_PULLEY_WIDTH',
                            'name'                  => 'Largura da polia',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ALTERNATOR_PULLEY_INSIDE_DIAMETER',
                            'name'                  => 'Di??metro interno',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ALTERNATOR_PULLEY_OUTSIDE_DIAMETER',
                            'name'                  => 'Di??metro externo',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'GROOVE_NUMBER',
                            'name'                  => 'N??mero de sulcos',
                            'value_name'            => '2',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INLET_DIAMETER',
                            'name'                  => 'Di??metro de entrada',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'OUTLET_DIAMETER',
                            'name'                  => 'Di??metro de sa??da',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "id"			            => "RIVET_TYPE",
                            "name"			            => "Tipo de rebite",
                            "value_name"		        => "Rosca",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "RIVET_DIAMETER",
                            "name"			            => "Di??metro do rebite",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "MATERIAL",
                            "name"			            => "Material",
                            "value_name"		        => "Metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "QUANTITY_OF_PADS",
                            "name"			            => "Quantidade de pastilhas",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "INCLUDES_WEAR_SENSORS",
                            "name"			            => "Inclui sensores de desgaste",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "FMSI_NUMBER",
                            "name"			            => "C??digo FMSI",
                            "value_name"		        => "10102020",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "RIVET_HEAD_TYPE",
                            "name"			            => "Tipo de cabe??a",
                            "value_name"		        => "Chata",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "RIVET_HEAD_DIAMETER",
                            "name"			            => "Di??metro da cabe??a",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "SALE_FORMAT",
                            "name"			            => "Formato de venda",
                            "value_name"		        => "Unidade",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "UNITS_PER_PACKAGE",
                            "name"			            => "Unidades por pacote",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HAND_BRAKE_CABLE_MATERIAL",
                            "name"			            => "Material do cabo de freio de m??o",
                            "value_name"		        => "A??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HAND_BRAKE_CABLE_LENGTH",
                            "name"			            => "Comprimento do cabo de freio de m??o",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HAND_BRAKE_CABLE_COVER_MATERIAL",
                            "name"			            => "Material da capa do cabo de freio de m??o",
                            "value_name"		        => "Borracha",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HAND_BRAKE_CABLE_COVER_LENGTH",
                            "name"			            => "Comprimento da capa do cabo de freio de m??o",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "INCLUDES_ASSEMBLY_KIT",
                            "name"			            => "Inclui kit de montagem",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BRAKE_HYDRAULIC_HOSE_LENGTH",
                            "name"			            => "Comprimento",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "END_THREAD_DIAMETER",
                            "name"			            => "Di??metro da rosca",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TIMING_BELT_TENSIONER_PULLEY_MATERIAL",
                            "name"			            => "Material do tensor da correia dentada",
                            "value_name"		        => "Pl??stico",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TIMING_BELT_TENSIONER_PULLEY_WIDTH",
                            "name"			            => "Largura do tensor da correia dentada",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TIMING_BELT_TENSIONER_PULLEY_INSIDE_DIAMETER",
                            "name"			            => "Di??metro interno do tensor da correia dentada",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "TIMING_BELT_TENSIONER_PULLEY_OUTSIDE_DIAMETER",
                            "name"			            => "Di??metro externo do tensor da correia dentada",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BELT_GROOVE_COMPRESSOR",
                            "name"			            => "Canais da polia",
                            "value_name"		        => "8PK",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "COMPRESSOR_PULLEY_DIAMETER",
                            "name"			            => "Di??metro da polia",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "COMPRESSOR_REFRIGERANT_TYPE",
                            "name"			            => "Tipo de refrigerante",
                            "value_name"		        => "R134A",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ALTERNATOR_VOLTAGE",
                            "name"			            => "Voltagem do alternador",
                            "value_name"		        => "14V",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "AMPERAGE",
                            "name"			            => "Amperagem",
                            "value_name"		        => "90 A",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "PULLEY_DIAMETER",
                            "name"			            => "Di??metro da roldana",
                            "value_name"		        => "10 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "INLET_CONNECTION_DIAMETER",
                            "name"			            => "Di??metro do conector de entrada",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "OUTLET_CONNECTION_DIAMETER",
                            "name"			            => "Di??metro do conector de sa??da",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "HOUSING_INCLUDED",
                            "name"			            => "Inv??lucro inclu??do",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUMPER_IMPACT_ABSORBER_LENGTH",
                            "name"			            => "Comprimento do alma do parachoque",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BUMPER_IMPACT_ABSORBER_WIDTH",
                            "name"			            => "Anchura do alma do parachoque",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_CYLINDER_HEAD_BOLT_LENGTH",
                            "name"		                => "Comprimento do parafuso de cabe??ote de cilindro",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_CYLINDER_HEAD_BOLT_DIAMETER",
                            "name"		                => "Di??metro do parafuso de cabe??ote de cilindro",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "ENGINE_CYLINDER_HEAD_BOLT_HEAD_TYPE",
                            "name"		                => "Tipo de cabe??a do parafuso de cabe??ote de cilindro",
                            "value_name"		        => "20 in",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "BOLT_THREAD_WIDTH",
                            "name"		                => "Largura da rosca do parafuso",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "NUMBER_TEETH_WHEEL_SIDE",
                            "name"		                => "Dentes lado roda",
                            "value_name"		        => "3",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            "id"			            => "NUMBER_TEETH_HUB_SIDE",
                            "name"		                => "Dentes lado cambio",
                            "value_name"		        => "3",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                    => 'CLUTCH_SLAVE_CYLINDER_LENGTH',
                            'name'                  => 'Comprimento do atuador de embreagem',
                            'value_name'            => '20 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ANCHOR_TYPE',
                            'name'                  => 'Tipo de ancoragem',
                            'value_name'            => 'Parafusado',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'PARKING_LIGHTS_INCLUDED',
                            'name'                  => 'Com luz de posi????o',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_LED_LIGHT',
                            'name'                  => 'Inclui luzes LED',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                        => 'HOOD_HINGE_LENGTH',
                            "name"			            => "Comprimento da dobradi??a de cap??",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'HOOD_HINGE_WIDTH',
                            "name"			            => "Largura da dobradi??a de cap??",
                            "value_name"		        => "20 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAXIMUM_OPENING_ANGLE',
                            "name"			            => "??ngulo m??ximo de abertura",
                            "value_name"		        => "180??",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                    => 'INTAKE_DIAMETER',
                            "name"			        => "Di??metro de admiss??o",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'COMPRESSOR_DIAMETER',
                            "name"			        => "Di??metro do compressor",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'IS_NUMBERED',
                            "name"			        => "?? numerado",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_CYLINDER_HEAD_MATERIAL',
                            "name"			            => "Material",
                            "value_name"		        => "Metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_CYLINDER_HEAD_TOTAL_LENGTH',
                            "name"			            => "Comprimento total",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_CYLINDER_HEAD_TOTAL_HEIGHT',
                            "name"			            => "Altura total",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'CAMSHAFT_INCLUDED',
                            "name"			            => "Comando de v??lvulas inclu??do",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'VALVES_GUIDE_INCLUDED',
                            "name"			            => "Guias de v??lvulas inclu??dos",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SPARK_PLUGS_POSITION',
                            "name"			            => "Posi????o das velas de igni????o",
                            "value_name"		        => "Horizontal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_CRANKSHAFT_PULLEY_MATERIAL',
                            "name"			        => "Material da polia do virabrequim",
                            "value_name"		    => "Alum??nio",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                       /*[
                            'id'                    => 'ENGINE_CRANKSHAFT_PULLEY_WIDTH',
                            "name"			        => "Largura da polia do virabrequim",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_CRANKSHAFT_PULLEY_BELT_TYPE',
                            "name"			        => "Tipo de correia da polia do virabrequim",
                            "value_name"		    => "Poli-V",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_CRANKSHAFT_PULLEY_INTERNAL_DIAMETER',
                            "name"			        => "Di??metro interno da polia do virabrequim",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'GROOVE_QUANTITY',
                            "name"			        => "N??mero de sulco",
                            "value_name"		    => "2",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'SIDE',
                            "name"			        => "Lado",
                            "value_name"		    => "Esquerdo",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'OUTER_TIE_ROD_END_LENGTH',
                            "name"			        => "Comprimento",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'THREAD_GENDER',
                            "name"			        => "Rosca",
                            "value_name"		    => "Macho",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ROD_THREAD_SIZE',
                            "name"			        => "Rosca passo",
                            "value_id"		        => "-1",
                            "value_name"		    => null,
                            'value_struct'          => null,
                            'values' => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            "attribute_group_id"	=> "OTHERS",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'CLUTCH_FORK_LENGTH',
                            "name"			        => "Comprimento do garfo de embreagem",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'BEARING_HOLDER_WIDTH',
                            "name"			        => "Largura do suporte do rolamento",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'CLUTCH_FORK_INSIDE_DIAMETER',
                            "name"			        => "Di??metro interno do garfo de embreagem",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'CLUTCH_FORK_OUTSIDE_DIAMETER',
                            "name"			        => "Di??metro externo do garfo de embreagem",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],/*
                        [
                            'id'                    => 'CLUTCH_BEARING_INSIDE_DIAMETER',
                            "name"			        => "Di??metro interno do rolamento de embreagem",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],
                        [
                            'id'                    => 'CLUTCH_BEARING_OUTSIDE_DIAMETER',
                            "name"			        => "Di??metro externo do rolamento de embreagem",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],
                        /*[
                            'id'                    => 'FUEL_INJECTION_PIPE_LENGTH',
                            "name"			        => "Comprimento do cano de injetores",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENDS_OUTSIDE_DIAMETER',
                            "name"			        => "Di??metro externo das pontas",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENDS_INSIDE_DIAMETER',
                            "name"			        => "Di??metro interno das pontas",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'FUEL_INJECTION_PIPE_SHAPE',
                            "name"			        => "Forma do cano de injetores",
                            "value_name"		    => "Moldado",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'WATER_PUMP_MATERIAL',
                            "name"			        => "Material do corpo",
                            "value_name"		    => "Alum??nio",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'PROPELLER_PUMP_MATERIAL',
                            "name"			        => "Material do rotor",
                            "value_name"		    => "Alum??nio",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'GASKET_INCLUDED',
                            "name"			        => "Inclui junta",
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'SCREWS_INCLUDED',
                            "name"			        => "Inclui parafusos",
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'PULLEY_INCLUDED',
                            "name"			        => "Inclui polia",
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'BAR_DIAMETER',
                            "name"			        => "Di??metro do arame",
                            "value_name"		    => "20 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'CAR_AXIS_POSITION',
                            "name"			        => "Eixo",
                            "value_name"		    => $posicao_name,
                            "attribute_group_name"	=> "Outros",
                        ],*/

                        /*[
                            'id'                    => 'BUSHING_INCLUDED',
                            "name"			        => "Buchas inclu??das",
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTERCOOLER_HOSE_MATERIAL',
                            "name"			            => "Material da mangueira de intercooler",
                            "value_name"		        => "Borracha",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTERCOOLER_HOSE_LENGTH',
                            "name"			            => "Comprimento da mangueira de intercooler",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTERCOOLER_HOSE_ENDS_OUTSIDE_DIAMETER',
                            "name"			            => "Di??metro externo das pontas da mangueira de intercoleer",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INTERCOOLER_HOSE_ENDS_INSIDE_DIAMETER',
                            "name"			            => "Di??metro interno das pontas da mangueira de intercooler",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAXIMUM_WORKING_PRESSURE',
                            "name"			            => "Press??o de trabalho m??xima",
                            'value_id'                  => "-1",
                            "value_name"		        => null,
                            "value_struct"	            => null,
                            'values'                    => [
                                0       => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                        => 'CLAMPS_INCLUDED',
                            "name"			            => "Grampos inclu??dos",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'INSIDE_DIAMETER',
                            "name"			            => "Di??metro interno",
                            "value_name"		        => "1 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'LENGTH',
                            "name"			            => "Comprimento",
                            "value_name"		        => "1 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'OUTSIDE_DIAMETER',
                            "name"			            => "Di??metro externo",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SUSPENSION_CONTROL_ARM_BUSHING_TYPE',
                            "name"			            => "Tipo de bucha de controle de suspens??o",
                            "value_name"		        => "De bandeja",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SPEEDS_NUMBER',
                            "name"			            => "Quantidade de velocidades",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'CLUTCH_BEARING_INSIDE_DIAMETER',
                            "name"			            => "Di??metro interno do rolamento de embreagem",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'CLUTCH_BEARING_OUTSIDE_DIAMETER',
                            "name"			            => "Di??metro externo do rolamento de embreagem",
                            "value_name"		        => "10 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_OIL_DIPSTICK_MATERIAL',
                            "name"			            => "Material da vareta de nivel de ??leo",
                            "value_name"		        => "Alum??nio",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_OIL_DIPSTICK_WIDTH',
                            "name"			            => "Largura da vareta de nivel de ??leo",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_OIL_DIPSTICK_LENGTH',
                            "name"			            => "Comprimento da vareta de nivel de ??leo",
                            "value_name"		        => "20 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_OIL_DIPSTICK_TUBE_INCLUDED',
                            "name"			            => "Tubo da vareta de nivel de ??leo inclu??do",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'POWER_STEERING_PUMP_INLET_DIAMETER',
                            "name"			            => "Di??metro do porto de entrada da bomba de dire????o",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'POWER_STEERING_PUMP_OUTLET_DIAMETER',
                            "name"			            => "Di??metro do porto de sa??da da bomba de dire????o",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'POWER_STEERING_PUMP_PULLEY_INCLUDED',
                            "name"			            => "Polia da bomba de dire????o inclu??da",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'POWER_STEERING_PUMP_TYPE',
                            "name"			            => "Tipo de bomba de dire????o",
                            "value_name"		        => "El??trica",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'RESERVOIR_INCLUDED',
                            "name"			            => "Dep??sito de l??quido inclu??do",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/

                        /*[
                            'id'                        => 'PRIMARY_SHOE_FRICTION_MATERIAL_THICKNESS',
                            "name"			            => "Espessura do material de fric????o da sapata prim??ria",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SECONDARY_SHOE_FRICTION_MATERIAL_THICKNESS',
                            "name"			            => "Espessura do material de fric????o da sapata secund??ria",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'PRIMARY_SHOE_FRICTION_MATERIAL_LENGTH',
                            "name"			            => "Comprimento do material de fric????o da sapata prim??ria",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'SECONDARY_SHOE_FRICTION_MATERIAL_LENGTH',
                            "name"			            => "Comprimento do material de fric????o da sapata secund??ria",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'FRICTION_MATERIAL_BONDING_TYPE',
                            "name"			            => "Tipo de fixa????o do material de fric????o",
                            "value_name"		        => "Rebitado",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BRAKE_DRUM_DIAMETER',
                            "name"			            => "Di??metro do tambor de freio",
                            "value_name"		        => "25 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MOUNTING_HARDWARE_INCLUDED',
                            "name"			            => "Acess??rios de instala????o inclu??dos",
                            "value_name"		        => "N??o",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ENGINE_STROKE',
                            "name"			            => "Tempos do motor",
                            "value_name"		        => "2",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MAIN_JOURNAL_BUSHING_DIAMETER',
                            "name"			            => "Di??metro do munh??o principal",
                            "value_name"		        => "2 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'WRIST_PIN_JOURNAL_BUSHING_DIAMETER',
                            "name"			            => "Di??metro do munh??o da biela",
                            "value_name"		        => "2 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'CRANKSHAFT_LENGTH',
                            "name"			            => "Comprimento do virabrequim",
                            "value_name"		        => "2 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'ROD_DIAMETER',
                            "name"			            => "Di??metro da biela",
                            "value_name"		        => "2 mm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BRAKE_DRUM_OUTSIDE_DIAMETER',
                            "name"			            => "Di??metro externo do tambor de freio",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BRAKE_DRUM_INSIDE_DIAMETER',
                            "name"			            => "Di??metro interno do tambor de freio",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'BRAKE_DRUM_NOMINAL_DIAMETER',
                            "name"			            => "Di??metro nominal do tambor de freio",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MUFFLER_MATERIAL',
                            "name"			            => "Material do silencioso",
                            "value_name"		        => "Liga de metal",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MUFFLER_SHAPE',
                            "name"			            => "Forma do silencioso",
                            "value_name"		        => "Quadrado",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MUFFLER_LENGTH',
                            "name"			            => "Comprimento do silencioso",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'MUFFLER_WIDTH',
                            "name"			            => "Largura do silencioso",
                            "value_name"		        => "25 cm",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'QUANTITY_OF_INLETS',
                            "name"			            => "Quantidade de entradas",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id'                        => 'QUANTITY_OF_OUTLETS',
                            "name"			            => "Quantidade de sa??das",
                            "value_name"		        => "1",
                            "attribute_group_name"	    => "Outros",
                        ],*/
                        /*[
                            'id' 			        => 'ENGINE_PISTON_MATERIAL_TYPE',
                            'name' 			        => 'Tipo de material do pist??o de motor',
                            'value_name' 		    => 'Forjado',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                            'id' 			        => 'ENGINE_PISTON_PIN_LENGTH',
                            'name' 			        => 'Largo da trava do pist??o de motor',
                            'value_name' 		    => '25 cm',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                            'id' 			        => 'ENGINE_PISTON_INSIDE_DIAMETER',
                            'name' 			        => 'Di??metro interno do pist??o de motor',
                            'value_name' 		    => '25 cm',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                            'id' 			        => 'ENGINE_PISTON_OUTSIDE_DIAMETER',
                            'name' 			        => 'Di??metro externo do pist??o de motor',
                            'value_name' 		    => '25 cm',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                            'id' 			        => 'OIL_RINGS_KIT_INCLUDED',
                            'name' 			        => 'Kit de an??is de aceite inclu??do',
                            'value_name' 		    => 'N??o',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                             'id' 			        => 'CONNECTOR_GENDER',
                             'name' 			    => 'G??nero do conector',
                             'value_id' 		    => '2210104',
                             'value_name' 		    => 'Macho',
                             'value_struct' 		=> null,
                             'attribute_group_id' 	=> 'OTHERS',
                             'attribute_group_name'	=> 'Outros',
                         ],*/
                        /*[
                            'id' 			        => 'TERMINAL_GENDER',
                            'name' 			        => 'G??nero do terminal',
                            'value_name' 		    => 'Macho',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                            'id' 			        => 'ENGINE_OIL_PRESSURE_SENSOR_TERMINAL_TYPE',
                            'name' 			        => 'Tipo de terminal do sensor de press??o de ??leo',
                            'value_name' 		    => 'Press??o',
                            'attribute_group_name'	=> 'Outros',
                        ],*/
                        /*[
                             'id'                    => 'TERMINAL_QUANTITY',
                             'name'                  => 'Quantidade de terminais',
                             'value_id'              => null,
                             'value_name'            => '1',
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Tipo de conector',
                            'value_name'            => 'H7',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Cor da luz',
                            'value_name'            => 'Branco-quente',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Modelo de l??mpadas',
                            'value_name'            => 'PECAAGO',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Quantidade de l??mpadas',
                            'value_name'            => '1',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Watts',
                            'value_name'            => '60 W',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Tecnologia de l??mpada',
                            'value_name'            => '1',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Cor de l??mpada',
                            'value_name'            =>  'Branco',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Di??metro m??ximo das p??s',
                            'value_name'            => '25 in',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'name'                  => 'Suportes de radiador inclu??dos',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                             'id'                    => 'TIPS_DIAMETER',
                             'name'                  => 'Di??metro das puntas',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                            'id'                    => 'BOLT_DIAMETER',
                            'name'                  => 'Di??metro do parafuso',
                            'value_id'              => null,
                            'value_name'            => '25 polegadas',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'BOLT_LENGTH',
                            'name'                  => 'Comprimento do parafuso',
                            'value_id'              => null,
                            'value_name'            => '35 polegadas',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                             'id'                    => 'MATERIAL',
                             'name'                  => 'Material',
                             'value_name'            => 'Borracha',
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'AUTOMOTIVE_PLACEMENT_SITE',
                            'name'                  => 'Lugar de coloca????o',
                            'value_name'            => 'Porta',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'WORKING_PRESSURE',
                            'name'                  => 'Press??o de trabalho',
                            'value_name'            => '20 bar',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'FILTER_SHAPE',
                            'name'                  => 'Forma do filtro',
                            'value_name'            => 'Retangular',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INPUT_DIAMETER',
                            'name'                  => 'Di??metro de entrada',
                            'value_name'            => '20 cm',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'MANUAL_TRANSMISSION_SHIFT_LEVER_BOOT_MATERIAL',
                            'name'                  => 'Material',
                            'value_name'            => 'Sint??tico',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'OUTSIDE_DIAMETER',
                            'name'                  => 'Di??metro externo',
                            'value_name'            => '25 mm',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INSIDE_DIAMETER',
                            'name'                  => 'Di??metro interno',
                            'value_name'            => '25 mm',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                             'id'                    => 'WRENCH_LENGTH',
                             'name'                  => 'Comprimento da chave',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                             'id'                    => 'AIRBAG_INCLUDED',
                             'name'                  => 'Airbag inclu??do',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'RADIO_CONTROLS_INCLUDED',
                             'name'                  => 'Controles de est??reo inclu??dos',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'STEERING_WHEEL_GRIP_MATERIAL',
                             'name'                  => 'Material do agarre',
                             'value_id'              => '2707741',
                             'value_name'            => 'Sint??tico',
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'name'                  => 'Tipo',
                             'value_name'            => 'Ajust??vel',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                             'id'                    => 'SIDE_POSITION',
                             'name'                  => 'Lados',
                             'value_name'            => 'Esquerdo',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                            'id'                    => 'COMPOSITION_TYPE',
                            'name'                  => 'Tipo de composi????o',
                            'value_name'            => 'Com g??s',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'EXTENDED_LENGTH',
                            'name'                  => 'Comprimento estendido',
                            'value_name'            => '25 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'COMPRESSED_LENGTH',
                            'name'                  => 'Comprimento comprimido',
                            'value_name'            => '25 cm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'IS_OEM_REPLACEMENT',
                            'name'                  => '?? reposi????o original',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'UNITS_PER_PACKAGE',
                            'name'                  => 'Unidades por pacote',
                            'value_name'            => '2',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'SHOCK_MOUNT_INSOLATOR_MATERIAL',
                            'name'                  => 'Material do coxim amortecedor',
                            'value_name'            => 'Chapa',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'BUSHING_MATERIAL',
                            'name'                  => 'Material da bucha',
                            'value_name'            => 'Pl??stico',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_SCREWS',
                            'name'                  => 'Inclui parafusos',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                             'id'                    => 'BUMPER_BRACKET_MATERIAL',
                             'name'                  => 'Material do suporte de p??ra-choques',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'ACCESSORY_TYPE',
                             'name'                  => 'Tipo de acess??rio',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'name'                  => 'Comprimento',
                             'value_name'            => null,
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'REFRIGERANT_TYPE',
                             'name'                  => 'Tipo de refrigerante',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'SEALS_INCLUDED',
                             'name'                  => 'Selos inclu??dos',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'SYSTEM_PRESSURE',
                             'name'                  => 'Press??o do sistema',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                             'id'                    => 'CLUTCH_BEARING_INSIDE_DIAMETER',
                             'name'                  => 'Di??metro interno do rolamento de embreagem',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => '',
                             'attribute_group_name'  => '',
                        ],*/
                         /*[
                             'id'                    => 'CLUTCH_BEARING_RACE_INCLUDED',
                             'name'                  => 'Pista do rolamento de embreagem inclu??da',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'CLUTCH_BEARING_OUTSIDE_DIAMETER',
                             'name'                  => 'Di??metro externo do rolamento de embreagem',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'SEALED_CLUTCH_BEARING',
                             'name'                  => 'Rolamento de embreagem selado',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'BENDIX_GEAR_TEETH',
                             'name'                  => 'Dentes bendix',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'DIRECTION_ROTATION',
                             'name'                  => 'Sentido de rota????o',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'STARTER_VOLTAGE',
                             'name'                  => 'Voltagem',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                             'id'                    => 'WIRE_LENGTH',
                             'name'                  => 'Comprimento do cabo',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'BRAKE_BOOSTER_DIAPHRAGM_TYPE',
                             'name'                  => 'Tipo de diafragma do servo-freio',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'BRAKE_BOOSTER_TYPE',
                             'name'                  => 'Tipo de servo-freio',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'BRAKE_BOOSTER_USE',
                             'name'                  => 'Uso do servo-freio',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'INCLUDES_MASTER_CYLINDER',
                             'name'                  => 'Inclui cilindro principal',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                         /*[
                             'id'                    => 'INCLUDES_PEDAL_ROD_EXTENSION',
                             'name'                  => 'Inclui extens??o da barra do pedal',
                             'value_id'              => '-1',
                             'value_name'            => null,
                             'value_struct'          => null,
                             'attribute_group_id'    => 'OTHERS',
                             'attribute_group_name'  => 'Outros',
                         ],*/
                        /*[
                            'id'                    => 'DISTANCE_BETWEEN_INJECTORS',
                            "name"			        => "Dist??ncia entre injetores",
                            "value_name"		    => "10 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'FUEL_INJECTION_RAIL_LENGTH',
                            "name"			        => "Comprimento da flauta de combust??vel",
                            "value_name"		    => "10 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'FUEL_SYSTEM_TYPE',
                            "name"			        => "Tipo de sistema de combust??vel",
                            "value_name"		    => "Combust??vel",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'INJECTOR_QUANTITY',
                            "name"			        => "Quantidade de injetores",
                            "value_name"		    => "1",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "name"			        => 'Manopla inclu??da',
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'RADIATOR_CAP_MATERIAL',
                            "name"			        => 'Material da tampa do radiador',
                            "value_name"		    => "Metal",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                => 'PRESSURE_CAPACITY',
                            'name'              => 'Capacidade de press??o',
                            'value_id'          => "-1",
                            'value_name'        => null,
                            'value_struct'      => null,
                            'values'            => [
                                0       => [
                                            'id'        => "-1",
                                            'name'      => null,
                                            'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "name"			        => "Voltagem",
                            "value_name"		    => "14V",
                            "attribute_group_name"	=> "Outros",
                        ],
                        [
                            "name"			        => "??ngulo de vis??o",
                            "value_name"		    => "180??",
                            "attribute_group_name"	=> "Outros",
                        ],
                        [
                            "name"			        => "Resolu????o de v??deo",
                            "value_name"		    => "720p",
                            "attribute_group_name"	=> "Outros",
                        ],
                        [
                            "name"			        => "Tamanho da tela",
                            "value_name"		    => "2900mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "HEIGHT",
                            "name"			        => "Altura",
                            "value_name"		    => "20 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "CLUTCH_TYPE",
                            "name"			        => "Tipo de embreagem",
                            "value_name"		    => "Monodisco",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "DISC_DIAMETER",
                            "name"			        => "Di??metro do disco",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "SPLINES_NUMBER",
                            "name"			        => "Quantidade de estrias",
                            "value_name"		    => "25",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "INCLUDES_CLUTCH_PLATE",
                            "name"			        => "Inclui plat??",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "INCLUDES_DISC",
                            "name"			        => "Inclui disco",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "INLET_FITTING_LINE_DIAMETER",
                            "name"			        => "Di??metro da linha de entrada",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "OUTLET_FITTING_LINE_DIAMETER",
                            "name"			        => "Di??metro da linha de sa??da",
                            "value_name"		    => "25 mm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "RACK_AND_PINION_TYPE",
                            "name"			        => "Tipo de caixa de dire????o",
                            "value_name"		    => "El??trica",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "INCLUDES_SEALS",
                            "name"			        => "Inclui juntas",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "INCLUDES_INNER_TIE_ROD",
                            "name"			        => "Inclui terminais",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "id"			        => "HEAD_BOLTS_INCLUDED",
                            "name"			        => "Parafusos de cabe??ote inclu??dos",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_BEARING_INSIDE_DIAMETER',
                            "name"			        => 'Di??metro interno da bronzina de motor',
                            "value_name"		    => '25 mm',
                            "attribute_group_name"	=> 'Outros',
                        ],/*
                        /*[
                            'id'                    => 'ENGINE_BEARING_OUTSIDE_DIAMETER',
                            "name"			        => 'Di??metro externo da bronzina de motor',
                            "value_name"		    => '25 mm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'OUTER_RING_WIDTH',
                            "name"			        => 'Largura do anel externo',
                            "value_name"		    => '25 mm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INNER_RING_WIDTH',
                            "name"			        => 'Largura do anel interno',
                            "value_name"		    => '25 mm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'USE_TYPE',
                            "name"			        => 'Tipo de uso',
                            "value_name"		    => 'Regular',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_INTAKE_HOSE_MATERIAL',
                            "name"			        => "Material da mangueira de admiss??o",
                            "value_name"		    => "Alum??nio",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_INTAKE_HOSE_LENGTH',
                            "name"			        => 'Comprimento da mangueira de admiss??o',
                            "value_name"		    => '25 cm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_INTAKE_HOSE_INSIDE_DIAMETER',
                            "name"			        => 'Di??metro interno da mangueira de admiss??o',
                            "value_name"		    => '25 cm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ENGINE_INTAKE_HOSE_OUTSIDE_DIAMETER',
                            "name"			        => 'Di??metro externo da mangueira de admiss??o',
                            "value_name"		    => '25 cm',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'MOUNTING_BRACKETS_INCLUDED',
                            "name"			        => 'Grampos inclu??dos',
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_FAN_ASSEMBLY',
                            "name"			        => 'Inclui defletor',
                            "value_name"		    => 'N??o',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'NUMBER_OF_FANS',
                            "name"			        => 'Quantidade de ventiladores',
                            "value_name"		    => '1',
                            "attribute_group_name"	=> 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'HOSE_MATERIAL',
                            'name'                  => 'Material da mangueira',
                            'value_id'              => '-1',
                            'value_name'            => null,
                            'value_struct'          => null,
                            'values'    => [
                                0 => [
                                    'id'            => "-1",
                                    'name'          => null,
                                    'struct'        => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ]*/
                        /*[
                            'id'                => 'SUSPENSION_CONTROL_ARM_BUSHING_TYPE',
                            'name'              => 'Tipo de bucha de controle de suspens??o',
                            'value_id'          => '-1',
                            'value_name'        => null,
                            'value_struct'      => null,
                            'values'            => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "name"			        => "Comprimento da mangueira",
                            "value_name"		    => "25 cm",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                => 'BURST_PRESSURE',
                            'name'              => 'Press??o de ruptura',
                            'value_id'          => '-1',
                            'value_name'        => null,
                            'value_struct'      => null,
                            'values' => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]

                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "name"			        => "Junta ou selo inclu??do",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'ROTATING_HEAD',
                            "name"			        => "Cabe??a rotativa",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'            => 'COUPLINGS_TYPE',
                            'name'          => 'Tipo de pontas',
                            'value_id'      => '-1',
                            'value_name'    => null,
                            'value_struct'  => null,
                            'values'    => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]

                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "id"                    => "GEARS_POSITIONS",
                            "name"			        => "Posi????es dos engrenagens",
                            "value_id"              => "-1",
                            "value_name"		    => null,
                            "value_struct"          => null,
                            'values' => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            "attribute_group_id"    => "OTHERS",
                            "attribute_group_name"  => "Outros",
                        ],*/
                        /*[
                            "name"			        => "Posi????o do eixo",
                            "value_name"		    => $posicao_name,
                            "attribute_group_name"	=> "Outros",

                        ],*/
                        /*[
                            "name"			        => "Quantidade de furos de montagem",
                            "value_name"		    => "2",
                            "attribute_group_name"	=> "Outros",

                        ],*/
                        /*[
                            "name"			        => "Inclui rolamento",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",

                        ],*/
                        /*[
                            'id'                    =>  'INCLUDES_MOUNTING_ACCESSORIES',
                            "name"			        => "Inclui acess??rios para a montagem",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",

                        ],*/
                        /*[
                            "name"			        => "Inclui sensor ABS",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",

                        ],*/
                        /*[
                            "name"			        => "Lado",
                            "value_name"		    => $lado_name,
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "name"			        => "Posi????o",
                            "value_name"		    => $posicao_name,
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                    => 'HIGHT',
                            'name'                  => 'Comprimento',
                            'value_id'              => null,
                            'value_name'            => '50 cm',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'LENGTH',
                            'name'                  => 'Comprimento',
                            'value_id'              => null,
                            'value_name'            => '5 m',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'WITH_REMOVABLE_SURFACE',
                            'name'                  => 'Com superficie remov??vel',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'SURFACE_MATERIALS',
                            'name'                  => 'Materiais da superf??cie',
                            'value_name'            => 'Alum??nio',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'IS_ORIGINAL',
                            'name'                  => '?? original',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'IS_SPORTING',
                            'name'                  => '?? esportivo',
                            'value_name'            => 'N??o',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'WIDTH',
                            'name'                  => 'Largura',
                            'value_name'            => '50 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'THICKNESS',
                            'name'                  => 'Espessura',
                            'value_name'            => '50 mm',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'CAR_FILTER_DESIGN',
                            'name'                  => 'Estrutura',
                            'value_id'              => null,
                            'value_name'            => 'Flex??vel',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'CAR_FILTER_MATERIAL',
                            'name'                  => 'Elemento',
                            'value_id'              => null,
                            'value_name'            => 'Non-woven',
                            'value_struct'          => null,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'EXTINGUISHING_AGENT',
                            'name'                  => 'Agente extintor',
                            'value_name'            => 'P?? ABC',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'FIRE_EXTINGUISHER_CAPACITY',
                            'name'                  => 'Capacidade do extintor',
                            'value_name'            => '2 kg',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'FIRE_CLASS',
                            'name'                  => 'Classe de fogo',
                            'value_name'            =>  'A, B, C',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_SUPPORT',
                            'name'                  => 'Inclui suporte',
                            'value_name'            =>  'N??o',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_MIRROR',
                            'name'                  => 'Inclui espelho',
                            'value_name'            =>  'N??o',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'TERMINATION_TYPE',
                            'name'                  => 'Tipo de acabamento',
                            'value_name'            =>  'Lisa',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'INCLUDES_CONTROL',
                            'name'                  => 'Inclui controle',
                            'value_name'            =>  'N??o',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'MIRROR_LOCATION',
                            'name'                  => 'Posi????o do espelho',
                            'value_name'            => 'Esquerda',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'DRILLS_SCREWDRIVERS_TYPE',
                            'name'                  => 'Tipo de furadeiras-parafusadeiras',
                            'value_name'            =>  'Furadeira/Parafusadeira',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'POWER',
                            'name'                  => 'Pot??ncia',
                            'value_name'            =>  '350 W',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'ROTATION_SPEED',
                            'name'                  => 'Velocidade de rota????o',
                            'value_name'            =>  '20 rpm',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'WITH_VARIABLE_SPEED',
                            'name'                  => 'Com velocidade vari??vel',
                            'value_name'            => 'N??o',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'IS_WIRELESS',
                            'name'                  => '?? sem fio',
                            'value_name'            => 'N??o',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'                    => 'TORQUE',
                            'name'                  => 'Torque',
                            'value_name'            => '20 ft-lbs',
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            "name"			        => "Rolamento de embreagem selado",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            "name"			        => "Pista do rolamento de embreagem inclu??da",
                            "value_name"		    => "N??o",
                            "attribute_group_name"	=> "Outros",
                        ],*/
                        /*[
                            'id'                => 'CLUTCH_BEARING_OUTSIDE_DIAMETER',
                            'name'              => 'Di??metro externo do rolamento de embreagem',
                            'value_id'          => "-1",
                            'value_name'        => null,
                            'value_struct'      => null,
                            'values'            => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/
                        /*[
                            'id'            => 'CLUTCH_BEARING_INSIDE_DIAMETER',
                            'name'          => 'Di??metro interno do rolamento de embreagem',
                            'value_id'      => "-1",
                            'value_name'    => null,
                            'value_struct'  => null,
                            'values'        => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/

                        /*[
                            'id'            => 'SIDE_POSITION',
                            'name'          => 'Lado',
                            'value_id'      => "-1",
                            'value_name'    => null,
                            'value_struct'  => null,
                            'values'        => [
                                0 => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/

                        /*[
                            'id'            => 'POSITION',
                            'name'          => 'Posi????o',
                            'value_id'      => "-1",
                            'value_name'    => null,
                            'value_struct'  => null,
                            'values'        => [
                                0           => [
                                    'id'        => "-1",
                                    'name'      => null,
                                    'struct'    => null,
                                ]
                            ],
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],*/

                    ]
                ];
//print_r($body);
                $response = $meli->put("items/{$meli_id}?access_token=" . $meliAccessToken, $body, []);

                if ($response['httpCode'] >= 300) {
                    print_r($response);
                    echo " - Erro - ";
                }else{
                    //print_r($response);
                    echo " - Ok - ".ArrayHelper::getValue($response, 'body.permalink');

                    /*$produto_filial_duplicado = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->one();
                    if($produto_filial_duplicado){
                        $response_duplicada = $meli->put("items/{$produto_filial_duplicado->meli_id}?access_token=" . $meliAccessToken_duplicada, $body, []);
                        echo " |Duplicada Encontrada - ";//.ArrayHelper::getValue($response_duplicada, 'body.permalink');

                        if ($response_duplicada['httpCode'] >= 300) {
                            echo " |Erro";
                        }else{
                            echo " |Ok";
                        }
                    }
                    else{
                        //print_r($response);
                        echo " -  Erro (Duplicada N??O encontrada";
                    }*/
                }
            }else{
                echo " - sem internet";
            }
        }
    }
}
