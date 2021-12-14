<?php
namespace console\controllers\actions\mercadolivre;

use common\models\Produto;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;

class AtualizarNCMAction extends Action
{

    public function run()
    {
        
        /*$arquivo_origem = '/var/tmp/NCM_31-08-2021';
                
        $file = fopen($arquivo_origem.".csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $destino = $arquivo_origem."_precificado.csv";
        if (file_exists($destino)){
            unlink($destino);
        }
        
        $arquivo_destino = fopen($destino, "a");
        
        foreach ($LinhasArray as $i => &$linhaArray){
            
            echo "\n".$i." - ".$linhaArray[0]. " - ".$linhaArray[1];
            
            if($i == 0){
                continue;
            }
            
            $produto = Produto::find() ->andWhere(["=", "id", $linhaArray[0]])->one();
           
            if($produto){

                if($produto->codigo_montadora == $linhaArray[1]){
                    echo " - mesmo NCM";
                    continue;
                }
                
                $produto->codigo_montadora = $linhaArray[1];
                if($produto->save()){
                    echo " - Produto salvo";
                }
                else{
                    echo " - Produto não salvo";
                }
            }
        }
        
        die;*/
            
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $produtos = Produto::find() ->andWhere(["is not", "codigo_montadora", null ])
                                    ->andWhere(["<>", "codigo_montadora", "" ])
                                    ->orderBy(["id" => SORT_ASC ])
                                    //->limit(10)
                                    ->all();

        foreach ($produtos as $k => $produto) {
            echo "\n" . $k . " - " . $produto->id . " - " . $produto->codigo_montadora;
            
            if($k <= 133903){
                echo " - Pular";
                continue;
            }

            $produtos_filiais = ProdutoFilial::find()   ->where(" produto_id = $produto->id and (meli_id is not null or meli_id_sem_juros is not null or meli_id_flex is not null or meli_id_full is not null) ")
                                                        ->orderBy(["id" => SORT_ASC])
                                                        ->all();

            foreach ($produtos_filiais as $i => $produto_filial) {
                echo "\n     " . $i . " - " . $produto_filial->id . " - " . $produto_filial->filial_id . "(meli_id: $produto_filial->meli_id, meli_id_sem_juros: $produto_filial->meli_id_sem_juros, meli_id_full: $produto_filial->meli_id_full, meli_id_flex: $produto_filial->meli_id_flex)";

                if(is_null($produto_filial->filial->refresh_token_meli)){
                    echo " - FILIAL FORA DO ML";
                    continue;
                }

                $user = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
                $response = ArrayHelper::getValue($user, 'body');
                $meliAccessToken = $response->access_token;
                //print_r($meliAccessToken);

                $user_array = explode("-", $produto_filial->filial->refresh_token_meli);
                $user_id    = $user_array[2];

                $meli_id_array = [];

                if (! is_null($produto_filial->meli_id)) {
                    $meli_id_array[] = [
                        "tipo"      => "meli_id",
                        "meli_id"   => $produto_filial->meli_id,
                    ];
                }
                if (! is_null($produto_filial->meli_id_sem_juros)) {
                    $meli_id_array[] = [
                        "tipo"      => "meli_id_sem_juros",
                        "meli_id"   => $produto_filial->meli_id,
                    ];
                }
                if (! is_null($produto_filial->meli_id_full)) {
                    $meli_id_array[] = [
                        "tipo"      => "meli_id_full",
                        "meli_id"   => $produto_filial->meli_id,
                    ];
                }
                if (! is_null($produto_filial->meli_id_flex)) {
                    $meli_id_array[] = [
                        "tipo"      => "meli_id_flex",
                        "meli_id"   => $produto_filial->meli_id,
                    ];
                }
                
                foreach($meli_id_array as $k => $meli_id){
                    $body = [
                        'attributes' => [
                            [
                                'id' => 'SKU',
                                'name' => 'SKU',
                                'value_id' => null,
                                'value_name' => $produto_filial->id."_".$meli_id["tipo"],
                                'value_struct' => null,
                                'values' => [
                                    [
                                        'id' => null,
                                        'name' => $produto_filial->id."_".$meli_id["tipo"],
                                        'struct' => null
                                    ]
                                ],
                                'attribute_group_id' => "OTHERS",
                                'attribute_group_name' => "Outros"
                            ]
                        ]
                    ];
                    $response = $meli->put("items/{$meli_id["meli_id"]}?access_token=" . $meliAccessToken, $body, []);
                    // print_r($response);
                    // die;

                    $body = [
                        "sku" => $produto_filial->id."_".$meli_id["tipo"],
                        //"seller_id" => "435343067",
                        "title"     => utf8_encode(substr($produto->nome, 0,60)),
                        "type" => "single",
                        "tax_information" => [
                            "ncm" => $produto->codigo_montadora,
                            "origin_type" => "reseller",
                            "origin_detail" => "0",
                            //"tax_rule_id" => null,//"42228",
                            "csosn" => "102",
                            //"cest"  => "0105700",
                            "ean"   => "",
                            "empty" => false,
                        ],
                        "register_type" => "final"
                    ];

                    if($user_id == "193724256"){
                        $body ["tax_information"]["tax_rule_id"] = "42228";
                    }
                    
                    if($user_id == "195972862"){
                        $body ["tax_information"]["tax_rule_id"] = "70921";
                    }

                    //print_r($body);
                    
                    //$response_item = $meli->get("/items/" . $produto_filial->meli_id . "?access_token=" . $meliAccessToken);
                    //print_r($response_item);

                    $response = $meli->post("items/fiscal_information?access_token=" . $meliAccessToken, $body, []);
                    //print_r($response);

                    if ($response['httpCode'] >= 300) {
                        // fwrite($arquivo_log, "\n".$produtoFilial->id.";".$preco.";Preço não alterado;");
                        print_r($response);
                        echo " - \n         Produto Não atualizado";
                    } else {
                        // fwrite($arquivo_log, "\n".$produtoFilial->id.";".$preco.";Preço alterado;");
                        echo " - \n         Produto atualizado";
                    }
                }
            }
            //die;
        }
    }
}
