<?php

namespace console\controllers\actions\mercadolivre;

use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;


class AnaliseProdutosVannucciAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;
        
        if (file_exists("/var/tmp/log_analise_produtos_vannucci.csv")){
            unlink("/var/tmp/log_analise_produtos_vannucci.csv");
        }
        $arquivo_log = fopen("/var/tmp/log_analise_produtos_vannucci.csv", "a");
        
        $LinhasArrayAntigo = Array();
        $file = fopen('/var/tmp/log_comparar_planilhas_vannucci_antiga.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayAntigo[] = $line;
        }
        fclose($file);
        
        $LinhasArrayNovo = Array();
        $file = fopen('/var/tmp/log_comparar_planilhas_vannucci_nova.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayNovo[] = $line;
        }
        fclose($file);

        $produtosFilial = ProdutoFilial::find() ->joinWith('produto')
                                                ->andWhere(['=','filial_id',38])
                                                ->all();

        fwrite($arquivo_log, "codigo_fabricante;codigo_globo;status_antiga;status_novo;conta_velha_ausente_conta_nova;conta_nova_ausente_velha\n");
                                                
        foreach($produtosFilial as $i => $produtoFilial){
            
            if($i < 0){
                continue;
            }
            
            echo "\n".$i." - ".$produtoFilial->id." - ".$produtoFilial->produto->codigo_global;
            fwrite($arquivo_log,$produtoFilial->id.";".$produtoFilial->produto->codigo_fabricante.";".$produtoFilial->produto->codigo_global);
            
            $status_planilha_antiga     = "Não encontrado planilha antiga";
            $status_planilha_nova       = "Não encontrado planilha nova";
            $status_antiga_ausente_nova = "";
            $status_nova_ausente_antiga = "";
            
            foreach ($LinhasArrayAntigo as $k => &$LinhaArrayAntigo ){
                if($LinhaArrayAntigo[1] == $produtoFilial->produto->codigo_fabricante){
                    echo "\n        PLanilha Antiga: ".$k." - ".$LinhaArrayAntigo[0]." - ".$LinhaArrayAntigo[2];
                    $status_planilha_antiga     = "Produto encontrado Planilha Antiga";
                    $status_antiga_ausente_nova = $LinhaArrayAntigo[0];
                    break;
                }
                $codigo_reduzido = explode("-",$LinhaArrayAntigo[1]);
                if($codigo_reduzido[0] == $produtoFilial->produto->codigo_fabricante){
                    echo "\n        PLanilha Antiga: ".$k." - ".$LinhaArrayAntigo[0]." - ".$LinhaArrayAntigo[2];
                    $status_planilha_antiga     = "Produto encontrado Planilha Antiga REDUZIDO";
                    $status_antiga_ausente_nova = $LinhaArrayAntigo[0];
                    break;
                }
            }
            
            foreach ($LinhasArrayNovo as $k => &$LinhaArrayNovo ){
                if($LinhaArrayNovo[1] == $produtoFilial->produto->codigo_fabricante){
                    echo "\n        PLanilha Nova: ".$k." - ".$LinhaArrayNovo[0]." - ".$LinhaArrayNovo[2];
                    $status_planilha_nova     = "Produto encontrado Planilha Nova";
                    $status_nova_ausente_antiga= $LinhaArrayNovo[0];
                    break;
                }
                $codigo_reduzido = explode("-",$LinhaArrayNovo[1]);
                if($codigo_reduzido[0] == $produtoFilial->produto->codigo_fabricante){
                    echo "\n        PLanilha Nova: ".$k." - ".$LinhaArrayNovo[0]." - ".$LinhaArrayNovo[2];
                    $status_planilha_nova     = "Produto encontrado Planilha Nova REDUZIDO";
                    $status_nova_ausente_antiga= $LinhaArrayNovo[0];
                    break;
                }
            }
            
            fwrite($arquivo_log, ";".$status_planilha_antiga.";".$status_planilha_nova.";".$status_antiga_ausente_nova.";".$status_nova_ausente_antiga."\n");
            
        }
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        /*$LinhasArray = Array();
        $file = fopen('/var/tmp/ListaCompletaVannucci05-08-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            echo "\n".$k." - ".$linhaArray[0]." - ".$linhaArray[2];
            
            if($k <=1 100){
                continue;
            }
            
            $produtosFilial = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['=','filial_id',38])
                                                    ->andWhere(['=','produto.codigo_fabricante',$linhaArray[0]])
                                                    ->all();
            
            if($produtosFilial){
                foreach($produtosFilial as $i => $produtoFilial){
                    echo "\n    ".$i." - ".$produtoFilial->id;
                    $produtosDuplicado = ProdutoFilial::find()   ->joinWith('produto')
                                                                ->andWhere(['=','filial_id',38])
                                                                ->andWhere(['=','produto.codigo_fabricante',$produtoFilial->produto->codigo_fabricante])
                                                                ->andWhere(['<>','produto_id',$produtoFilial->produto_id])
                                                                ->all();
                    
                    if($produtosDuplicado){
                        foreach($produtosDuplicado as $i => $produtoDuplicado){
                            echo "\n    ".$i." - Duplicado - ".$produtoDuplicado->id;
                        }
                    }
                }
            }
            else{
                $codigo_fabricante = explode("-",$linhaArray[0]);
                echo " - ".$codigo_fabricante[0];
                $produtosFilial = ProdutoFilial::find() ->joinWith('produto')
                                                        ->andWhere(['=','filial_id',38])
                                                        ->andWhere(['=','produto.codigo_fabricante',$codigo_fabricante[0]])
                                                        //->andWhere(['like','produto.codigo_global',$linhaArray[2]])
                                                        ->all();
                
                if($produtosFilial){
                    foreach($produtosFilial as $i => $produtoFilial){
                        $produtosDuplicado = ProdutoFilial::find()   ->joinWith('produto')
                                                                    ->andWhere(['=','filial_id',38])
                                                                    ->andWhere(['=','produto.codigo_fabricante',$produtoFilial->produto->codigo_fabricante])
                                                                    ->andWhere(['<>','produto_id',$produtoFilial->produto_id])
                                                                    ->all();
                        
                        if($produtosDuplicado){
                            foreach($produtosDuplicado as $i => $produtoDuplicado){
                                echo "\n    ".$i." - Fabricante Menor Duplicado - ".$produtoDuplicado->id;
                            }
                        }
                    }
                }
            }
        }*/
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        /*// Escreve no log
        $arquivo_log = fopen("/var/tmp/analise_concorrente_Ranchao_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "Nome;Preço;Data Criação;Quantidade Vendida;URL\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');
        //print_r($response); die;
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            print_r($meliAccessToken); die;
            
            //360447035 -> ALGOMAISPECAS
            for($x=0;$x<=2000;$x+=50){
                echo "\n".$x;
                $response_order = $meli->get("sites/MLB/search?seller_id=45927183&search_type=scan&offset=".$x."&access_token=" . $meliAccessToken);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $meli_itens){
                    $response_itens = $meli->get("/items/".ArrayHelper::getValue($meli_itens, 'id'));
                    
                    echo "\n".$k." - ".ArrayHelper::getValue($response_itens, 'body.sold_quantity')." - ".ArrayHelper::getValue($response_itens, 'body.price');
                    
                    fwrite($arquivo_log,ArrayHelper::getValue($response_itens, 'body.title').";".
                                        ArrayHelper::getValue($response_itens, 'body.price').";".
                                        ArrayHelper::getValue($response_itens, 'body.start_time').";".
                                        ArrayHelper::getValue($response_itens, 'body.sold_quantity').";".   
                                        ArrayHelper::getValue($response_itens, 'body.permalink').";".
                                        "\n");
                }
            }
        }
    
        fclose($arquivo_log);*/
        
        echo "\n\nFIM!";
    }
}
