<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\Imagens;
use common\models\Filial;

class RetirarMercadoEnviosAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_por_categoria".date("Y-m-d_H-i-s").".csv", "a");
        //fwrite($arquivo_log, "Conta Duplicada\n\nmeli_id;nome;categoria\n");
        fwrite($arquivo_log, "Conta Principal\n\nmeli_id;nome;categoria\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response_token = ArrayHelper::getValue($user, 'body');
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=', 'id', 98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');

        if (is_object($response_token) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response_token->access_token;
            $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

            $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                        ->andWhere(['id' => [62, 76, 77, 78, 84]])
                                        ->andWhere(['<>', 'id', 98])
                                        ->orderBy('id')
                                        ->all();
            
            foreach ($filials as $filial) {
                echo "Inicio da filial: " . $filial->nome . "\n";

                $produtos_filiais = ProdutoFilial::find()   //->joinWith('produto')
                                                            //->andWhere(['like', 'upper(produto.nome)', "LENTE"])
                                                            ->andWhere(['is not', 'meli_id', null])
                                                            //->andWhere(['produto_filial.id' => [206940, 302495]])
                                                            //->andWhere(['produto_id' => [250070]])
                                                            ->andWhere(['=', 'filial_id', $filial->id])
                                                            //->andWhere(['=', 'meli_id', 'MLB1157884906'])
                                                            ->orderBy('produto_filial.id')
                                                            ->all();
                
                foreach($produtos_filiais as $k => $produto_filial){
                    
                    if($k%5000==0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                        
                        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
                        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
                        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;
                        
                        echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                        echo "\nTOKEN DUPLICADA:" . $meliAccessToken_conta_duplicada;
                    }
                    
                    echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id;
                    
                    /*switch ($produto_filial->filial_id){
                        case 69:
                            $preco = round($produto_filial->getValorMercadoLivre(), 2);
                            
                            if($preco > 500){
                                continue;
                            }
                            
                            break;
                        default:
                            echo " - nenhuma exceção";
                    }*/
                    
                    $body = ["category_id" => "MLB191833"];
                    
                    $response = $meli->put("items/{$produto_filial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                    if ($response['httpCode'] >= 300) {
                        echo " - error(Principal)";
                    }
                    else{
                        echo " - OK(Principal)";
                    }
                    
                    if($produto_filial->meli_id_sem_juros <> null and $produto_filial->meli_id_sem_juros <> ""){
                        $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - error(Sem Juros)";
                        }
                        else{
                            echo " - OK(Sem Juros)";
                        }
                    }
                    
                    if($produto_filial->meli_id_full <> null and $produto_filial->meli_id_full <> ""){
                        $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            echo " - error(Sem Juros)";
                        }
                        else{
                            echo " - OK(Sem Juros)";
                        }
                    }
                    
                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produto_filial->id])->one();
                    
                    if($produto_filial_conta_duplicada){
                        if($produto_filial_conta_duplicada->meli_id <> null and $produto_filial_conta_duplicada->meli_id <> ""){
                            $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - error(Conta Duplicada)";
                                print_r($response);
                            }
                            else{
                                echo " - OK(Conta Duplicada)";
                            }
                        }
                        
                        if($produto_filial_conta_duplicada->meli_id_sem_juros <> null and $produto_filial_conta_duplicada->meli_id_sem_juros <> ""){
                            $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - error(Conta Duplicada-Sem Juros)";
                            }
                            else{
                                echo " - OK(Conta Duplicada-Sem Juros)";
                            }
                        }
                        
                        if($produto_filial_conta_duplicada->meli_id_full <> null and $produto_filial_conta_duplicada->meli_id_full <> ""){
                            $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_full}?access_token=" . $meliAccessToken_conta_duplicada, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                echo " - error(Conta Duplicada-Sem Juros)";
                            }
                            else{
                                echo " - OK(Conta Duplicada-Sem Juros)";
                            }
                        }
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 