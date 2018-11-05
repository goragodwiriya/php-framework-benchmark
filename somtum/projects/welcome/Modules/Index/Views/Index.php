<?php
/**
 * @filesource Modules/Index/Views/Index.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

namespace Index\Index;

/*
 * default View
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */

class View extends \Somtum\View
{
    public function render()
    {
        echo '<html style="height:100%;width:100%"><head>';
        echo '<meta charset=utf-8>';
        echo '<link href="https://fonts.googleapis.com/css?family=Unica+One" rel="stylesheet">';
        echo '<meta name=viewport content="width=device-width, initial-scale=1.0">';
        echo '<style>';
        echo '.warper{display:inline-block;text-align:center;height:50%;}';
        echo '.warper::before{content:"";display:inline-block;height:100%;vertical-align:middle;width:0px;}';
        echo '</style>';
        echo '</head><body style="height:100%;width:100%;margin:0;font-family:\'Unica One\', cursive, Tahoma, Loma;color:#666;">';
        echo '<div class=warper style="display:block"><div class="warper"><div>';
        echo '<h1 style="line-height:1;margin:0;text-shadow:3px 3px 0 rgba(0,0,0,0.1);font-weight:normal;font-size:80px;">SOMTUM</h1>';
        echo 'PHP Micro Framework';
        echo '</div></div></body></html>';
    }
}
