<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 19/10/2015
 * Time: 13:39
 */

namespace backend\actions\banner;


use common\models\Banner;
use common\models\CategoriaBanner;
use common\models\Subcategoria;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UpdateAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->atualizaSubcategorias($model);
            $this->atualizaCategoriasBanner($model);
            $this->uploadImage($model);
            if (!Yii::$app->request->post('tipo-link')) {
                $this->uploadPdf($model);
            }
            if ($model->save()) {

                return $this->controller->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->controller->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $model Banner
     */
    private function uploadImage(&$model)
    {
        if ($file = UploadedFile::getInstance($model, 'imagem')) {
            $model->imagem = base64_encode(file_get_contents($file->tempName));
        } else {
            $model->imagem = $model->oldAttributes['imagem'];
        }
    }

    /**
     * @param $model Banner
     * @return mixed
     */
    private function uploadPdf(&$model)
    {
        if (($file = UploadedFile::getInstance($model, 'pdf'))) {
            $model->pdf = base64_encode(file_get_contents($file->tempName));
            $model->link = null;
        } else {
            $model->pdf = $model->oldAttributes['pdf'];
        }
    }

    /**
     * @param $model Banner
     */
    private function atualizaSubcategorias(&$model)
    {
        $subcategoria_ids = ArrayHelper::getColumn($model->subcategorias, 'id');
        foreach ($model->subcategoria_id as $id) {
            if (!in_array($id, $subcategoria_ids)) {
                $model->link('subcategorias', Subcategoria::findOne($id));
            }
        }
        foreach ($model->subcategorias as $subcategoria) {
            if ($subcategoria && !in_array($subcategoria->id, $model->subcategoria_id)) {
                $model->unlink('subcategorias', $subcategoria, true);
            }
        }
    }

    /**
     * @param $model Banner
     */
    private function atualizaCategoriasBanner(&$model)
    {
        $categoriaBanner_ids = ArrayHelper::getColumn($model->categoriaBanners, 'id');
        foreach ($model->categoriaBanner_id as $id) {

            if (!in_array($id, $categoriaBanner_ids)) {
                $model->link('categoriaBanners', CategoriaBanner::findOne($id));
            }
        }
        foreach ($model->categoriaBanners as $categoriaBanner) {
            if ($categoriaBanner && !in_array($categoriaBanner->id, $model->categoriaBanner_id)) {
                $model->unlink('categoriaBanners', $categoriaBanner, true);
            }
        }
    }
}