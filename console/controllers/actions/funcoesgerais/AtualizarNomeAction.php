<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;

class AtualizarNomeAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do nome: \n\n";

        $arquivo_log = fopen("/var/tmp/log_alterar_nome.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "id;nome;status\n");

        $produtos = Produto::find()->andWhere(['is', 'slug', null])->all();

        foreach ($produtos as &$produto){
            echo "\n".$produto->nome; var_dump($produto->slug);
            $this->slugify($produto);
            $produto->save();
        }

        echo "\n\nFIM da rotina de atualizacao do nome!";
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
