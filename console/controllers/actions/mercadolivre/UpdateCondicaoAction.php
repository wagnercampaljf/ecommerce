<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateCondicaoAction extends Action
{
    public function run()
    {
        $nome_arquivo = "/var/tmp/log_update_condicao_ml_".date("Y-m-d_H-i-s").".csv";

        $arquivo_log = fopen($nome_arquivo, "a");
        // Escreve no log
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");
        fwrite($arquivo_log, "produto_filial_id;status;produto_filial_conta_duplicada;status");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        echo "Começo da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => [84]])
            ->andWhere(['<>','id', 98])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                $produtoFilials = $filial   ->getProdutoFilials()
                                            ->andWhere(['is not','meli_id',null])
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => ['MLB1442476414']])
                                            //->andWhere(['produto_filial.id' => [242479,243000,248420,113446,340597,317245,320016,321316,242482,317212,139622,139629,244452]])
                    					    //->andWhere(['produto_filial.produto_id' => [236162]])
                                            ->joinWith('produto')
                                            ->andWhere(['=','produto.e_usado', true])
                                            ->orderBy('produto_filial.id')
                                            ->all();

                foreach ($produtoFilials as $k => $produtoFilial) {
                    echo "\n".$k ." - Conta principal: ". $produtoFilial->id ." - ".$produtoFilial->meli_id." - É usado: "; var_dump($produtoFilial->produto->e_usado);
                    fwrite($arquivo_log, "\n".$produtoFilial->id);
                    
                    $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->one();
                    
                    if($produto_filial_conta_duplicada){
                        echo " - Conta Duplicada: ".$produto_filial_conta_duplicada->id;
                    }
                    
                    echo " - ".(($produtoFilial->produto->e_usado)? "used" : "new");
                    echo " - ".$produtoFilial->produto->produtoCondicao->nome." - ".$produtoFilial->produto->produto_condicao_id."   ";
                    
                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    if($produtoFilial->produto->produtoCondicao){
                        switch ($produtoFilial->produto->produtoCondicao->meli_id){
                            case "new":
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                                break;
                            case "used":
                                $condicao = "used";
                                $condicao_id = "2230581";
                                $condicao_name = "Usado";
                                break;
                            case "recondicionado":
                                $condicao = "new";
                                $condicao_id = "2230582";
                                $condicao_name = "Recondicionado";
                                break;
                            default:
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                        }
                    };
                    
                    $body = [
                        "condition" => $condicao,
                        'attributes' =>[
                            [
                                'id'                    => 'ITEM_CONDITION',
                                'name'                  => 'Condição do item',
                                'value_id'              => $condicao_id,
                                'value_name'            => $condicao_name,
                                'value_struct'          => null,
                                'values'                => [[
                                    'id'    => $condicao_id,
                                    'name'  => $condicao_name,
                                    'struct'=> null,
                                ]],
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                            ]
                        ]
                    ];                    
                    
                    //print_r($body);
                    
                    $response = $meli->put("items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken, $body, [] );
                    if ($response['httpCode'] >= 300) {
                        print_r($response);
                        echo " - ERROR Condição";
                        fwrite($arquivo_log, ";Condição não alterada");
                    }
                    else {
        			    echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                        echo " - OK Condição";
		                fwrite($arquivo_log, ";Condição alterada");
                    }
                    
                    //Alteração SEM JUROS
                    if($produtoFilial->meli_id_sem_juros != null && $produtoFilial->meli_id_sem_juros != ""){
                        $response = $meli->put("items/{$produtoFilial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            print_r($response);
                            echo " - SEM JUROS ERROR Condição";
                            fwrite($arquivo_log, ";SEM JUROS Condição não alterada");
                        }
                        else {
                            echo " - SEM JUROS OK Condição";
			                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            fwrite($arquivo_log, ";SEM JUROS Condição alterada");
                        }
                    }
                    //Alteração SEM JUROS
                    
                    if($produto_filial_conta_duplicada){
                        fwrite($arquivo_log, ";".$produto_filial_conta_duplicada->id);
                        
                        $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id}?access_token=" . $meliAccessToken_conta_duplicada, $body, [] );
                        if ($response['httpCode'] >= 300) {
                            print_r($response);
                            echo " - ERROR Condição (Conta Duplicada)";
                            fwrite($arquivo_log, ";Condição não alterada (Conta Duplicada)");
                        }
                        else {
                            echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                            echo " - OK Condição (Conta Duplicada)";
                            fwrite($arquivo_log, ";Condição alterada (Conta Duplicada)");
                        }
                        
                        //Alteração SEM JUROS
                        if($produto_filial_conta_duplicada->meli_id_sem_juros != null && $produto_filial_conta_duplicada->meli_id_sem_juros != ""){
                            $response = $meli->put("items/{$produto_filial_conta_duplicada->meli_id_sem_juros}?access_token=" . $meliAccessToken_conta_duplicada, $body, [] );
                            if ($response['httpCode'] >= 300) {
                                print_r($response);
                                echo " - SEM JUROS ERROR Condição (Conta Duplicada)";
                                fwrite($arquivo_log, ";SEM JUROS Condição não alterada (Conta Duplicada)");
                            }
                            else {
                                echo " - SEM JUROS OK Condição (Conta Duplicada)";
                                echo "\nLink: ".ArrayHelper::getValue($response, 'body.permalink');
                                fwrite($arquivo_log, ";SEM JUROS Condição alterada (Conta Duplicada)");
                            }
                        }
                        //Alteração SEM JUROS
                    }
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
