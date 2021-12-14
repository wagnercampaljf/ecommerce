<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'assets/global/plugins/bootstrap/css/bootstrap.css',
        'assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css',
        'assets/global/plugins/bootstrap-switch/css/bootstrap-switch.css',
        'assets/global/plugins/font-awesome/css/font-awesome.css',
        'assets/global/plugins/simple-line-icons/simple-line-icons.css',
        'assets/global/plugins/uniform/css/uniform.default.css',
        'assets/global/css/components-rounded.css',
        'assets/global/css/plugins.css',
        'assets/admin/layout3/css/themes/default.css',
        'assets/admin/layout3/css/layout.css',
        'assets/admin/layout3/css/custom.css',
        'css/site.css',
    ];
    public $js = [
        'assets/global/plugins/bootstrap/js/bootstrap.js',
        'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
        'assets/global/plugins/bootstrap-switch/js/bootstrap-switch.js',
        'assets/global/plugins/uniform/jquery.uniform.min.js',
        'assets/global/plugins/respond.min.js',
        'assets/global/plugins/excanvas.min.js',
        'assets/global/scripts/metronic.js',
        'assets/admin/layout3/scripts/layout.js',
        'assets/admin/layout3/scripts/demo.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
