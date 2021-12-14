<?php

namespace backend\controllers;

class ProdutoEstoquePesquisaController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $this->layout = 'main-pesquisa';
        return $this->render('index');
    }

}
