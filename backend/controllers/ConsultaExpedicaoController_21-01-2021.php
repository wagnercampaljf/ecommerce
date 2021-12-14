<?php

namespace backend\controllers;

use backend\models\ConsultaExpedicaoBusca;

class ConsultaExpedicaoController extends \yii\web\Controller
{
    
    public $layout = "layout_limpo";
    
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionBusca($codigo_pa)
    {
        
        $codigo_busca = str_replace("A","",$codigo_pa);
        $codigo_busca = str_replace("a","",$codigo_pa);
        $codigo_busca = str_replace("P","",$codigo_pa);
        $codigo_busca = str_replace("p","",$codigo_pa);
        
        $searchModel = new ConsultaExpedicaoBusca();
        $dataProvider = $searchModel->buscar($codigo_busca);
        
        return $this->render('busca', ['dataProvider'=> $dataProvider]);
    }

}
