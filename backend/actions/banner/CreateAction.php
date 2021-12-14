<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 16/10/2015
 * Time: 09:52
 */

namespace backend\actions\banner;


use common\models\Banner;
use common\models\CategoriaBanner;
use common\models\Subcategoria;
use Yii;
use yii\base\Action;
use yii\web\UploadedFile;

class CreateAction extends Action
{

    public function run()
    {
        $model = new Banner();
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImage($model);
            $this->uploadPdf($model);
            if ($model->save()) {
                $this->linkSubcategorias($model);
                $this->linkCategoriaBanner($model);

                return $this->controller->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->controller->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->controller->render('create', [
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
        }
    }

    /**
     * @param $model Banner
     */
    private function uploadPdf($model)
    {
        if (!$model->link && ($file = UploadedFile::getInstance($model, 'pdf'))) {
            $model->pdf = base64_encode(file_get_contents($file->tempName));
        }
    }

    /**
     * @param $model Banner
     */
    private function linkSubcategorias(&$model)
    {
        foreach ($model->subcategoria_id as $id) {
            if ($subcategoria = Subcategoria::findOne($id)) {
                $model->link('subcategorias', $subcategoria);
            }
        }
    }

    /**
     * @param $model Banner
     */
    private function linkCategoriaBanner(&$model)
    {
        foreach ($model->categoriaBanner_id as $id) {
            if ($categoriaBanner = CategoriaBanner::findOne($id)) {
                $model->link('categoriaBanners', $categoriaBanner);
            }
        }
    }
}