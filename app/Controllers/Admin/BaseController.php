<?php
/**
 * LeeCMS
 *
 * @version     1.0.0
 * @package     xiaoyao-work/LeeCMS
 *
 * @author      逍遥·李志亮 <xiaoyao.work@gmail.com>
 * @copyright   2017 逍遥·李志亮
 * @license     http://www.hhailuo.com/license
 *
 * @link        http://www.hhailuo.com/leecms
 */
namespace App\Controllers\Admin;

use \Lee\Controller;

/**
* 首页
*/
class BaseController extends Controller {
    protected $viewPathPrefix = 'admin';

    public function __construct() {
        parent::__construct();
    }
}