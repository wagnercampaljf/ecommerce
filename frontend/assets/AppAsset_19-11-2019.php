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
        'css/categorias.css',
        'css/landpage.css',
        'css/hover-min.css',
        'assets/fa/css/font-awesome.css',
//        'assets/select2/select2.css',
//        'assets/select2/select2-bootstrap.css',
        'assets/slider/jquery.nouislider.css',
        'assets/toastr/toastr.min.css',
        'assets/animate/animate.css',
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
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
