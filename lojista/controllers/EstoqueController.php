<?php

namespace lojista\controllers;

use common\models\ProdutoFilial;
use common\models\UploadForm;
use lojista\controllers\actions\estoque;
use lojista\models\ProdutoFilialSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EstoqueController implements the CRUD actions for ProdutoFilial model.
 */
class EstoqueController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'lojista\controllers\actions\estoque\UploadAction',
            ]
        ];
    }

    /**
     * Lists all ProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '300M');
        $searchModel = new ProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $uploadForm = new UploadForm();

        if ($errors = Yii::$app->session->get('errors')) {
            $textoError = '';
            foreach ($errors as $linha => $error) {
                foreach ($error as $attribute => $text) {
                    $linha++;
                    $li = Html::tag(
                        'li',
                        implode('</li><li>', $text)
                    );
                    $ul = Html::tag('ul', $li);
                    $textoError .= Html::tag('p', "Na linha $linha:" . $ul, ['class' => 'container']);
                }
            }
            Yii::$app->session->setFlash('error', $textoError);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'uploadForm' => $uploadForm
        ]);
    }

    /**
     * Finds the ProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProdutoFilial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
