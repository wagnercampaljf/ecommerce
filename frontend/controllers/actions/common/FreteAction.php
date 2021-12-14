<?php

namespace frontend\controllers\actions\common;

use common\models\Filial;
use common\models\ProdutoFilial;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\db\Query;

class FreteAction extends Action
{
    public $view = '_resultadoFrete';


    public function run($cep, $returnType)
    {
        $fretes = $this->calculaFrete($cep);
        if (strtolower($returnType) == 'json') {
            return $this->retornoJson($fretes);
	    //print_r($fretes);
        } else {
            return $this->retornoHtml($fretes);
	    //print_r(fretes);
        }
    }

    private function getProdutos()
    {
        $carrinhoKeys = Yii::$app->session['carrinho'] ? array_keys(Yii::$app->session['carrinho']) : [];
        if ($id = Yii::$app->request->get('produto_id')) {
	//echo 1;die;
	//echo "<pre>"; print_r(ProdutoFilial::find()->byProduto($id)->all()); echo "</pre>"; die;
            /*$dadosProduto = (new Query())
	    ->distinct('p.id')
	    ->select([
	        'p.nome',
	        'pf.id as pf_id',
	        'pf.quantidade',
	        'vpmm.menor_valor',
	        'f.nome as filial',
	        'filial_id'
	    ])
	    ->from('produto p')
	    ->innerJoin('produto_filial pf', 'pf.produto_id = p.id')
	    ->innerJoin('valor_produto_menor_maior vpmm', 'vpmm.produto_id = pf.produto_id')
	    ->innerJoin('filial f', 'f.id = pf.filial_id')
	    ->where(['p.id' => $id])
	    ->andWhere('pf.quantidade > 0')
	    ->all();

	    $dadoProduto = null;

	    foreach ($dadosProduto as $dado) {
		    if (empty($dadoProduto)) {
		        $dadoProduto = $dado;
		    } else {
		        if ($dado['menor_valor'] < $dadoProduto['menor_valor']) {
		            $dadoProduto = $dado;
		        }
		    }
	    }*/

	    //echo "<pre>"; print_r($dadoProduto); echo "</pre>"; die;
	    //echo "<pre>"; print_r(ProdutoFilial::find()->andWhere(["=", "id", $dadoProduto["pf_id"]])->all()); echo "</pre>"; die;

	    //return $produtos = ProdutoFilial::find()->andWhere(["=", "id", $dadoProduto["pf_id"]])->all();
	    return $produtos = ProdutoFilial::find()->byProduto($id)->all(); //ATUAL
	    //return $produtos = ProdutoFilial::find()->byIds($carrinhoKeys)->byProduto($id)->all();
        }
        if ($id = Yii::$app->request->get('filial_id')) {
	//echo 2; die;
            return $produtos = ProdutoFilial::find()->byIds($carrinhoKeys)->byFilial($id)->all();
        }

        return ProdutoFilial::findAll($carrinhoKeys);
    }

    public function calculaFrete($cep)
    {
        if (empty($cep)) {
            return null;
        }

        $fretes = [];
        $calculadoras = [];
        $produtos = $this->getProdutos();
        $juridica = Yii::$app->params['isJuridica']();
        $somatorio_volumes = 0;
        $qt = 1;
        foreach ($produtos as $produto) {
            if (isset(Yii::$app->session['carrinho'][$produto->id])) {
                $qt = Yii::$app->session['carrinho'][$produto->id];
            }
            if (!isset($produto->valorAtual)) { continue; }

            $fretes[$produto->filial->id]["quantidade"] = $qt;
            $fretes[$produto->filial->id]["peso"] = (isset($fretes[$produto->filial->id]["peso"])) ? $fretes[$produto->filial->id]["peso"] + ($produto->produto->peso * $qt) : $produto->produto->peso * $qt;
            $fretes[$produto->filial->id]["altura"] = (isset($fretes[$produto->filial->id]["altura"])) ? $fretes[$produto->filial->id]["altura"] + ($produto->produto->altura * $qt) : $produto->produto->altura * $qt;
            $fretes[$produto->filial->id]["largura"] = (isset($fretes[$produto->filial->id]["largura"])) ? $fretes[$produto->filial->id]["largura"] + ($produto->produto->largura * $qt) : $produto->produto->largura * $qt;
            $fretes[$produto->filial->id]["profundidade"] = (isset($fretes[$produto->filial->id]["profundidade"])) ? $fretes[$produto->filial->id]["profundidade"] + ($produto->produto->profundidade * $qt) : $produto->produto->profundidade * $qt;
            $fretes[$produto->filial->id]["valorTotal"] = (isset($fretes[$produto->filial->id]["valorTotal"])) ? $fretes[$produto->filial->id]["valorTotal"] + ($produto->valorAtual->getValorFinal($juridica) * $qt) : ($produto->valorAtual->getValorFinal($juridica) * $qt);
            $fretes[$produto->filial->id]["loja"] = $produto->filial->nome;
            $fretes[$produto->filial->id]["somatorio_volumes"] =
                (isset($fretes[$produto->filial->id]["somatorio_volumes"]))
                    ? ($fretes[$produto->filial->id]["somatorio_volumes"] + ($produto->produto->profundidade * $produto->produto->altura * $produto->produto->largura) * $qt)
                    : ($produto->produto->profundidade * $produto->produto->altura * $produto->produto->largura * $qt);
        }

	//echo "<pre>"; print_r($fretes); echo "</pre>"; die;

        foreach ($fretes as $key => $frete) {
            $frete['somatorio_volumes'] = ceil(pow($frete['somatorio_volumes'], (1 / 3)));
            if ($frete['somatorio_volumes'] < 16) {
                $frete['somatorio_volumes'] = 16;
            }
            $filial = Filial::find()->innerJoinWith([
                'enderecoFilial',
                'enderecoFilial.cidade',
                'enderecoFilial.cidade.estado'
            ])->andWhere(['filial.id' => $key])->one();
            $calculadora = Yii::createObject(
                [
                    'class' => '\frete\CalculadoraFrete',
                    'valorDeclarado' => $frete['valorTotal'],
                    'cepOrigem' => $filial->enderecoFilial->cep,
                    'filial' => $filial,
                    'cepDestino' => $cep,
                    'peso' => $frete["peso"],
                    'altura' => $frete['somatorio_volumes'],
                    'largura' => $frete['somatorio_volumes'],
                    'profundidade' => $frete['somatorio_volumes'],
                    'quantidade' => $frete["quantidade"],
                ]
            );
            $fretes[$key]["valores"] = $calculadora->getFretes();
            $calculadoras[$key] = $calculadora->getFretes();
        }

        return $calculadoras;
    }

    public function retornoHtml($fretes)
    {
        return $this->controller->renderAjax($this->view, ['fretes' => $fretes]);
    }

    public function retornoJson($fretes)
    {
        return Json::encode($fretes);
    }
}
