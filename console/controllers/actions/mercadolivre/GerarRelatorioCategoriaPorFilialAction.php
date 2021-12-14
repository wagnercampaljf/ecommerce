<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class GerarRelatorioCategoriaPorFilialAction extends Action
{
    public function run($filial_id)
    {

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio Frete Produtos - Conta Antiga";
        
        echo "\n\nComeço da rotina de atualização dos produtos no ML";
        $date = date('Y-m-d H:i');
        echo $date;

        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [$filial_id]])
    	    ->orderBy('id')
            ->all();

        foreach ($filials as $filial) {
            echo "Inicio da filial: " . $filial->nome . "\n";
            
            $nome_arquivo = "/var/tmp/log_categoria_por_filial_ml_".str_replace(" ", "", $filial->nome)."_".date("Y-m-d_H-i-s").".csv";
            $arquivo_log = fopen($nome_arquivo, "a");
            // Escreve no log
            fwrite($arquivo_log, "produto_filial_id;categoria_meli_id;categoria_nome;status_me;status_ml;encontrado_ml;subcategoria_id;subcategoria_nome;codigo_global;codigo_fabricante;nome;quantidade\n");
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');

            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

                $meliAccessToken = $response->access_token;
                echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;

                $produto_filiais = $filial  ->getProdutoFilials()
                                            //->andWhere(['is not','meli_id',null])
                                            ->where(" meli_id is not null and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
                                            //->andWhere(['>','quantidade',0])
                                            //->andWhere(['produto_filial.meli_id' => []])
                    					    //->andWhere(['produto_filial.id' => []])
                    					    //->joinWith('produto')
                                            //->andWhere(['like','produto.nome', 'CAPA PORCA'])
                                            //->andWhere(['=','e_preco_alterado',true])
                                            //->andWhere(['is not', 'meli_id_sem_juros', null])
                                            ->orderBy(['produto_filial.id' => SORT_ASC])
                                            ->all();

                foreach ($produto_filiais as $k => $produto_filial) {
                    if($k%5000==0){
                        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
                        $response = ArrayHelper::getValue($user, 'body');
                        $meliAccessToken = $response->access_token;
                        
                        echo "\n\nTOKEN PRINCIPAL:" . $meliAccessToken;
                    }
                    
                    echo "\n ==> ".$k." - ".$produto_filial->id;
                    fwrite($arquivo_log, "\n".$produto_filial->id);

        		    /*if($produto_filial->filial_id == 96 && $produto_filial->id <= 453976){
                        continue;
        		    }*/
                    
                    $response_tipo_anuncio = $meli->get('https://api.mercadolibre.com/items/'.$produto_filial->meli_id.'?access_token='.$meliAccessToken);
                    
                    $status_me = false;
                    if ($response_tipo_anuncio['httpCode'] >= 300) {
                        echo " - produto nao encontrado no ML, tipo de anuncio";
                        
                        fwrite($arquivo_log, ";;;;;Produto não encontrado ML;".$produto_filial->produto->subcategoria_id.";".$produto_filial->produto->subcategoria->nome.";".$produto_filial->produto->codigo_global.";".$produto_filial->produto->codigo_fabricante.";".$produto_filial->produto->nome.";".$produto_filial->quantidade);
                    }
                    else {
                        
                        //Se o anuncio não for por ME, remover o frete.
                        //print_r($response_tipo_anuncio);die;
                        $response_categoria = $meli->get('https://api.mercadolibre.com/categories/'.ArrayHelper::getValue($response_tipo_anuncio, 'body.category_id'));
                        //print_r($response_categoria);die;
                        if ($response_categoria['httpCode'] >= 300) {
                            echo " - categoria não encontrada";
                            fwrite($arquivo_log, ";Categoria não encontrada;;;".ArrayHelper::getValue($response_tipo_anuncio, 'body.status').";Produto encontrado;".$produto_filial->produto->subcategoria_id.";".$produto_filial->produto->subcategoria->nome.";".$produto_filial->produto->codigo_global.";".$produto_filial->produto->codigo_fabricante.";".$produto_filial->produto->nome.";".$produto_filial->quantidade);
                        }
                        else{
                            
                            foreach(ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes') as $modo_envio){
                                if($modo_envio == "me2"){
                                    $status_me = true;
                                    break;
                                }
                            }
                            
                            echo " - categoria encontrada";
                            fwrite($arquivo_log, ";".ArrayHelper::getValue($response_tipo_anuncio, 'body.category_id').";".ArrayHelper::getValue($response_categoria, 'body.name').";".(($status_me?"ME":"Sem ME")).";".ArrayHelper::getValue($response_tipo_anuncio, 'body.status').";Produto encontrado;".$produto_filial->produto->subcategoria_id.";".$produto_filial->produto->subcategoria->nome.";".$produto_filial->produto->codigo_global.";".$produto_filial->produto->codigo_fabricante.";".$produto_filial->produto->nome.";".$produto_filial->quantidade);
                        }
                    }
                }
            }
        echo "Fim da filial: " . $filial->nome . "\n";
        }

    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
