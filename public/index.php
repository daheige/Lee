<?php
/**
 * Lee - a micro PHP framework
 * [ 轻量级PHP框架 ]
 *
 * @version     1.0.0
 * @package     xiaoyao-work/Lee
 *
 * @author      逍遥·李志亮 <xiaoyao.work@gmail.com>
 * @copyright   2017 逍遥·李志亮
 * @license     http://www.hhailuo.com/license
 *
 * @link        http://www.hhailuo.com/lee
 */

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('require PHP > 5.4.0 !');
}

define("APP_ENV", isset($_SERVER['APP_ENV']) ? strtolower($_SERVER['APP_ENV']) : 'local');

define('APP_DEBUG', in_array(APP_ENV, ['local', 'testing']));

require __DIR__.'/../bootstrap/app.php';
