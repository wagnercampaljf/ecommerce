<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 27/09/2016
 * Time: 11:41
 */

namespace frontend\controllers;

use common\models\Categoria;
use common\models\CategoriaModelo;
use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use common\models\ProdutoFilial;
use frontend\models\ProdutoSearch;
use common\models\Subcategoria;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

class VeiculosController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $categoriasModelos = CategoriaModelo::find()->orderBy('nome')->all();
        return $this->render('index', ['categoriasModelos' => $categoriasModelos]);
    }

    public function actionModelos($tipo, $marca)
    {
        $categoriaModelo = CategoriaModelo::findOne(['slug' => $tipo]);
        $modelos = Modelo::find()
            ->joinWith('marca', ['modelo.marca_id' => 'marca.id'])
            ->andWhere(['marca.slug' => $marca])
            ->all();
        return $this->render('modelos', ['marca' => $marca, 'modelos' => $modelos, 'categoriaModelo' => $categoriaModelo]);
    }

    public function actionMarcas($tipo)
    {
        $categoriaModelo = CategoriaModelo::findOne(['slug' => $tipo]);
        $marcas = Marca::find()
            ->byCategoriaSlug($tipo)
            ->all();
        return $this->render('marcas', ['marcas' => $marcas, 'categoriaModelo' => $categoriaModelo]);
    }


}
