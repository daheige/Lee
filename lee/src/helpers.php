<?php
use Lee\Application;

if (!function_exists('abort')) {
	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param  int     $code
	 * @param  string  $message
	 * @param  array   $headers
	 * @return void
	 *
	 * @throws \Lee\Exception\Stop
	 */
	function abort($code, $message = '') {
		return app()->halt($code, $message);
	}
}

if (!function_exists('app')) {
	/**
	 * Get the available container instance.
	 *
	 * @param  string  $make
	 * @return \Lee\Application
	 */
	function app($make = null) {
		if (is_null($make)) {
			return Application::getInstance();
		}
		return Application::getInstance($make);
	}
}

if (!function_exists('base_path')) {
	/**
	 * Get the path to the base of the install.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function base_path($path = '') {
		return app()->basePath() . ($path ? '/' . $path : $path);
	}
}

if (!function_exists('config')) {
	/**
	 * Get / set the specified configuration value.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param  array|string  $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	function config($key = null, $default = null) {
		if (is_null($key)) {
			return app()->settings;
		}

		if (is_array($key)) {
			return app('config')->set($key);
		}

		return app('config')->get($key, $default);
	}
}

if (!function_exists('database_path')) {
	/**
	 * Get the path to the database directory of the install.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function database_path($path = '') {
		return app()->databasePath() . ($path ? '/' . $path : $path);
	}
}

if (!function_exists('response')) {
	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Symfony\Component\HttpFoundation\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
	function response($content = '', $status = 200, array $headers = []) {
		return new \Lee\Http\Response($content, $status, $headers);
	}
}

if (!function_exists('storage_path')) {
	/**
	 * Get the path to the storage folder.
	 *
	 * @param  string  $path
	 * @return string
	 */
	function storage_path($path = '') {
		return app()->storagePath($path);
	}
}

if (!function_exists('route')) {
	/**
	 * Generate a url for the application.
	 *
	 * @param  string  $path
	 * @param  mixed   $parameters
	 * @param  bool    $secure
	 * @return string
	 */
	function route($name, $params = []) {
		$this->request->urlFor($name, $params);
	}
}
