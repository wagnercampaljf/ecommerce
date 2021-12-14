<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 28/08/17
 * Time: 15:33
 */

namespace console\controllers\actions\skyhub;

use common\models\Filial;
use common\models\ProdutoFilial;
use Yii;
use yii\base\Action;
use console\models\SkyhubClient;

class CreateAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/produtos_b2w_ignorar.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $skyhub = new SkyhubClient();

	//Arquivo de log
	$arquivo_log = fopen("/var/tmp/log_skyhub_create_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;criar_alterar;status");

        $filials = Filial::find()
            //->andWhere(['IS', 'integrar_b2w', true])
            ->andWhere(['id' => [38, 43, 60, 72, 97]])
    	    //->andWhere(['id' => [38]])
    	    //->andWhere(['id' => [97]])
    	    //->andWhere(['id' => [60]])
    	    //->andWhere(['id' => [72]])
    	    //->andWhere(['id' => [86]])
            //->andWhere(['id' => [77,86,62,76,92,96]])
            //->limit(1)
    	    ->orderBy('id')
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
          	echo "Inicio da filial: " . $filial->nome . "\n";
		$produtoFilials = $filial->getProdutoFilials()	//->hasImage()
                						//->andWhere(['>','quantidade',0])
						                //->andWhere(['IS NOT','status_b2w',null])
        							->andWhere(['=','status_b2w',true])
        							//->andWhere(['=', 'produto_filial.id', 392998])
        							//->where(' produto_filial.id in (201381) and id in (select distinct produto_filial_id from valor_produto_filial) ')
			 		                    	->where(' id in (select distinct produto_filial_id from valor_produto_filial) ')
								->orderBy('id')
						                ->all();

            /* @var $produtoFilial ProdutoFilial */
            foreach ($produtoFilials as $k => $produtoFilial) {
        		echo "\n".$k." - ".$produtoFilial->id;
        		
        		if(($k <= 23370) && $produtoFilial->filial_id = 38){
        		    continue;
        		}
        		
        		//if($produtoFilial->id < 335936 && $produtoFilial->filial_id = 38){
        		//if($produtoFilial->id < 194312 && $produtoFilial->filial_id = 97){
        		//if($produtoFilial->id < 37389 && $produtoFilial->filial_id = 60){
        		//if($produtoFilial->id < 102218 && $produtoFilial->filial_id = 72){
        		//	continue;
        		//}
        
        		fwrite($arquivo_log, "\n".$produtoFilial->id.";");
        
        		
        		/*$ignorar = false;
        		foreach ($LinhasArray as $i => &$linhaArray){
        		    if($produtoFilial->id == $linhaArray[0]){
        		        echo " - Ignorado";
        		        fwrite($arquivo_log, "Ignorado");
        		        $ignorar = true;
        		        break;
        		    }
        		}
        		
        		if($ignorar){
        		    continue;
        		}*/

        		
        		/*$valor_mais_recente = $produtoFilial->valorMaisRecente;
        
                     	if (is_null($valor_mais_recente)) {
                            Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                                'error_yii');
        		    fwrite($arquivo_log, ";Sem Valor");
                            continue;
                        }
        
        		print_r($valor_mais_recente->valor);*/
        		//continue;
        
        		//print_r($produtoFilial->getSkyhubData()->product);die;
        		//print_r($produtoFilial->getSkyhubData());

        		$dados = $produtoFilial->getSkyhubData();

        		$dados['product']['description'] = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/skyhub/produto.php', ['produto' => $produtoFilial]);
        
        		$preco        = $dados['product']['price'];
        		$peso 		  = $dados['product']['weight'];
        		$altura       = $dados['product']['height'];
                $largura	  = $dados['product']['width'];
                $profundidade = $dados['product']['length'];
        
        		echo "\nPreco: "; print_r($dados['product']['price']);
        		//If que verifica se o produto vai pelo B2W Entregas, se for, adiciona 26 reais
        		if($peso < 30 && $preco < 10000 && $altura < 70 && $largura < 70 && $profundidade < 70){
        			$dados['product']['price'] += 26;
        			$dados['product']['promotional_price'] += 26;
        		}
                //echo "\nPreco: "; print_r($dados['product']['price']);
                //echo "\nPeso: "; print_r($dados['product']['weight']);
                //echo "\nAltura: "; print_r($dados['product']['height']);
                //echo "\nLargura: "; print_r($dados['product']['width']);
                //echo "\nProfundidade: "; print_r($dados['product']['length']);
        		echo " - Quantidade: "; print_r($dados['product']['qty']);
        		
        		if($dados['product']['price'] > 300){
        		    echo " - Preço maior que 300";
        		    continue;
        		}
        		
        		echo " - SUBIR";
        		
        		//print_r($dados);

                if ($produtoFilial->status_b2w === null) {
                    
                    echo " - criar";
        		    fwrite($arquivo_log, "Criar;");
                    continue;
        		    //$response = $skyhub->products()->create($dados);
                    //$response = $skyhub->products()->create($produtoFilial->getSkyhubData());
                } 
                else {
        		    echo " - atualizar";
        		    fwrite($arquivo_log, "Alterar;");
        		    //print_r($dados);
                    $response = $skyhub->products()->update($produtoFilial->id, $dados);
        		    //print_r($response);
                    //$response = $skyhub->products()->update($produtoFilial->id, $produtoFilial->getSkyhubData());
                }
        
                if (!$response->isOk) {
        		    //print_r($response);
                    echo "Erro ao cadastrar Produto x Filial de id: $produtoFilial->id\n";
        		    fwrite($arquivo_log, ";Error");
                } 
                else {
        		    echo " - ok \n";
                    $produtoFilial->status_b2w = true;
                    $produtoFilial->save();
        		    fwrite($arquivo_log, ";OK");
                }
            }
	       
            echo "Fim da filial: " . $filial->nome . "\n";
        }

	   fclose($arquivo_log);

        echo "Finalizado";
    }

}
