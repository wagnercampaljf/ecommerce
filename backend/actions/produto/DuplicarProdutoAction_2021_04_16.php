<?php

namespace backend\actions\produto;

use common\models\Produto;
use yii\base\Action;
use common\models\Imagens;

class DuplicarProdutoAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);

        $novo_produto                           = new Produto;
        $novo_produto->nome                     = $model->nome;
        $novo_produto->descricao                = $model->descricao;
        $novo_produto->peso                     = $model->peso;
        $novo_produto->altura                   = $model->altura;
        $novo_produto->largura                  = $model->largura;
        $novo_produto->profundidade             = $model->profundidade;
        $novo_produto->codigo_global            = "D.".$model->codigo_global;
        $novo_produto->codigo_montadora         = $model->codigo_montadora;
        $novo_produto->codigo_fabricante        = $model->codigo_fabricante;
        $novo_produto->fabricante_id            = $model->fabricante_id;
        $novo_produto->slug                     = $model->slug;
        $novo_produto->micro_descricao          = $model->micro_descricao;
        $novo_produto->subcategoria_id          = $model->subcategoria_id;
        $novo_produto->aplicacao                = $model->aplicacao;
        $novo_produto->texto_vetor              = $model->texto_vetor;
        $novo_produto->codigo_similar           = $model->codigo_similar;
        $novo_produto->aplicacao_complementar   = $model->aplicacao_complementar;
	$novo_produto->produto_condicao_id	= $model->produto_condicao_id;
	//echo "<pre>"; print_r($novo_produto); echo "</pre>";die;
        if ($novo_produto->save()) {

            $imagens = Imagens::find()->andWhere(['=','produto_id',$model->id])->all();
            foreach ($imagens as $k => $imagem){
                echo $k." - "; var_dump($imagem->id);
                $nova_imagem = new Imagens;
                $nova_imagem->produto_id        = $novo_produto->id;
                $nova_imagem->imagem            = $imagem->imagem;
                $nova_imagem->imagem_sem_logo   = $imagem->imagem_sem_logo;
                $nova_imagem->ordem             = $imagem->ordem;
                var_dump($nova_imagem->save());
            }

            return $this->controller->redirect(['update', 'id' => $novo_produto->id]);
        } else {
            return $this->controller->render('update', ['model' => $model]);
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
