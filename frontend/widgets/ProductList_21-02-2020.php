<?php
/**
 * @author Igor Mageste
 */

namespace frontend\widgets;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\BaseListView;
use yii\widgets\LinkSorter;

class ProductList extends BaseListView
{
    public $summaryOptions = ['class' => 'summary text-center pull-left'];

    public $layout = "{items}\n{pager}";

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
    }

    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $content = '';
        $this->pager['view'] = '_productList';
        foreach ($models as $index => $model) {
	    //echo $model->nome."<br><br>";
            $content .= $this->render('_productList', ['produto' => $model]);
        }

        $body = Html::tag('div', $content, ['class' => 'produtos-search']);
        $header = Html::tag('div', $this->renderSummary() . $this->renderSorter(),
            ['class' => 'search-header col-md-12 nav']);

        return $header . $body;
    }

    public function renderSorter()
    {
        $sort = $this->dataProvider->getSort();
        if ($sort === false || empty($sort->attributes) || $this->dataProvider->getCount() <= 0) {
            return '';
        }

        $attributes = array_keys($sort->attributes);
        $content = [];
        foreach ($attributes as $name) {
            $content[] = Html::tag('span', $sort->link($name, ['rel' => 'nofollow']), ['class' => 'orderby-' . $name]);
        }
        $content = implode(' | ', $content);

        return Html::tag('div', 'Ordenar por: ' . $content, ['class' => 'orderby pull-right']);
    }
}
