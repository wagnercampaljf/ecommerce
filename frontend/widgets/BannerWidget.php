<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 09/10/2015
 * Time: 13:47
 */

namespace frontend\widgets;


use common\models\Banner;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class BannerWidget extends \yii\bootstrap\Widget
{
    public $options = [];

    public $linkOptions = [];

    public $imgOptions = [];

    public $subcategoria = null;

    public $cidade = null;

    public $produto = null;

    public $posicao_banner = null;

    public $banner = null;

    private static $posicoes = [
        'Banner_Busca_1' => 1,
        'Banner_Busca_2' => 2,
    ];

    private static $posicoes_default = [

    ];

    public function init()
    {
        parent::init();
        if (!is_object($this->banner)) {
            $this->prepareBanner();
        }
    }

    public function run()
    {
        if (is_null($this->posicao_banner) || !is_object($this->banner)) {
            return null;
        }

        $img = Html::img('data:image/png;base64,' . stream_get_contents($this->banner->imagem),
            ArrayHelper::merge([
                'title' => $this->banner->nome,
                'alt' => $this->banner->nome,
                'width' => $this->banner->posicao->largura,
                'height' => $this->banner->posicao->altura,
            ], $this->imgOptions));
        $link = Html::a($img, $this->getLink($this->banner),
            ArrayHelper::merge(['target' => '_blank','rel' => 'nofollow'], $this->linkOptions));
        Html::addCssClass($this->options, $this->banner->posicao->class);

        return Html::tag('div', $link, $this->options);

    }

    public function prepareBanner()
    {
        $this->banner = is_int($this->banner) ? Banner::findOne($this->banner) : Banner::find()
            ->byCidade($this->cidade)
            ->byPosicao(ArrayHelper::getValue(self::$posicoes, $this->posicao_banner))
            ->bySubCategoria($this->subcategoria)
            ->byProduto($this->produto)
            ->ativo()
            ->one();

        if (!$this->banner) {
            $this->banner = Banner::find()->byPosicao(ArrayHelper::getValue(
                self::$posicoes_default, $this->posicao_banner))->one();
        }

    }

    /**
     * @param $banner Banner
     * @return string
     */
    public function getLink($banner)
    {
        if ($banner->link) {
            return Url::to($banner->link);
        } else {
            return Url::to('data:application/pdf;base64,' . stream_get_contents($banner->pdf));
        }
    }
}