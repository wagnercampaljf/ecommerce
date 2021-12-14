<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 16/05/2016
 * Time: 11:24
 */

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Produto;
use common\models\ProdutoFilial;
use frontend\models\ProdutoSearch;
use common\models\Subcategoria;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

class CategoriasController extends \yii\web\Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Lists all Banner models.
     * @return mixed
     */
    public function actionAuto($categoria)
    {
        $categoria = Categoria::findOne(['slug' => $categoria]);
        if ($categoria != null) {
            $subCategorias = $categoria->subcategorias;
            if (!empty($subCategorias)) {
                $categoria_id["categoria_id"] = $categoria['id'];
                $searchModel = new ProdutoSearch();
                $dataProvider = $searchModel->search($categoria_id);
                return $this->render('categorias', [
                    'categoria' => $categoria,
                    'subCategorias' => $subCategorias,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
                ]);
            }
        }
        throw new NotFoundHttpException("Página não encontrada!");
    }

    public function actionSubcategoria($subcategoria)
    {
        $subcategoria = Subcategoria::findOne(['slug' => $subcategoria]);
        if ($subcategoria != null) {
            $categoria = ArrayHelper::toArray($subcategoria->categoria);
            $categoria = Categoria::findOne(['slug' => $categoria["slug"]]);
            $subCategorias = $categoria->subcategorias;

            $subcategoria_id["subcategoria_id"] = $subcategoria['id'];
            $subcategoria_id += Yii::$app->request->get();
            $searchModel = new ProdutoSearch();
            $dataProvider = $searchModel->search($subcategoria_id);
            if (!empty($dataProvider)) {
                return $this->render('subcategoria', [
                    'dataProvider' => $dataProvider,
                    'subcategoria' => $subcategoria,
                    'subCategorias' => $subCategorias,
                    'categoria' => $categoria,
                    'searchModel' => $searchModel
                ]);
            }
        }
        throw new NotFoundHttpException("Página não encontrada!");
    }

}
