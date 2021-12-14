<?php

namespace frontend\controllers;

use common\models\Lojista;
use common\models\Usuario;
use vendor\vhorta\asyncmailer\Mailer;
Use Yii;
use common\models\Comprador;
use common\models\Pedido;

class MailerController extends Mailer
{
    const MUDANCA_STATUS = "/status-alterado";
    const CRIACAO_COMPRADOR = "/comprador-criado";
    const CRIACAO_LOJISTA = "/lojista-criado";
    const PEDIDO_CONFIRMADO = 2;
    const PEDIDO_CANCELADO = 5;
    /**
     * Cria email com template de mudança de status
     *
     * @param  $id do pedido alterado
     * @author Vitor Horta  14/04/2015
     * @since  0.1
     */
    public function actionStatusAlterado($id, $usuarioEmail, $usuarioNome)
    {
        $pedido = Pedido::findOne($id);
        $comprador = $pedido->comprador;

        $message = \Yii::$app->mailer->compose('mudanca_status', ['nome' => $comprador->nome, 'status' => $pedido->statusAtual->tipoStatus->nome, 'id' => $pedido->id])
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($comprador->email)
            ->setSubject('Mudança de status');

        $this->sendMail($message);

        if ($pedido->statusAtual->tipoStatus->id == self::PEDIDO_CONFIRMADO || $pedido->statusAtual->tipoStatus->id == self::PEDIDO_CANCELADO) {
            $message = \Yii::$app->mailer->compose('mudanca_status_lojista',
                ['nome' => $usuarioNome, 'status' => $pedido->statusAtual->tipoStatus->nome, 'id' => $pedido->id])
                ->setFrom(Yii::$app->params['supportEmail'])
                ->setTo([$usuarioEmail,'sac@pecaagora.com'])
                ->setCc('sac@pecaagora.com')
                ->setSubject('Mudança de status');

            $this->sendMail($message);
        }
    }

    /**
     * Cria email com template de criação de comprador
     *
     * @param  $id do comprador
     * @author Vitor Horta  14/04/2015
     * @since  0.1
     */
    public function actionCompradorCriado($id)
    {
        $comprador = Comprador::findOne($id);

        $message =  \Yii::$app->mailer->compose('criacao_comprador', ['nome' => $comprador->nome, 'email' => $comprador->email])
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($comprador->email)
            ->setSubject('Cadastro Confirmado')
	    ->send();

        $this->sendMail($message);
    }

    /**
     * Cria email com template de criação de lojista
     *
     * @param  $id do lojista
     * @author Vitor Horta  14/04/2015
     * @since  0.1
     */
    public function actionLojistaCriado($id)
    {
        $usuario = Usuario::findOne($id);

        $message = \Yii::$app->mailer->compose('criacao_lojista', ['nome' => $usuario->nome, 'email' => $usuario->email])
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($usuario->email)
            ->setSubject('Cadastro Confirmado');

        $this->sendMail($message);
    }

}
