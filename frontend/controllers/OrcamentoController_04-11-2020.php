<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 09/12/2015
 * Time: 17:27
 */

namespace frontend\controllers;

use common\models\Orcamento;
use Yii;

class OrcamentoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new Orcamento();

        if ($model->load(Yii::$app->request->post())) {


            $texto_cabecalho = "<br><h2>Solicitação de Orçamento</h2><br>";

            $texto_html = "<div>
                            <p>Nome: $model->name</p>
                            <p>Email: $model->email</p>
                            <p>Modelo: $model->subject</p>
                            <p>Peças: $model->body</p>
                        </div>";

            $texto_rodape = "<br><br><h3>Atenciosamente.</h3> <br>";

            \Yii::$app->mailer  ->compose()

                ->setFrom(["vendas3.pecaagora@gmail.com",])
                ->setReplyTo($model->email)
                //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                ->setTo(["vendas3.pecaagora@gmail.com",])
                ->setCc($model->email)
                ->setSubject(\Yii::$app->name . ' - Solicitação de Orçamento: '.$model->name)
                //->setTextBody($texto)
                ->setHtmlBody($texto_cabecalho . $texto_html . $texto_rodape)
                ->send();

            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }








}

?>