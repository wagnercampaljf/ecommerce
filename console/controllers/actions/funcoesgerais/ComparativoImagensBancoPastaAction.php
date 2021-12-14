<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Imagens;

class ComparativoImagensBancoPastaAction extends Action
{
    public function run()
    {

        echo "INÍCIO da rotina de Verificação de imagens\n\n";

        $arquivo_log = fopen("/var/www/log_imagens_banco_pasta_" . date("Y-m-d") . ".txt", "a");

        $modelImagem = Imagens::find()->select(['produto_id', 'ordem'])->orderBy(['produto_id' => SORT_DESC])->all();


        foreach ($modelImagem as $k => $imagem) {
            $texto = $imagem->produto_id . ' -- ';
            if (!file_exists('/var/www/imagens_produto/produto_' . $imagem->produto_id . '/' . $imagem->produto_id . '_' . $imagem->ordem . '.webp')) {
                $texto = 'Imagem - ' . $imagem->produto_id . ' - ' . $imagem->produto_id . '_' . $imagem->ordem;
                $msg = sprintf("[%s]: %s%s", date('Y-m-d H:i:s'), $texto, PHP_EOL);

                file_put_contents("/var/www/log_imagens_banco_pasta_" . date("Y-m-d") . ".txt", $msg, FILE_APPEND);
            }



            echo $k . ' - ' . $texto . "\n";
        }

        fclose($arquivo_log);

        echo "\n\nFIM da rotina de Verificação de imagens!";
    }
}
