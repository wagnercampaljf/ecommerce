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

class CreatePrecoAction extends Action
{
    public function run()
    {
        echo "Criando produtos...\n\n";
        $skyhub = new SkyhubClient();

	//Arquivo de log
	$arquivo_log = fopen("/var/tmp/log_skyhub_create_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;criar_alterar;status");

        $filials = Filial::find()
//            ->andWhere(['IS', 'integrar_b2w', true])
            ->andWhere(['id' => [60, 72]])
//		->andWhere(['id' => [77,86,62,76,92,96]])
//            ->limit(1)
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
          	echo "Inicio da filial: " . $filial->nome . "\n";
 		$produtoFilials = $filial->getProdutoFilials()	//->hasImage()
                						//->andWhere(['>','quantidade',0])
						                //->andWhere(['IS NOT','status_b2w',null])
								//->andWhere(['=','status_b2w',true])
								//->andWhere(['produto_filial.id' => [35264]])
								->orderBy('id')
						                ->all();

            /* @var $produtoFilial ProdutoFilial */
            foreach ($produtoFilials as $k => $produtoFilial) {
		echo "\n".$k." - ".$produtoFilial->id;
//		continue;

		fwrite($arquivo_log, "\n".$produtoFilial->id.";");

             	if (is_null($produtoFilial->valorMaisRecente)) {
                    Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                        'error_yii');
		    fwrite($arquivo_log, ";Sem Valor");
                    continue;
                }

		//print_r($produtoFilial->getSkyhubData());

                if ($produtoFilial->status_b2w === null) {
//                    echo " - criar";
//		    fwrite($arquivo_log, "Criar;");
 //                   $response = $skyhub->products()->create($produtoFilial->getSkyhubData());
                } else {
		    echo " - atualizar";
		    fwrite($arquivo_log, "Alterar;");
                    $dados = $produtoFilial->getSkyhubDataPrecoQuantidade();
                    $dados['product']['description'] = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/skyhub/produto.php', ['produto' => $produtoFilial]);
                    $response = $skyhub->products()->update($produtoFilial->id, $dados);
                    //$response = $skyhub->products()->update($produtoFilial->id, $produtoFilial->getSkyhubData());

		    if (!$response->isOk) {
	                    print_r($response);
	                    echo "Erro ao cadastrar Produto x Filial de id: $produtoFilial->id\n";
	                    fwrite($arquivo_log, ";Error");
                    } else {
	                    echo " - ok \n";
	                    $produtoFilial->status_b2w = true;
	                    $produtoFilial->save();
	                    fwrite($arquivo_log, ";OK");
                    }
                }
		continue; /////////////
                if (!$response->isOk) {
		    print_r($response);
                    echo "Erro ao cadastrar Produto x Filial de id: $produtoFilial->id\n";
		    fwrite($arquivo_log, ";Error");
                } else {
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
