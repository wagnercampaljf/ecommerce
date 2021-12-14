<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lojista\assets;

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
        'assets/global/plugins/bootstrap/css/bootstrap.css',
        'assets/global/plugins/font-awesome/css/font-awesome.min.css',
        'assets/global/plugins/simple-line-icons/simple-line-icons.min.css',
        'assets/global/plugins/bootstrap/css/bootstrap.css',
        'assets/global/plugins/uniform/css/uniform.default.css',
        'assets/global/css/components-rounded.css',
        'assets/global/css/plugins.css',
        'css/site.css',
        'css/hover-min.css',
    ];
    public $js = [
        'assets/global/plugins/respond.min.js',
        'assets/global/plugins/excanvas.min.js',
//        'assets/global/plugins/jquery.min.js',
        'assets/global/plugins/jquery-migrate.min.js',
        'assets/global/scripts/metronic.js',
        'assets/global/scripts/index.js',
        'assets/global/plugins/jquery-ui/jquery-ui.min.js',
        'assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
        'assets/global/plugins/jquery.bui.min.js',
        'assets/global/plugins/jquery.cokie.min.js',
        'assets/global/plugins/uniform/jquery.uniform.min.js',
        'assets/global/plugins/flot/jquery.flot.js',
        'assets/global/plugins/flot/jquery.flot.resize.js',
        'assets/global/plugins/flot/jquery.flot.categories.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
