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

class BaseController extends Controller {
	protected $viewPathPrefix = 'admin';
	protected $siteid;

	// 无需验证方法
	protected $exceptAuth = [
		'Index' => ['index', 'left', 'main', 'change_site', 'public_session_life'],
	];

	function __construct() {
		parent::__construct();

		$this->siteid = get_siteid();
        $this->controller = $this->route->getController();
        $this->action = $this->route->getAction();

        $this->filterLogin();
		if (isset($this->exceptAuth[$this->controller]) && in_array($this->action, $this->exceptAuth[$this->controller])) {
			return;
		}
		$this->filterAuth();
	}

	protected function beforeFilter($method, $params = []) {
		if (empty($params)) {
			call_user_func([$this, $method]);
		} else {
			if (isset($params['only'])) {
				if (in_array($this->action, $params['only'])) {
					call_user_func([$this, $method]);
				}
			} elseif (isset($params['except'])) {
				if (!in_array($this->action, $params['except'])) {
					call_user_func([$this, $method]);
				}
			}
		}
	}

	protected function filterLogin() {
		if (!session(C('USER_AUTH_KEY'))) {
			if (is_ajax()) {
				$this->ajaxReturn("window.top.location.reload();", 'eval');
			}
			//跳转到认证网关
			$this->assign('jumpUrl', __MODULE__ . C('USER_AUTH_GATEWAY'));
			$this->assign('waitSecond', 3);
			$this->error('请先登录后台管理');
		}
	}

	protected function filterAuth() {
		// 用户权限检查
		if (!RBAC::AccessDecision()) {
			// 没有权限 抛出错误
			if (C('RBAC_ERROR_PAGE')) {
				// 定义权限错误页面
				$this->assign('jumpUrl', __MODULE__ . C('RBAC_ERROR_PAGE'));
				$this->error('您没有权限操作该项');
				model('Log')->addLog(2);
			} else {
				if (C('GUEST_AUTH_ON')) {
					$this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
				}
				// 提示错误信息
				$this->error(L('_VALID_ACCESS_'));
			}
		}
		// 记录操作日志
		model('Log')->addLog(1);
	}

	protected function filterPostTypeAuth() {
		return true;
	}

	protected function checkToken() {
		if (IS_POST) {
			if (!M()->autoCheckToken($_POST)) {
				$this->error('[hash]数据验证失败');
			}
		}
	}

}