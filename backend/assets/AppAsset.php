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
    public $sourcePath = '@backend/web';
    public $css = [
        'vendors/bootstrap/dist/css/bootstrap.min.css',
        'vendors/font-awesome/css/font-awesome.min.css',
        'vendors/iCheck/skins/flat/green.css',
        'vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css',
        'build/css/custom.css',
        'css/custom.css',
        'css/maps/jquery-jvectormap-2.0.3.css',
        'css/tree/style.css',
    ];
    public $js = [
//        'vendors/jquery/dist/jquery.min.js',
        'vendors/bootstrap/dist/js/bootstrap.min.js',
        'vendors/fastclick/lib/fastclick.js',
        'vendors/nprogress/nprogress.js',
        'vendors/Chart.js/dist/Chart.min.js',
        'vendors/bernii/gauge.js/dist/gauge.min.js',
        'vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
        'vendors/iCheck/icheck.min.js',
        'vendors/skycons/skycons.js',
        'vendors/Flot/jquery.flot.js',
        'vendors/Flot/jquery.flot.pie.js',
        'vendors/Flot/jquery.flot.time.js',
        'vendors/Flot/jquery.flot.stack.js',
        'vendors/Flot/jquery.flot.resize.js',
        'js/flot/jquery.flot.orderBars.js',
        'js/flot/date.js',
        'js/flot/jquery.flot.spline.js',
        'js/flot/curvedLines.js',
        'js/maps/jquery-jvectormap-2.0.3.min.js',
        'js/moment/moment.min.js',
        'js/moment-timezone-with-data.js',
        'js/datepicker/daterangepicker.js',
        'build/js/custom.min.js',
        'js/jquery.countdown.min.js',
        'js/tree/jquery-migrate-1.3.0.js',
        'js/tree/jquery-ui.js',
        'js/tree/jquery.tree.js',
        'js/bootstrap-notify/bootstrap-notify.min.js'
        
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_END
    );
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
