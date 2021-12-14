<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19/10/2015
 * Time: 13:39
 */

namespace backend\actions\produto;

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
                    
                    $this->criarOmie($model);
                    
                    $this->alterarOmie($model);
                    
                }

                if (Yii::$app->request->post('submitButton')=='expedicao'){

                    return $this->controller->redirect([
                        '/consulta-expedicao/busca', 'codigo_pa' => $model->id,
                        'mensagem' => '<div class="text-primary h4" style="font-size: 30px; color: red">Produto atualizado com sucesso!</div>',
                    ]);

                }else{

                    return $this->controller->render('update', [
                        'model' => $model,
                         'mensagem' => '<div class="text-primary h4" style="font-size: 30px; color: red">Produto atualizado com sucesso!</div>',
                    ]);

                }
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
    
    public function criarOmie($produto)
    {
        echo "Criando produtos...\n\n";
        
        $criar_omie = new Omie(1, 1);
        
        if (substr($produto->codigo_global,0,3) != 'CX.'){
            
            $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
            $valor_produto  = ($minValue==NULL) ? "1" : $minValue->getValorFinal();
            $descricao = substr($produto->codigo_global." ".$produto->nome,0,120);
            $descricao = str_replace(" ","%20",$descricao);
            
            
            //echo "Inserindo produtos...SP\n\n";
            $body = [
                "call" => "IncluirProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,//$produto->codigo_global,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0 ,
                        "cupom_fiscal"      => "S"
                    ]
                ]
            ];
            $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>"; //die;
            
            
            //echo "Inserindo produtos...MG\n\n";
            $body = [
                "call" => "IncluirProduto",
                "app_key" => '469728530271',
                "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0 ,
                        "cupom_fiscal"      => "S"
                    ]
                ]
            ];
            $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>"; //die;
            
            
            //echo "Inserindo produtos...CONTA DUPLICADA\n\n";
            $body = [
                "call" => "IncluirProduto",
                "app_key" => '1017311982687',
                "app_secret" => '78ba33370fac6178da52d42240591291',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    //"codigo"                    => $produto->codigo_global,
                    "codigo"                    => "PA".$produto->id,
                    //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
                    //"descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0 ,
                        "cupom_fiscal"      => "S"
                    ],
                ]
            ];
            $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>"; //die;
            
        }
    }
    
    
    public function alterarOmie($produto)
    {

        echo "Criando produtos...\n\n";
        
        $criar_omie = new Omie(1, 1);
        
        if (substr($produto->codigo_global,0,3) != 'CX.'){
            
            $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
            $valor_produto  = ($minValue==NULL) ? "1" : $minValue->getValorFinal();
            $descricao = substr($produto->codigo_global." ".$produto->nome,0,120);
            $descricao = str_replace(" ","%20",$descricao);
            
            //echo "Alterando produtos...SP\n\n";
            $body = [
                "call" => "AlterarProduto",
                "app_key" => '468080198586',
                "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0 ,
                        "cupom_fiscal"      => "S"
                    ]
                ]
            ];
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>"; //die;
            
            //echo "Alterando produtos...MG\n\n";
            $body = [
                "call" => "AlterarProduto",
                "app_key" => '469728530271',
                "app_secret" => '6b63421c9bb3a124e012a6bb75ef4ace',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0 ,
                        "cupom_fiscal"      => "S"
                    ],
                ]
            ];
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>";
            
            //echo "Alterando produtos...CONTA DUPLICADA\n\n";
            $body = [
                "call" => "AlterarProduto",
                "app_key" => '1017311982687',
                "app_secret" => '78ba33370fac6178da52d42240591291',
                "param" => [
                    "codigo_produto_integracao" => "PA".$produto->id,
                    "codigo"                    => "PA".$produto->id,
                    //"descricao"                 => substr($produto->nome." (".$produto->codigo_global.")",0,100),
                    //"descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
                    "descricao"                 => $descricao,
                    "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                    "unidade"                   => "PC",
                    "valor_unitario"            => round($valor_produto,2),
                    "tipoItem"                  => "99",
                    "peso_liq"                  => round($produto->peso,2),
                    "peso_bruto"                => round($produto->peso,2),
                    "altura"                    => round($produto->altura,2),
                    "largura"                   => round($produto->largura,2),
                    "profundidade"              => round($produto->profundidade,2),
                    "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [
                        "origem_mercadoria" => 0,
                        "cupom_fiscal"      => "S"
                    ],
                ]
            ];
            $response = $criar_omie->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            //print_r($response); echo "<br><br><br>"; die;
            
        }
    }

}