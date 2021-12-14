<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19/10/2015
 * Time: 13:39
 */

namespace backend\actions\produto;


use common\models\AnoModelo;
use common\models\Produto;
use common\models\ProdutoAnoModelo;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use console\controllers\actions\omie\Omie;
use common\models\ValorProdutoFilial;

class UpdateAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $this->slugify($model);
            if ($model->save()) {
               
		//Atualização no Omie - São Paulo
		if (substr($model->codigo_global,0,3) != 'CX.'){
	                $meli = new Omie(1, 1);
	                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($model->id)->one();
	                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
	                $body = [
	                    "call" => "AlterarProduto",
	                    "app_key" => '468080198586',
	                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
	                    "param" => [
	                        "codigo_produto_integracao" => $model->id,
	                        "codigo"                    => $model->codigo_global,
	                        "descricao"                 => $model->nome." (".$model->codigo_global.")",
	                        "ncm"                       => ($model->codigo_montadora=="" ? "0000.00.00" : substr($model->codigo_montadora,0,4).".".substr($model->codigo_montadora,4,2).".".substr($model->codigo_montadora,6,2)),
	                        "valor_unitario"            => round($valor_produto,2),
	                        "unidade"                   => "PC",
	                        "tipoItem"                  => "99",
	                        "peso_liq"                  => $model->peso,
	                        "peso_bruto"                => $model->peso,
	                        "altura"                    => $model->altura,
	                        "largura"                   => $model->largura,
	                        "profundidade"              => $model->profundidade,
	                        "marca"                     => ($model->fabricante_id == null)? "Peça Agora" : $model->fabricante->nome,
	                        "recomendacoes_fiscais"     => [ "origem_mercadoria" => 0 ]
	                    ]
	                ];
	                $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
			//print_r($response);

	                //Atualização no Omie - Minas Gerais
	                $body = [
	                    "call" => "AlterarProduto",
	                    "app_key" => '469728530271',
	                    "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
	                    "param" => [
	                        "codigo_produto_integracao" => $model->id,
	                        "codigo"                    => $model->codigo_global,
	                        "descricao"                 => $model->nome." (".$model->codigo_global.")",
	                        "ncm"                       => ($model->codigo_montadora=="" ? "0000.00.00" : substr($model->codigo_montadora,0,4).".".substr($model->codigo_montadora,4,2).".".substr($model->codigo_montadora,6,2)),
	                        "valor_unitario"            => round($valor_produto,2),
	                        "unidade"                   => "PC",
	                        "tipoItem"                  => "99",
	                        "peso_liq"                  => $model->peso,
	                        "peso_bruto"                => $model->peso,
	                        "altura"                    => $model->altura,
	                        "largura"                   => $model->largura,
	                        "profundidade"              => $model->profundidade,
	                        "marca"                     => ($model->fabricante_id == null)? "Peça Agora" : $model->fabricante->nome,
       		                "recomendacoes_fiscais"     => [ "origem_mercadoria" => 0 ]
	                    ]
    	            ];
    	            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
		    //echo "<br><br><br>"; print_r($response); die;
		}

		return $this->controller->redirect(['index']);
            } else {
                return $this->controller->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }

    //Desativada em 30/05/2017, aplicação volta a ser textual apenas
    /**
     * @param $model Produto
     */
//    private function atualizaAplicacao(&$model){
//        $anosModelo_ids = ArrayHelper::getColumn($model->anosModelo, 'id');
//        if (!empty($model->anoModelo_id)) {
//            foreach ($model->anoModelo_id as $id) {
//                if (!in_array($id, $anosModelo_ids)) {
//                    $model->link('anosModelo', AnoModelo::findOne($id));
//                }
//            }
//        } else {
//            $model->anoModelo_id = [];
//        }
//        foreach ($model->anosModelo as $anosModelo) {
//            if ($anosModelo && !in_array($anosModelo->id, $model->anoModelo_id)) {
//                $model->unlink('anosModelo', $anosModelo, true);
//            }
//        }
////        Aplicação em texto recebe o nome dos modelos
//        if (!empty($model->produtoModelo)) {
//            $model->aplicacao = " ";
//            foreach ($model->produtoModelo as $modelo) {
//                $model->aplicacao .= $modelo['nome'] . "<br>";
//            }
//        }
//    }

    private function slugify(&$model)
    {
        $text = $model->nome . ' ' . $model->codigo_global;

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        $model->slug = $text;
    }

}
