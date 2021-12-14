<?php

namespace frontend\controllers;

use common\models\Banner;
use yii\data\ActiveDataProvider;

class PortaldasoficinasController extends \yii\web\Controller
{
    /**
     * Lists all Banner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Banner::find(),
        ]);

        return $this->render('portal', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
