<?php
    namespace App\Controllers\Home;

    /**
    * 首页
    */
    class IndexController {
        function index() {
            $title = 'Welcome to Lee framework!';
            $body = '<p>Lee - a micro PHP framework</p>' .
                    '<p>@version     1.0.0</p>' .
                    '<p>@package     xiaoyao-work/Lee</p>' .
                    '<p>@author      逍遥·李志亮 <xiaoyao.work@gmail.com></p>' .
                    '<p>@copyright   2017 逍遥·李志亮</p>' .
                    '<p>@license     http://www.hhailuo.com/license</p>' .
                    '<p>@link        http://www.hhailuo.com/lee</p>';
            echo sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>", $title, $title, $body);
        }
    }