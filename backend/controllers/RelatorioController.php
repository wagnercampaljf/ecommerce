<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\base\Controller;
use backend\models\Relatorios\InventarioEstoque;
use backend\models\Relatorios\RelatorioEstoqueMarcaExcel;
use backend\models\Relatorios\RelEstoqueVendasMarcaExcel;
use backend\models\Relatorios\RelVendasPorMarcaExcel;


class RelatorioController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionRelatorios()
    {
        $dados = Yii::$app->request->get();

        switch ($dados['relatorio']) {
            case 1:
                $relatorio = new InventarioEstoque();
                $relatorio->init($dados['filial']);
                break;
            case 2:
                $relatorio = new RelatorioEstoqueMarcaExcel();
                $relatorio->init($dados['marca']);
                break;
            case 3:
                $relatorio = new RelVendasPorMarcaExcel();
                $relatorio->init($dados['marca'], $dados['dt_inicial'], $dados['dt_final']);
                break;
            case 4:
                $relatorio = new RelEstoqueVendasMarcaExcel();
                $relatorio->init($dados['marca'], $dados['dt_inicial'], $dados['dt_final']);
                break;
        }
    }
}
