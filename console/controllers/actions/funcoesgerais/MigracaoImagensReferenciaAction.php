<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use yii\db\Query;


class MigracaoImagensReferenciaAction extends Action
{
    public function run($id)
    {

        $imagens = (new Query())
            ->select([
                'i.id',
                'i.produto_id',
                'i.ordem',
                'i.imagem_sem_logo',
                'i.imagem_zoom'
            ])
            ->from('imagens i')
            ->where("produto_id = $id")
            ->orderBy(["id" => SORT_DESC])
            ->limit(200)
            ->all();

        $stream_opts = [
            "ssl" => [
                "verify_peer" => false,
            ]
        ];

        foreach ($imagens as $key => $imagem) {

            echo "\n" . $key . " - " . $imagem["id"] . " - " . $imagem["produto_id"];

            if (!file_exists('/var/www/imagens_produto/produto_' . $imagem['produto_id'])) {
                mkdir('/var/www/imagens_produto/produto_' . $imagem['produto_id'], 0777, true);
            }

            if (!empty($imagem)) {
                $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                copy($caminho, '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . ".webp", stream_context_create($stream_opts));

                if ($imagem['imagem_sem_logo'] !== null) {
                    $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                    copy($caminho, '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_sem_logo.webp", stream_context_create($stream_opts));
                }

                if ($imagem['imagem_zoom'] !== null) {
                    $caminho = "https://www.pecaagora.com/site/get-link-zoom?produto_id=" . $imagem['produto_id'] . '&' . 'ordem=' . $imagem['ordem'];
                    copy($caminho, '/var/www/imagens_produto/produto_' . $imagem['produto_id'] . '/' . $imagem['produto_id'] . '_' . $imagem['ordem'] . "_zoom.webp", stream_context_create($stream_opts));
                }
            }
        }


        echo "\n\nFIM da rotina de geração de imagens locais!";
    }
}
