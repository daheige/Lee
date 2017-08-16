<?php
/**
 * Lee - a micro PHP framework
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

namespace Lee;

/**
 * 日志处理类
 * @author 逍遥·李志亮 <xiaoyao.working@gmail.com>
 */
class Log {
	// 日志级别 从上到下，由低到高
	const FAULT  = 'fault'; // 严重错误: 导致系统崩溃无法使用
	const ALERT  = 'alert'; // 警戒性错误: 必须被立即修改的错误
	const ERR    = 'error'; // 一般错误: 一般性错误
	const WARN   = 'warn'; // 警告性错误: 需要发出警告的错误
	const NOTICE = 'notic'; // 通知: 程序可以运行但是还不够完美的错误
	const INFO   = 'info'; // 信息: 程序输出信息
	const DEBUG  = 'debug'; // 调试: 调试信息

	// 日志信息
	static protected $log = [];
	static protected $storage = null;

    /**
     * 日志初始化
     * @param array $config 日志配置
     */
    public function __construct($config = []) {
		$type  = isset($config['type']) ? $config['type'] : 'File';
		$class = strpos($type, '\\') ? $type : '\\Lee\\Log\\Driver\\' . ucwords(strtolower($type));
		unset($config['type']);
		self::$storage = new $class($config);
	}

	static public function fault($message, $filename = '', $type = '') {
		self::write($message, self::FAULT, $filename, $type);
	}

	static public function alert($message, $filename = '', $type = '') {
		self::write($message, self::ALERT, $filename, $type);
	}

	static public function error($message, $filename = '', $type = '') {
		self::write($message, self::ERR, $filename, $type);
	}

	static public function warn($message, $filename = '', $type = '') {
		self::write($message, self::WARN, $filename, $type);
	}

	static public function notice($message, $filename = '', $type = '') {
		self::write($message, self::NOTICE, $filename, $type);
	}

	static public function info($message, $filename = '', $type = '') {
		self::write($message, self::INFO, $filename, $type);
	}

	static public function debug($message, $filename = '', $type = '') {
		self::write($message, self::DEBUG, $filename, $type);
	}

	/**
	 * 记录日志 并且会过滤未经设置的级别
	 * @static
	 * @access public
	 * @param string $message 日志信息
	 * @param string $level  日志级别
	 * @param boolean $record  是否强制记录
	 * @return void
	 */
	static function record($message, $level = self::ERR, $record = false) {
		if ($record || false !== strpos(C('LOG_LEVEL'), $level)) {
			self::$log[] = "{$level}: {$message}\r\n";
		}
	}

	/**
	 * 日志保存
	 * @static
	 * @access public
	 * @param integer $type 日志记录方式
	 * @param string $destination  写入目标
	 * @return void
	 */
	static function save($type = '', $level = self::ERR, $destination = '') {
		if (empty(self::$log)) {
			return;
		}
		if (empty($destination)) {
			$destination = app()->config('log_path') . date('y_m_d') . '.log';
		}
		if (!self::$storage) {
			$type               = $type ?: app()->config('log_type');
			$class              = '\\Lee\\Log\\Driver\\' . ucwords($type);
			self::$storage      = new $class($config);
		}
		$message = implode('', self::$log);
		self::$storage->write($message, $level, $destination);
		// 保存后清空日志缓存
		self::$log = [];
	}

	/**
	 * 写入日志到文件
	 * @static
	 * @access protected
	 * @param string $message 日志信息
	 * @param string $level  日志级别
	 * @param string $destination  写入目标
	 * @return void
	 */
	static protected function write($message, $level = self::ERR, $destination = '', $type = '') {
		if (!self::$storage) {
			$type               = $type ?: app()->config('log_type');
			$config['log_path'] = app()->config('log_path');
			$class              = '\\Lee\\Log\\Driver\\' . ucwords($type);
			self::$storage      = new $class($config);
		}
		self::$storage->write(is_string($message) ? $message : json_encode($message, JSON_UNESCAPED_UNICODE), $level, $destination);
	}
}
