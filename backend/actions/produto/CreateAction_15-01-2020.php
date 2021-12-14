<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 16/10/2015
 * Time: 09:52
 */

namespace backend\actions\produto;


use common\models\AnoModelo;
use common\models\Modelo;
use common\models\Produto;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;

class CreateAction extends Action
{

    public function run()
    {
        $model = new Produto();
//        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post())) {
            $this->slugify($model);
            if ($model->save()) {
                $this->atualizaAplicacao($model);

		        //OMIE               
                if (substr($model->codigo_global,0,3) != 'CX.'){
                    
                    $criar_omie = new Omie(1, 1);
                    
                    $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($model->id)->one();
                    //$valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
		    $valor_produto  = ($minValue==NULL) ? "1" : $minValue->getValorFinal();
                    $descricao = substr($model->nome." (".$model->codigo_global.")",0,100);
                    
                    //echo "Inserindo produtos...SP\n\n";
                    $body = [
                        "call" => "IncluirProduto",
                        "app_key" => '468080198586',
                        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                        "param" => [
                            "codigo_produto_integracao" => $model->codigo_global,
                            "codigo"                    => $model->codigo_global,
                            "descricao"                 => $descricao,//substr($model->nome." (".$model->codigo_global.")",0,100),
                            "ncm"                       => ($model->codigo_montadora=="" ? "0000.00.00" : substr($model->codigo_montadora,0,4).".".substr($model->codigo_montadora,4,2).".".substr($model->codigo_montadora,6,2)),
                            "unidade"                   => "PC",
                            "valor_unitario"            => round($valor_produto,2),
                            "tipoItem"                  => "99",
                            "peso_liq"                  => round($model->peso,2),
                            "peso_bruto"                => round($model->peso,2),
                            "altura"                    => round($model->altura,2),
                            "largura"                   => round($model->largura,2),
                            "profundidade"              => round($model->profundidade,2),
                            "marca"                     => ($model->fabricante_id==null) ? "Peça Agora" : $model->fabricante->nome,
                            "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                        ]
                    ];
                    $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                    print_r($response); echo "<br><br><br>"; //die;
                                       
                    //echo "Inserindo produtos...MG\n\n";
                    $body = [
                        "call" => "IncluirProduto",
                        "app_key" => '469728530271',
                        "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
                        "param" => [
                            "codigo_produto_integracao" => $model->codigo_global,
                            "codigo"                    => $model->codigo_global,
                            "descricao"                 => substr($model->nome." (".$model->codigo_global.")",0,100),
                            "ncm"                       => ($model->codigo_montadora=="" ? "0000.00.00" : substr($model->codigo_montadora,0,4).".".substr($model->codigo_montadora,4,2).".".substr($model->codigo_montadora,6,2)),
                            "unidade"                   => "PC",
                            "valor_unitario"            => round($valor_produto,2),
                            "tipoItem"                  => "99",
                            "peso_liq"                  => round($model->peso,2),
                            "peso_bruto"                => round($model->peso,2),
                            "altura"                    => round($model->altura,2),
                            "largura"                   => round($model->largura,2),
                            "profundidade"              => round($model->profundidade,2),
                            "marca"                     => ($model->fabricante_id==null) ? "Peça Agora" : $model->fabricante->nome,
                            "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                        ]
                    ];
                    $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                    print_r($response); echo "<br><br><br>"; //die;
                }
                //OMIE

                return $this->controller->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->controller->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $model Produto
     */
    private function atualizaAplicacao(&$model)
    {
        $anosModelo_ids = ArrayHelper::getColumn($model->anosModelo, 'id');
        if (!empty($model->anoModelo_id)) {
            foreach ($model->anoModelo_id as $id) {
                if (!in_array($id, $anosModelo_ids)) {
                    $anoModelo = AnoModelo::findOne($id);
                    $model->link('anosModelo', $anoModelo);
                    $model->aplicacao .= $anoModelo->modelo->nome . " " . $anoModelo->nome . "<br>";
                    $model->save();
                }
            }
        } else {
            $model->anoModelo_id = [];
        }
    }

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
