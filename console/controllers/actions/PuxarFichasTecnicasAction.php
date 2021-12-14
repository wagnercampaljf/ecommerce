<?php

namespace console\controllers\actions\mercadolivre;

use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use yii\helpers\ArrayHelper;

class PuxarFichasTecnicasAction extends Action
{
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $filial     = Filial::find()->andWhere(["=", "id", 94])->one();
        $meli       = new Meli(static::APP_ID, static::SECRET_KEY);
        $user       = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response   = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $filial_principal           = Filial::find()->andWhere(["=", "id", 72])->one();
            $user_principal             = $meli->refreshAccessToken($filial_principal->refresh_token_meli);
            $response_principal         = ArrayHelper::getValue($user_principal, 'body');
            $meliAccessToken_principal  = $response_principal->access_token;
            
            $produtos_filiais_mg = ProdutoFilial::find()->andWhere(["=", "filial_id", $filial->id])
                                                        ->andWhere(["is not", "meli_id", null])
                                                        ->all();
            
            foreach($produtos_filiais_mg as $k => $produto_filial_mg){
                
                $produto_filial_principal = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto_filial_mg->produto_id])
                                                                    ->andWhere(["<>", "filial_id", 98])
                                                                    ->andWhere(["<>", "filial_id", $filial->id])
                                                                    ->one();
                                                                                    
                if($produto_filial_principal){
                    $response_item = $meli->get("/items/".$produto_filial_principal->meli_id."?access_token=" . $meliAccessToken);
                    //print_r($response_item["body"]->attributes); die;
                
                    $body = array();
                    
                    foreach($response_item["body"]->attributes as $atributo){
                        print_r($atributo);
                        $body['attributes'][] = [
                                                    'id'                    => $atributo->id,
                                                    'value_name'            => $atributo->value_name,
                                                ];
                         
                    }
                    
                    print_r($body);
                    
                    $response = $meli->put("items/{$produto_filial_mg->meli_id}?access_token=" . $meliAccessToken, $body, []);
                    if ($response['httpCode'] >= 300) {
                        print_r($response);
                        echo " - ERRO Ficha Técnica";
                    } else {
                        echo " - OK Ficha Técnica";
                    }
                    
                    die;
                }
                
            }
            
            die;
            
            $x = 0;
            $y = 0;
            
            
            
            $produtos_meli_id = ['MLB864670656','MLB864673269','MLB864728304','MLB864735651','MLB864740948','MLB864741429','MLB864744953','MLB864744956','MLB864742543','MLB864738660','MLB864738990','MLB864745582','MLB864739185','MLB864739284','MLB864739288','MLB864739344','MLB867588628','MLB878403049','MLB878407336','MLB878408762','MLB878407693','MLB878403883','MLB878403927','MLB878403924','MLB878409527','MLB878409529','MLB878409532','MLB878412710','MLB878409818','MLB878413246','MLB878417410','MLB878417725','MLB878417735','MLB878410950','MLB878411236','MLB878418318','MLB878414585','MLB878411693','MLB878414814','MLB878418871','MLB878419050','MLB878419202','MLB878415710','MLB878419347','MLB878415897','MLB878420868','MLB878420870','MLB878420098','MLB878430536','MLB878423875','MLB878430601','MLB878430610','MLB878427476','MLB878427523','MLB878423937','MLB878427530','MLB878424320','MLB878424322','MLB878431281','MLB878436750','MLB878431873','MLB878431910','MLB880188008','MLB883822276','MLB883828826','MLB883826169','MLB883829060','MLB883822548','MLB883822571','MLB883822930','MLB883829427','MLB883826597','MLB883829432','MLB883822969','MLB883826677','MLB883826686','MLB883826698','MLB883829729','MLB883826878','MLB902687843','MLB902688162','MLB902688271','MLB902688283','MLB902691136','MLB902694478','MLB902688381','MLB902688599','MLB902694810','MLB917990204','MLB917984261','MLB970563165','MLB976212655','MLB979133806','MLB979133764','MLB979130385','MLB979128448','MLB1013306028','MLB1015668696','MLB1015666709','MLB1015636956','MLB1015634179','MLB1015626913','MLB1015625144','MLB1015623937','MLB1053597831','MLB1063743563','MLB1070527995','MLB1073101931','MLB1086761318','MLB1103769271','MLB1103765527','MLB1103762084','MLB1104345048','MLB1107462166','MLB1124091736','MLB1124076715','MLB1124040234','MLB1157503579','MLB1157885302','MLB1157885247','MLB1157883519','MLB1157883464','MLB1157883421','MLB1157880360','MLB1157880358','MLB1157880354','MLB1296294631'];
            
            print_r($produtos_meli_id);
            
            foreach($produtos_meli_id as $k => $meli_id){
                $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                //print_r($response_item);
                echo "\n".$k." - ".ArrayHelper::getValue($response_item, 'body.category_id')." - ".ArrayHelper::getValue($response_item, 'body.sold_quantity')." - ".ArrayHelper::getValue($response_item, 'body.status')." - ".ArrayHelper::getValue($response_item, 'body.permalink'); //.ArrayHelper::getValue($response_item, 'body.title')." - "
            }
            
            /*$response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                    echo "\n".++$x;
                    
                    if($x <= 5100){
                        continue;
                    }
                    
                    $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                    echo "\n".++$x." - ".ArrayHelper::getValue($response_item, 'body.category_id')." - ".ArrayHelper::getValue($response_item, 'body.permalink'); //.ArrayHelper::getValue($response_item, 'body.title')." - "
                    
                    if(ArrayHelper::getValue($response_item, 'body.category_id') == "MLB194773"){ //Categoria "Suporte Parachoque"

                        $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                        
                        if ($produto_filial){
                            if($produto_filial->quantidade > 0){
                                if(($produto_filial->produto->altura < 70) && ($produto_filial->produto->largura < 70) && ($produto_filial->produto->profundidade < 70)){
                                    $body = ["category_id" => utf8_encode("MLB251640"),]; //Categoria "Porcas de Roda, com ME"
                                    $response = $meli->put( "items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                                    if(ArrayHelper::getValue($response_item, 'httpCode') >= 300){
                                        echo " - Erro";
                                    }
                                    else{
                                        echo " - OK";
                                    }
                                }
                                else{
                                    $body = ["category_id" => utf8_encode("MLB191833"),]; //Categoria "Peças Automotivas -> Outras, sem ME"
                                    $response = $meli->put( "items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                                    if(ArrayHelper::getValue($response_item, 'httpCode') >= 300){
                                        echo " - Erro";
                                    }
                                    else{
                                        echo " - OK";
                                    }
                                }
                            }
                        }
                        else{
                            $body = ["category_id" => utf8_encode("MLB251640"),]; //Categoria "Porcas de Roda, com ME"
                            $response = $meli->put( "items/{$meli_id}?access_token=" . $meliAccessToken, $body, [] );
                            if(ArrayHelper::getValue($response_item, 'httpCode') >= 300){
                                echo " - Erro";
                            }
                            else{
                                echo " - OK";
                            }
                        }
                    }
                    else{
                        echo " - Outra Categoria";
                    }
                }
                $y++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }*/
        }
        
        echo "\n\nFIM!\n\n";
    }
}