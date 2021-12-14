<?php
/**
 * Created by PhpStorm.
 * User: Ot�vio
 * Date: 25/02/2016
 * Time: 15:11
 */

namespace backend\widgets;


use frontend\widgets\Drop;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\Nav;

class Menu extends Nav
{

    /**
     * Renders widget items.
     */
//    public function renderItems()
//    {
//        echo "<pre>";
//        $items = [];
//        foreach ($this->items as $i => $item) {
//            if (isset($item['visible']) && !$item['visible']) {
//                continue;
//            }
//            foreach ($item['items'] as $k => $v) {
//                $v['label'] = $v['label'];
//                $item['items'][$k] = $v;
//            }
////
//            $items[] = $this->renderItem($item);
//        }
//        $items = "<h3>".implode("</h3><h3>", $items)."</h3>";
//        var_dump($items);
////        $items = implode(" ",$items);
//die;
//        return Html::tag('ul', $items, $this->options);
//    }

    public function renderItem($item)
    {

        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', $item['url']);
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($items !== null) {
            $linkOptions['data-toggle'] = 'dropdown';
            Html::addCssClass($options, 'dropdown');
            Html::addCssClass($linkOptions, ['dropdown-toggle', 'disabled']);
            $label .= ' ' . Html::tag('b', '', ['class' => 'caret']);
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderDropdown($items, $item);
            }
        }

//        echo "<pre>";
//        var_dump($item);
//        die;

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }

        return Html::tag('li', Html::a(Html::tag('h3', $label), $url, $linkOptions) . $items, $options);
//        return Html::tag('li', Html::tag('h3',Html::a($label, $url),['class' => implode(" ", $linkOptions)]) . $items, $options);
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @since 2.0.1
     */
    protected function renderDropdown($items, $parentItem)
    {
        return Drop::widget([
            'items' => $items,
            'encodeLabels' => $this->encodeLabels,
            'clientOptions' => false,
            'view' => $this->getView(),
        ]);
    }
}