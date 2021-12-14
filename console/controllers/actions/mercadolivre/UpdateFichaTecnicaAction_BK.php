<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateFichaTecnicaAction extends Action
{
    public function run()
    {
        echo "INICIO da função - FICHA TECNICA - "; $date = date('Y-m-d H:i'); echo $date;

        $nome_arquivo = "produtos_ficha_tecnica_332.csv";
        $arquivo_log = fopen("/var/tmp/log_".$nome_arquivo, "a");
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        //fwrite($arquivo_log, "meli_id;nome;categoria_meli_id;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5f1eb688dd81f00006ef37b4-193724256');

        //print_r($user) . die();

        $response = ArrayHelper::getValue($user, 'body');


        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;


            $x = 0;
            $file = fopen("/var/tmp/".$nome_arquivo, 'r');
            while (($linha = fgetcsv($file, null, ';')) !== false) {


                //    if($linha[0] != "MLB1514765529"){continue;}



                echo "\n". $x++. " - " . $linha[0];

                $produto_filial = ProdutoFilial::find()->where(" meli_id = '".$linha[0]."' or meli_id_sem_juros = '".$linha[0]."' ")->one();

                $marca = "OPT";

                $produtoFilial = ProdutoFilial::find()->where(" meli_id = '".$linha[0]."' or meli_id_sem_juros = '".$linha[0]."' ")->one();

                $codigo_barras = "";

                if($produto_filial){
                    if(!is_null($produto_filial->produto->marca_produto_id)){
                        $marca = $produto_filial->produto->marcaProduto->nome;
                    }
                }


                if($produtoFilial){
                    if(!is_null($produtoFilial->produto->codigo_barras)){
                        $codigo_barras = $produtoFilial->produto->codigo_barras;
                    }
                }


                $nome = $linha[1];

                $lado_id 	= "-1";
                $lado_name	= null;
                if(strpos($nome,"ESQUERD")){
                    $lado_id    = "364128";
                    $lado_name  = "Esquerdo";
                }
                else{
                    if(strpos($nome,"DIREIT")){
                        $lado_id    = "364127";
                        $lado_name  = "Direito";
                    }
                }

                $posicao_id 	= "-1";
                $posicao_name	= null;
                if(strpos($nome,"DIANT")){
                    $posicao_id    = "405827";
                    $posicao_name  = "Dianteira";
                }
                else{
                    if(strpos($nome,"TRAS")){
                        $posicao_id    = "116949";
                        $posicao_name  = "Traseira";
                    }
                }

                $body = [
                    'attributes' =>[
                        [
                            'id'                    => 'BRAND',
                            'value_name'            => $marca,
                        ],

                        [
                            'id'                    => 'GTIN',
                            'name'                  => 'Código universal de produto',
                            'value_name'            => $codigo_barras,
                            'attribute_group_id'    => 'OTHERS',
                            'attribute_group_name'  => 'Outros',
                        ],



                        [
                            'id'                    => 'ORIGIN',
                            //     'value_id'            => '-1',
                            //      'value_name'            => 'null',
                            //------------------------------------------//
                                'value_name'            => 'Brasil',

                        ],



                    ]

                ];

                $response = $meli->put("items/{$linha[0]}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    echo " - ERRO Ficha Técnica";
                    fwrite($arquivo_log, ";Ficha técnica não alterada");
                } else {
                    echo " - OK Ficha Técnica";
                    fwrite($arquivo_log, ";Ficha Técnica alterada");
                }
                //print_r($response) . die();
            }
            fclose($file);
        }

        fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
        fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
        fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
