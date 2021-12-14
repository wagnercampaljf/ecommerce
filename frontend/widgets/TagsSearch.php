<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 26/10/2015
 * Time: 14:49
 */

namespace frontend\widgets;


use common\models\SearchModel;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class TagsSearch extends Widget
{
    public $params = null;

    public $attributes = [];

    public function init()
    {
        if (is_array($this->params)) {
            foreach ($this->params as $k => $val) {
                if (ArrayHelper::keyExists($k, $this->attributes) && !empty($val)) {
                    $className = ArrayHelper::getValue($this->attributes, $k);
                    $this->attributes[$k] = $className::findOne($val);
                }
            }
        }
    }

    public function run()
    {
        $retorno = '';
        $href = ArrayHelper::merge([''], $this->params);
        foreach ($this->attributes as $k => $model) {
            if ($model instanceof SearchModel) {
                ArrayHelper::remove($href, $k);
                $link = Html::a('', $href, ['data' => ['role' => 'remove']]);
                $retorno .= Html::tag('span', $model->labelSearch . $link, ['class' => 'tag label label-info']);
                $href = ArrayHelper::merge([''], $this->params);
            }
        }

        return $retorno;
    }

    public static function renderTitle($params, $attributes)
    {
        $retorno = '';
        if (is_array($params)) {
            foreach ($params as $k => $val) {
                if (ArrayHelper::keyExists($k, $attributes) && !empty($val)) {
                    $className = ArrayHelper::getValue($attributes, $k);
                    $model = $className::findOne($val);
                    if ($model instanceof SearchModel) {
                        $retorno .= $model->labelSearch . ' ';
                    }
                }
            }
        }

        return $retorno;
    }
}