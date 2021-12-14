<?php
/* include autoloader normally */
namespace common\mail;

use Yii;
use yii\helpers\Url;

/**
 * AsyncMailer implementa a chamada assincrona via curl para que MailerController envie emails.
 *
 * @author Vitor Horta
 * @since 0.1
 */
class AsyncMailer
{
    const MUDANCA_STATUS = "status-alterado";
    const CRIACAO_COMPRADOR = "comprador-criado";
    const CRIACAO_LOJISTA = "lojista-criado";

    /**
     * Transforma array de parametros em string para ser enviado via post
     * Chama curl para envio de email assincrono
     *
     * @author Vitor Horta
     * @since 0.1
     */
    public static function sendMail($params, $actionRoute)
    {

        $params['token'] = Yii::$app->params['emailToken'];

        //Prepara parametros post
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key . '=' . urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $url = Yii::$app->request->serverName . Yii::$app->urlManagerFrontEnd->createUrl('') . Url::to('mailer/') . $actionRoute . "?" . $post_string;

        AsyncMailer::curl_post_async($url, $params);
    }

    /**
     * Realiza chamada curl para url para envio de email
     *
     * @author Vitor Horta
     * @since 0.1
     */
    function curl_post_async($url, $params, $type = 'POST')
    {
        set_time_limit(0);
        ignore_user_abort(true);

        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key . '=' . urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        $result = curl_exec($ch);

        curl_close($ch);
    }
}

?>