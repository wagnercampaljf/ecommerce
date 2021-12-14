<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $sourcePath = null;
    public $css = [
        'css/site.css',
        'css/style.css',
        'css/categorias.css',
        'css/landpage.css',
        'css/hover-min.css',
        'assets/fa/css/font-awesome.css',
        'css/bootstrap-min.css',

        'css/cartao.css',
//        'assets/select2/select2.css',
//        'assets/select2/select2-bootstrap.css',

        //'menu',

//'assets/wow/css/libs/animate.css',

        //'assets/wow/css/libs/animate.css',
    ];
    public $js = [
//        'assets/select2/select2.js',
        'js/site.js',
        'assets/slider/jquery.nouislider.all.min.js',
        'assets/toastr/toastr.min.js',
        'assets/mask/jquery.mask.js',
        'assets/wow/dist/wow.min.js',
        'js/analyticstracking.js',
        'js/masks.js',
        'js/readmore.js',
        'js/jquery.elevatezoom.js',
        'js/jquery.elevateZoom-3.0.8.min.js',
        'js/jquery-1.8.3.min.js',
        'js/slick.js',
        'js/main.js',

        'js/cartao.js',




    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
