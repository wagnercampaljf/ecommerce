<?php

namespace frontend\controllers;

use common\models\Pedido;
use vendor\iomageste\Moip\Http\HTTPConnection;
use vendor\iomageste\Moip\Http\HTTPRequest;
use vendor\iomageste\Moip\Moip;
use vendor\iomageste\Moip\Resource\Multiorders;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use vendor\vhorta\asyncmailer\AsyncMailer;
use yii\web\HttpException;

class CallbackApiController extends Controller
{
    public $enableCsrfValidation = false;
    public $moipEvents = ["MULTIORDER.PAID" => 2, "MULTIPAYMENT.CANCELLED" => 5];

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'moip' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * @param $event
     * @param $multiOrder
     */
    public function mudarStatus($event, $multiOrder)
    {
        foreach ($multiOrder->getOrders() as $order) {
            $orderId = $order->getId();
            $pedido = Pedido::find()->where(['token_moip' => $orderId])->one();
            $pedido->mudarStatus($this->moipEvents[$event['event']]);
        }
    }

    public function multiPaymentHook($event)
    {
        if ($event['event'] == "MULTIPAYMENT.CANCELLED") {
            $moip = new Moip(Moip::PRODUCTION_ENDPOINT, 'OAuth');

            $multiPayment = $moip->payments()->get($event['resource']['payment']['id']);

            $multiOrderLink = $multiPayment->getLinks()->multiorder->href;

            $array = explode('/', $multiOrderLink);
            $multiOrderId = end($array);

            $multiOrder = $moip->multiorders()->get($multiOrderId);

            $this->mudarStatus($event, $multiOrder);
        }
//        if($event['event'] == "ORDER.REVERTED"){
//
//            $moip = new Moip(Moip::PRODUCTION_ENDPOINT, 'OAuth');
//
//            $payment = $moip->payments()->get($event['resource']['ORDER']['id']);
//            $orderLink = $payment->getLinks()->order->href;
//            $orderId = array_pop(explode('/', $orderLink));
//            $order = $moip->orders()->get($orderId);
//
//            $this->mudarStatus($event, $order);
//        }
    }

    public function multiOrderHook($event)
    {
        if (isset($this->moipEvents[$event['event']])) {
            $moip = new Moip(Moip::PRODUCTION_ENDPOINT, 'OAuth');
            $multiOrder = $moip->multiorders()->get($event['resource']['multiorder']['id']);

            $this->mudarStatus($event, $multiOrder);
        }
    }

    public function actionMoip()
    {
        if (Yii::$app->request->get('code')) {
            $body = Json::encode([
                "appId" => Moip::APPID,
                "appSecret" => Moip::APPKEY,
                "redirectUri" => Yii::$app->urlManager->createAbsoluteUrl(['callback-api/moip'], 'https'),
                "grantType" => "AUTHORIZATION_CODE",
                "code" => Yii::$app->request->get('code')
            ]);

            $httpConnection = new HTTPConnection('Moip SDK');
            $httpConnection->initialize(Moip::PRODUCTION_ENDPOINT, true);
            $httpConnection->addHeader('Accept', 'application/json');
            $httpConnection->addHeader('Content-Type', 'application/json');
            $httpConnection->addHeader('Content-Length', strlen($body));
            $httpConnection->setRequestBody($body);

            $httpResponse = $httpConnection->execute('/oauth/accesstoken', HTTPRequest::POST);

            //echo Html::tag('pre', print_r(Json::decode($httpResponse->getContent()), true));
            try {
                $resposta = Json::decode($httpResponse->getContent());
                //throw new InvalidParamException('Erro');
            } catch (InvalidParamException $e) {
                Yii::error($httpResponse->getContent(), __METHOD__);
                throw new InvalidParamException('Erro. Por favor contate o admnistrador com horário e descrição do erro.');
            }
            Yii::$app->session['accessToken'] = ArrayHelper::getValue($resposta, 'accessToken');
            Yii::$app->session['moipAccountId'] = ArrayHelper::getValue($resposta, 'moipAccountId');

            return $this->redirect(['lojista/create']);
        } else {
            if (empty(Yii::$app->request->rawBody)) {
                Yii::$app->session->setFlash(
                    'error',
                    'Infelizmente não é possivel fazer o cadastro sem a sua autorização Moip.
                    Por favor autorize a utilização da conta Moip para continuarmos o cadastro.'
                );

                return $this->redirect(['lojista/intro']);
            }

            $data = json_decode(Yii::$app->request->rawBody, true);

            if ((strpos($data['event'], 'MULTIPAYMENT') !== false)) {
                $this->multiPaymentHook($data);
            } else {
                if (strpos($data['event'], 'MULTIORDER') !== false) {
                    $this->multiOrderHook($data);
                }
            }
        }
    }

    public function actionTeste()
    {
        
        $body = Json::encode([
            "appId" => Moip::APPID,
            "appSecret" => Moip::APPKEY,
            "redirectUri" => Yii::$app->urlManager->createAbsoluteUrl(['callback-api/moip'], 'https'),
            "grantType" => "AUTHORIZATION_CODE",
            "code" => Yii::$app->request->get('code')
        ]);
        
        $httpConnection = new HTTPConnection('Moip SDK');
        $httpConnection->initialize(Moip::PRODUCTION_ENDPOINT, true);
        $httpConnection->addHeader('Accept', 'application/json');
        $httpConnection->addHeader('Content-Type', 'application/json');
        $httpConnection->addHeader('Content-Length', strlen($body));
        $httpConnection->setRequestBody($body);
        
        $httpResponse = $httpConnection->execute('/oauth/accesstoken', HTTPRequest::POST);

	echo "<pre>"; print_r($httpResponse); echo "</pre>";
        echo Html::tag('pre', print_r(Json::decode($httpResponse->getContent()), true));
        die;
        
        if (Yii::$app->request->get('code')) {
            $body = Json::encode([
                "appId" => Moip::APPID,
                "appSecret" => Moip::APPKEY,
                "redirectUri" => Yii::$app->urlManager->createAbsoluteUrl(['callback-api/moip'], 'https'),
                "grantType" => "AUTHORIZATION_CODE",
                "code" => Yii::$app->request->get('code')
            ]);
            
            $httpConnection = new HTTPConnection('Moip SDK');
            $httpConnection->initialize(Moip::PRODUCTION_ENDPOINT, true);
            $httpConnection->addHeader('Accept', 'application/json');
            $httpConnection->addHeader('Content-Type', 'application/json');
            $httpConnection->addHeader('Content-Length', strlen($body));
            $httpConnection->setRequestBody($body);
            
            $httpResponse = $httpConnection->execute('/oauth/accesstoken', HTTPRequest::POST);
            
            echo Html::tag('pre', print_r(Json::decode($httpResponse->getContent()), true));
            die;

            try {
                $resposta = Json::decode($httpResponse->getContent());
                //throw new InvalidParamException('Erro');
            } catch (InvalidParamException $e) {
                Yii::error($httpResponse->getContent(), __METHOD__);
                throw new InvalidParamException('Erro. Por favor contate o admnistrador com horário e descrição do erro.');
            }
            Yii::$app->session['accessToken'] = ArrayHelper::getValue($resposta, 'accessToken');
            Yii::$app->session['moipAccountId'] = ArrayHelper::getValue($resposta, 'moipAccountId');
            
            return $this->redirect(['lojista/create']);
        } else {
            if (empty(Yii::$app->request->rawBody)) {
                Yii::$app->session->setFlash(
                    'error',
                    'Infelizmente não é possivel fazer o cadastro sem a sua autorização Moip.
                    Por favor autorize a utilização da conta Moip para continuarmos o cadastro.'
                    );
                
                return $this->redirect(['lojista/intro']);
            }
            
            $data = json_decode(Yii::$app->request->rawBody, true);
            
            if ((strpos($data['event'], 'MULTIPAYMENT') !== false)) {
                $this->multiPaymentHook($data);
            } else {
                if (strpos($data['event'], 'MULTIORDER') !== false) {
                    $this->multiOrderHook($data);
                }
            }
        }
    }

}
