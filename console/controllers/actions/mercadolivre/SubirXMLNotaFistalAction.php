<?php
//5555
namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\PedidoMercadoLivre;
use console\controllers\actions\omie\Omie;
use function GuzzleHttp\json_decode;
use backend\models\NotaFiscal;

class SubirXMLNotaFistalAction extends Action
{
        
    public function run()
    {

        //die;

        $arquivo_log_nome = "/var/tmp/log_subir_xml_".date("Y-m-d_H-i-s").".csv";
        $arquivo_log = fopen($arquivo_log_nome, "a");
        fwrite($arquivo_log, date("Y-m-d_H-i-s")."\n");
        
        $APP_KEY_OMIE_SP                   = '468080198586';
        $APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA   = '78ba33370fac6178da52d42240591291';
        $APP_KEY_OMIE_MG4                  = '1758907907757';
        $APP_SECRET_OMIE_MG4               = '0a69c9b49e5a188e5f43d5505f2752bc';
        
        //Obter access_toke da conta principal do ML
        $meli               = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial             = Filial::find()->andwhere(['=', 'id', 72])->one();
        $user               = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response           = ArrayHelper::getValue($user, 'body');
        $meliAccessToken    = $response->access_token;

        //Obter access_toke da conta duplicada do ML
        $filial_conta_duplicada             = Filial::find()->andwhere(['=', 'id', 98])->one();
        $user_conta_duplicada               = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada           = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada    = $response_conta_duplicada->access_token;
        
        //Obter access_toke da conta duplicada do ML
        $filial_MG4             = Filial::find()->andwhere(['=', 'id', 94])->one();
        $user_MG4               = $meli->refreshAccessToken($filial_MG4->refresh_token_meli);
        $response_MG4           = ArrayHelper::getValue($user_MG4, 'body');
        $meliAccessToken_MG4    = $response_MG4->access_token;
        
        $pedidos_mercado_livre = PedidoMercadoLivre::find() ->andWhere(['=','e_xml_subido',false])
                                                            ->andWhere(['=','e_pedido_autorizado',true])
                                                            ->andWhere(['=','e_pedido_enviado',false])
                                                            //->andWhere(['id' => [ 16927 ]])
                                                            ->andWhere(['=', 'status', 'paid'])
                                                            ->orderBy(['id'=>SORT_DESC])
                                                            ->all();
        
        foreach($pedidos_mercado_livre as $k => $pedido_mercado_livre){
            
            echo "\n".$k." - Pedido ID: ".$pedido_mercado_livre->id;
            fwrite($arquivo_log, "\n".$pedido_mercado_livre->id);       

            //if($pedido_mercado_livre->id != 34278) {continue;}

            //Analisa qual conta do ML o pedido pertence e obtem o access_token respectivo
            $token              = $meliAccessToken;
            $APP_KEY_OMIE       = $APP_KEY_OMIE_SP;
            $APP_SECRET_OMIE    = $APP_SECRET_OMIE_SP;
            /*if($pedido_mercado_livre->user_id == '435343067'){
                echo " - Conta Duplicada";
                $token = $meliAccessToken_conta_duplicada;
                $APP_KEY_OMIE       = $APP_KEY_OMIE_CONTA_DUPLICADA;
                $APP_SECRET_OMIE    = $APP_SECRET_OMIE_CONTA_DUPLICADA;
            }
            else{
                echo " - Conta Principal";
            }*/
            switch ($pedido_mercado_livre->user_id){
                case "435343067":
                    echo " - Conta Duplicada";
                    $token              = $meliAccessToken_conta_duplicada;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_CONTA_DUPLICADA;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_CONTA_DUPLICADA;
                    break;
                case "195972862":
                    echo " - Conta MG";
                    $token              = $meliAccessToken_MG4;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_MG4;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_MG4;
                    break;
                default:
                    echo " - Conta Principal";
                    $token              = $meliAccessToken;
                    $APP_KEY_OMIE       = $APP_KEY_OMIE_SP;
                    $APP_SECRET_OMIE    = $APP_SECRET_OMIE_SP;
            }

            $response_invoice = $meli->get("/users/".$pedido_mercado_livre->user_id."/invoices/shipments/".$pedido_mercado_livre->shipping_id."?access_token=".$token);
            
            //$response_invoice = $meli->get("/users/".$pedido_mercado_livre->user_id."/invoices/shipments/40704136889?access_token=".$token);
            //print_r($response_invoice); 
            //die;
            
            if($response_invoice['httpCode'] < 300){
                
                echo " - ";print_r($response_invoice["body"]->attributes->invoice_key);
                $nota_fiscal = NotaFiscal::find()->andWhere(["=", "chave_nf", $response_invoice["body"]->attributes->invoice_key])->one();
                if($nota_fiscal){
                    $pedido_mercado_livre->nota_fiscal_id = $nota_fiscal->id;
                }
                
                echo " - XML já subido anteriomente";
                fwrite($arquivo_log, ";XML já subido anteriomente");
                
                $pedido_mercado_livre->e_xml_subido = true;
                if($pedido_mercado_livre->save()){
                    echo " - Status alterado";
                    fwrite($arquivo_log, ";Status alterado");
                }
                else{
                    echo " - Status não alterado";
                    fwrite($arquivo_log, ";Status não alterado");
                }
                
                continue;
            }
            else{
                echo " - XML ainda não subido";
                fwrite($arquivo_log, ";XML ainda não subido");
            }
            
            //continue;
            
            $omie = new Omie(1, 1);
            
            //Consulta pedido no Omie, verificando se existe pedido criado com o código integração com o numero do pedido do ML
            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            $response_pedido = $omie->consulta_pedido("api/v1/geral/pedido/?JSON=",$body);
            //echo "==>\n\n"; print_r($response_pedido); echo "\n\n<=="; die;
            if($response_pedido["httpCode"] < 300){
                
                echo " - Pedido encontrado no Omie";
                fwrite($arquivo_log, ";Pedido encontrado no Omie");
                
                //Consulta Nota fiscal do pedido, se encontrar, baixa a XML da nota
                $body = [
                    "call" => "ConsultarNF",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                    ]
                ];
                $response_nota_fiscal = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=",$body);
                //echo "==>\n\n"; print_r($response_nota_fiscal); echo "\n\n<==";//die;
                if($response_nota_fiscal["httpCode"] < 300){
                    
                    echo " - Nota encontrada";
                    fwrite($arquivo_log, ";Nota encontrada");
                    
                    //Obtem a XML da nota para ser enviada para o ML
                    $body = [
                        "call" => "GetUrlNotaFiscal",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                        ]
                    ];
                    $response_url_nota_fiscal = $omie->consulta("/api/v1/produtos/notafiscalutil/?JSON=",$body);
                    //echo "==>\n\n"; print_r($response_url_nota_fiscal); echo "\n\n<=="; die;
                    if($response_nota_fiscal["httpCode"] < 300){
                 
                        echo " - XML gerada";
                        fwrite($arquivo_log, ";XML gerada");
                        
                        //Trecho que obtem a XML da nota para subir no ML
                        $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $xml_nota_fiscal = curl_exec($ch);
                        curl_close($ch);
                        //echo "==>\n\n"; print_r($xml_nota_fiscal); echo "\n\n<==";
                        //die;
                        
                        //Grava o xml da nota em um arquivo no servidor
                        $arquivo_nome = "/var/tmp/".ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF').".xml";
                        $arquivo = fopen($arquivo_nome, "a");
                        fwrite($arquivo, $xml_nota_fiscal);
                        fclose($arquivo);
                        
                        //Faz o envio do xml da nota para o ML
                        $url = "https://api.mercadolibre.com/shipments/".$pedido_mercado_livre->shipping_id."/invoice_data/?access_token=".$token."&siteId=MLB";
                        $ch = curl_init( $url );
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml_nota_fiscal );
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        $result = curl_exec($ch);
                        curl_close($ch);
                        //echo "==>\n\n"; print_r($result); echo "\n\n<==";
                        
                        $resultado = json_decode($result);
                        //echo "==>\n\n"; print_r($resultado); echo "\n\n<==";
                        
                        if($resultado->status < 400){
                            echo " - XML subida";
                            fwrite($arquivo_log, ";XML subida");
                            
                            echo " - ";print_r($response_nota_fiscal["body"]["compl"]["cChaveNFe"]);
                            $nota_fiscal = NotaFiscal::find()->andWhere(["=", "chave_nf", $response_nota_fiscal["body"]["compl"]["cChaveNFe"]])->one();
                            if($nota_fiscal){
                                $pedido_mercado_livre->nota_fiscal_id = $nota_fiscal->id;
                            }
                            
                            $pedido_mercado_livre->e_xml_subido = true;
                            if($pedido_mercado_livre->save()){
                                echo " - Status alterado";
                                fwrite($arquivo_log, ";Status alterado");
                            }
                            else{
                                echo " - Status não alterado";
                                fwrite($arquivo_log, ";Status não alterado");
                            }
                        }
                        else{
                            echo " - XML não subida";
                            fwrite($arquivo_log, ";XML não subida");
                            echo "==>\n\n"; print_r($resultado); echo "\n\n<==";
                        }
                    }
                    else{
                        echo " - XML não gerada";
                        fwrite($arquivo_log, ";XML não gerada");
                    }
                }
                else{
                    echo " - Nota não encontrada";
                    fwrite($arquivo_log, ";Nota não encontrad");
                }
            }
            else{
                echo " - Pedido não encotrado no Omie";
                fwrite($arquivo_log, ";Pedido não encotrado no Omie");
            }   
        }
        
        fwrite($arquivo_log, "\n\n".date("Y-m-d_H-i-s"));
        fclose($arquivo_log);
    }
}
