<?php
namespace App\Controllers\Home;

use \Lee\Controller;

/**
* 首页
*/
class Index extends Controller {
    function index() {
        $title = 'Welcome to Lee framework!';
        $body = '<p>Lee - a micro PHP framework</p>' .
                '<p>@version     1.0.0</p>' .
                '<p>@package     xiaoyao-work/Lee</p>' .
                '<p>@author      逍遥·李志亮 <xiaoyao.work@gmail.com></p>' .
                '<p>@copyright   2017 逍遥·李志亮</p>' .
                '<p>@license     http://www.hhailuo.com/license</p>' .
                '<p>@link        http://www.hhailuo.com/lee</p>';
        $this->assign(['title' => $title, 'body' => $body]);
        $this->display();
    }
}