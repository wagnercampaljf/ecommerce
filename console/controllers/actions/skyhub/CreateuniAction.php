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

class CreateuniAction extends Action
{
    public function run($global_id)
    {
        echo "Criando produtos...\n\n";
        $skyhub = new SkyhubClient();
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'integrar_b2w', NULL])
//            ->andWhere(['id' => 60])
//            ->limit(1)
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {
	        $produtoFilials = $filial->getProdutoFilials()->hasImage()
                //->andWhere(['>','quantidade',0])
                ->andWhere(['IS NOT','status_b2w',null])
                ->andWhere(['=','produto_filial.id',$global_id])
                ->all();

            /* @var $produtoFilial ProdutoFilial */
            foreach ($produtoFilials as $k => $produtoFilial) {
                echo "achou a filial";
		if (is_null($produtoFilial->valorMaisRecente)) {
                    Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                        'error_yii');
                    continue;
                }

                if ($produtoFilial->status_b2w === null) {
                    echo $k . " - " . $produtoFilial->produto_id . " - ok\n";
                    $response = $skyhub->products()->create($produtoFilial->getSkyhubData());
                } else {
                    echo $k . " - " . $produtoFilial->produto_id . " - up\n";
		    $dados = $produtoFilial->getSkyhubData();
		    $dados['product']['description'] = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/skyhub/produto.php', ['produto' => $produtoFilial]);
                    print_r($dados);
		    $response = $skyhub->products()->update($produtoFilial->id, $dados);//$produtoFilial->getSkyhubData());
                }

                if (!$response->isOk) {
                    echo "Erro ao cadastrar Produto x Filial de id: $produtoFilial->id\n";
                } else {
                    $produtoFilial->status_b2w = true;
                    $produtoFilial->save();
                }
            }
        }
        echo "Finalizado";
    }

}
