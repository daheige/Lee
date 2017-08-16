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
namespace Lee\Facades;

use RuntimeException;

abstract class Facade {
	/**
	 * The application instance being facaded.
	 *
	 * @var \Lee\Application
	 */
	protected static $app;

	/**
	 * The resolved object instances.
	 *
	 * @var array
	 */
	protected static $resolvedInstance;

	/**
	 * Get the root object behind the facade.
	 *
	 * @return mixed
	 */
	public static function getFacadeRoot() {
		return static::resolveFacadeInstance(static::getFacadeAccessor());
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	protected static function getFacadeAccessor() {
		throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
	}

	/**
	 * Resolve the facade root instance from the container.
	 *
	 * @param  string|object  $name
	 * @return mixed
	 */
	protected static function resolveFacadeInstance($name) {
		if (is_object($name)) {
			return $name;
		}

		if (isset(static::$resolvedInstance[$name])) {
			return static::$resolvedInstance[$name];
		}

		return static::$resolvedInstance[$name] = static::$app[$name];
	}

	/**
	 * Clear a resolved facade instance.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public static function clearResolvedInstance($name) {
		unset(static::$resolvedInstance[$name]);
	}

	/**
	 * Clear all of the resolved instances.
	 *
	 * @return void
	 */
	public static function clearResolvedInstances() {
		static::$resolvedInstance = [];
	}

	/**
	 * Get the application instance behind the facade.
	 *
	 * @return \Lee\Application
	 */
	public static function getFacadeApplication() {
		return static::$app;
	}

	/**
	 * Set the application instance.
	 *
	 * @param  \Lee\Application  $app
	 * @return void
	 */
	public static function setFacadeApplication($app) {
		static::$app = $app;
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 *
	 * @throws \RuntimeException
	 */
	public static function __callStatic($method, $args) {
		$instance = static::getFacadeRoot();

		if (!$instance) {
			throw new RuntimeException('A facade root has not been set.');
		}
        return call_user_func_array([$instance, $method], $args);
		// return $instance->$method(...$args);
	}
}
