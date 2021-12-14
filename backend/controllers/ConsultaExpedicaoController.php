<?php

namespace backend\controllers;

use backend\models\ConsultaExpedicaoBusca;
use common\models\LogConsultaExpedicao;
use common\models\LogConsultaExpedicaoSearch;
use Yii;

class ConsultaExpedicaoController extends \yii\web\Controller
{
    
    public $layout = "layout_limpo";

    public function actionIndex()
    {
        $searchModel = new LogConsultaExpedicaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBusca($codigo_pa)
    {


        $log_consulta_expedicao = new LogConsultaExpedicao;



        //$usuario= "Espedição SP";

        $codigo_pa   = str_replace("PA","",$codigo_pa);


        $log_consulta_expedicao->descricao=$codigo_pa;
        $log_consulta_expedicao->salvo_em=date("Y-m-d H:i:s");
        $log_consulta_expedicao->salvo_por=Yii::$app->user->identity->id;

        $log_consulta_expedicao->save();


        $codigo_busca = str_replace("A","",$codigo_pa);
        $codigo_busca = str_replace("a","",$codigo_pa);
        $codigo_busca = str_replace("P","",$codigo_pa);
        $codigo_busca = str_replace("p","",$codigo_pa);
        
        $searchModel = new ConsultaExpedicaoBusca();



        $dataProvider = $searchModel->buscar($codigo_busca);


        return $this->render('busca', ['dataProvider'=> $dataProvider]);


    }

}
