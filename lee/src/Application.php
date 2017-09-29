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
 * Lee
 * @package  Lee
 *
 * @author   逍遥·李志亮 <xiaoyao.work@gmail.com>
 *
 * @since    1.0.0
 */
class Application {
	use \Lee\Traits\RegistersExceptionHandlers;
	use \Lee\Traits\Hook;

	/**
	 * @const string
	 */
	const VERSION = '1.0.0';

	/**
	 * @var \Lee\Helper\Set
	 */
	public $container;

	/**
	 * @var array[\Lee]
	 */
	protected static $apps = [];

	/**
	 * All of the loaded configuration files.
	 *
	 * @var array
	 */
	protected $loadedConfigurations = [];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $middleware;

	/**
	 * @var mixed Callable to be invoked if application error
	 */
	protected $error;

	/**
	 * @var mixed Callable to be invoked if no matching routes are found
	 */
	protected $notFound;

	/**
	 * The base path of the application installation.
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * @var array
	 */
	protected $hooks = [
		'lee.before'          => [[]],
		'lee.before.router'   => [[]],
		'lee.before.dispatch' => [[]],
		'lee.after.dispatch'  => [[]],
		'lee.after.router'    => [[]],
		'lee.after'           => [[]],
	];

	/************************************************************************/
	/* Instantiation and Configuration **************************************/
	/************************************************************************/

	/**
	 * Constructor
	 * @param array $userSettings Associative array of application settings
	 */
	public function __construct($base_path) {
		$this->basePath = $base_path;

        // Setup IoC container
		$this->container             = new \Lee\Helper\Set();
		$this->container['settings'] = $this->getDefaultSettings();
        // load system config
        $this->configure('app');

        $this->registerAliases();
		// Default log
		$this->container->singleton('log', function ($c) {
			return new \Log($c['settings']['LOG']);
		});
		// Default environment
		$this->container->singleton('environment', function ($c) {
			return \Lee\Environment::getInstance();
		});
		// Default request
		$this->container->singleton('request', function ($c) {
			return new \Request($c['environment']);
		});

		// Default response
		$this->container->singleton('response', function ($c) {
			return new \Response();
		});

		// Default router
		$this->container->singleton('router', function ($c) {
			return new \Router();
		});

		// Define default middleware stack
		$this->middleware = [$this];
		$this->middleware(new \Lee\Middleware\Flash());
		$this->middleware(new \Lee\Middleware\MethodOverride());

		// Make default if first instance
		if (is_null(static::getInstance())) {
			$this->setName('default');
		}

		$this->registerErrorHandling();

		// 载入helper
		require __DIR__ . '/helpers.php';
	}

	/**
	 * 添加别名映射
	 * @return [type] [description]
	 */
	public function registerAliases() {
		$aliases = $this->config('aliases');
		if (is_array($aliases)) {
			foreach ($aliases as $key => $value) {
				class_alias($value, $key);
			}
		}
	}

	/**
	 * Get default application settings
	 * @return array
	 */
	public function getDefaultSettings() {
		return array_change_key_case([
			// Application
			'mode'                  => 'development',
			// Debugging
			'debug'                 => true,
			// Logging
			'log'                   => [
				'type'     => 'File',
				'log_path' => $this->storagePath(),
			],
			// View
			'templates.path'        => './templates',
			// Cookies
			'cookies'               => [
				'encrypt'     => false,
				'expires'     => 0,
				'path'        => '/',
				'domain'      => null,
				'secure'      => false,
				'httponly'    => false,
				// Encryption
				'secret_key'  => 'CHANGE_ME',
				'cipher'      => MCRYPT_RIJNDAEL_256,
				'cipher_mode' => MCRYPT_MODE_CBC,
			],
			// HTTP
			'http.version'          => '1.1',
			// Routing
			'routes.case_sensitive' => true,
		], CASE_UPPER);
	}

	/**
	 * Get application instance by name
	 * @param  string            $name The name of the Lee application
	 * @return \Lee\Lee|null
	 */
	public static function getInstance($name = 'default') {
		return isset(static::$apps[$name]) ? static::$apps[$name] : null;
	}

	/**
	 * Set Lee application name
	 * @param string $name The name of this Lee application
	 */
	public function setName($name) {
		$this->name          = $name;
		static::$apps[$name] = $this;
	}

	/**
	 * Get Lee application name
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Load a configuration file into the application.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function configure($name) {
		if (isset($this->loadedConfigurations[$name])) {
			return;
		}
		$this->loadedConfigurations[$name] = true;
		$default_config_path = realpath(__DIR__ . '/../config/' . $name . '.php');
		if (file_exists($default_config_path)) {
			$this->config(require $default_config_path);
		}
		$self_config_path = $this->basePath('config') . '/' . $name . '.php';
		if (file_exists($self_config_path)) {
			$this->config(require $self_config_path);
		}
		if (defined('APP_ENV')) {
			if (file_exists($custom_config_path = $this->basePath('config') . '/' . APP_ENV . '/' . $name . '.php')) {
				$this->config(require $custom_config_path);
			}
		}
	}

	/**
	 * Configure Lee Settings
	 *
	 * This method defines application settings and acts as a setter and a getter.
	 *
	 * If only one argument is specified and that argument is a string, the value
	 * of the setting identified by the first argument will be returned, or NULL if
	 * that setting does not exist.
	 *
	 * If only one argument is specified and that argument is an associative array,
	 * the array will be merged into the existing application settings.
	 *
	 * If two arguments are provided, the first argument is the name of the setting
	 * to be created or updated, and the second argument is the setting value.
	 *
	 * @param  string|array $name  If a string, the name of the setting to set or retrieve. Else an associated array of setting names and values
	 * @param  mixed        $value If name is a string, the value of the setting identified by $name
	 * @return mixed        The value of a setting if only one argument is a string
	 */
	public function config($name, $value = false) {
		$c = $this->container;
		if (is_array($name)) {
			if (true === $value) {
				$c['settings'] = array_replace_recursive($c['settings'], array_change_key_case($name, CASE_UPPER));
			} else {
				$c['settings'] = array_merge($c['settings'], array_change_key_case($name, CASE_UPPER));
			}
		} elseif (func_num_args() === 1) {
			if (!strpos($name, '.')) {
				$name = strtoupper($name);
				return isset($c['settings'][$name]) ? $c['settings'][$name] : null;
			}
			// 二维数组设置和获取支持
			$name    = explode('.', $name);
			$name[0] = strtoupper($name[0]);
			return isset($c['settings'][$name[0]][$name[1]]) ? $c['settings'][$name[0]][$name[1]] : null;
		} else {
			$settings = $c['settings'];
			if (!strpos($name, '.')) {
				$name            = strtoupper($name);
				$settings[$name] = $value;
			} else {
				// 二维数组设置和获取支持
				$name                         = explode('.', $name);
				$name[0]                      = strtoupper($name[0]);
				$settings[$name[0]][$name[1]] = $value;
			}
			$c['settings'] = $settings;
		}
		return null;
	}

	/**
	 * Get the path to the application "app" directory.
	 *
	 * @return string
	 */
	public function appPath($path = null) {
		return $this->basePath('app') . ($path ? '/' . ltrim($path, '/') : $path);
	}

	/**
	 * Get the base path for the application.
	 *
	 * @param  string|null  $path
	 * @return string
	 */
	public function basePath($path = null) {
		if (isset($this->basePath)) {
			return $this->basePath . ($path ? '/' . ltrim($path, '/') : $path);
		}

		if ($this->runningInConsole()) {
			$this->basePath = getcwd();
		} else {
			$this->basePath = realpath(getcwd() . '/../');
		}

		return $this->basePath($path);
	}

	/**
	 * Get the storage path for the application.
	 *
	 * @param  string|null  $path
	 * @return string
	 */
	public function storagePath($path = null) {
		$path = $this->basePath('storage') . ($path ? '/' . ltrim($path, '/') : $path);
		if (!is_dir($path)) {
			@mkdir($path, 755, true);
		}
		return $path;
	}

	/**
	 * Determine if the application is running in the console.
	 *
	 * @return bool
	 */
	public function runningInConsole() {
		return php_sapi_name() == 'cli';
	}

	/************************************************************************/
	/* Application Modes ****************************************************/
	/************************************************************************/

	/**
	 * Get application mode
	 *
	 * This method determines the application mode. It first inspects the $_ENV
	 * superglobal for key `SLIM_MODE`. If that is not found, it queries
	 * the `getenv` function. Else, it uses the application `mode` setting.
	 *
	 * @return string
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Configure Lee for a given mode
	 *
	 * This method will immediately invoke the callable if
	 * the specified mode matches the current application mode.
	 * Otherwise, the callable is ignored. This should be called
	 * only _after_ you initialize your Lee app.
	 *
	 * @param  string $mode
	 * @param  mixed  $callable
	 * @return void
	 */
	public function configureMode($mode, $callable) {
		if ($mode === $this->getMode() && is_callable($callable)) {
			call_user_func($callable);
		}
	}

	/************************************************************************/
	/* Application Accessors ************************************************/
	/************************************************************************/

	/**
	 * Get application log
	 * @return \Lee\Log
	 */
	public function log() {
		return $this->log;
	}

	/**
	 * Get a reference to the Environment object
	 * @return \Lee\Environment
	 */
	public function environment() {
		return $this->environment;
	}

	/**
	 * Get the Request object
	 * @return \Lee\Http\Request
	 */
	public function request() {
		return $this->request;
	}

	/**
	 * Get the Response object
	 * @return \Lee\Http\Response
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * Get the Router object
	 * @return \Lee\Route\Router
	 */
	public function router() {
		return $this->router;
	}

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version() {
        return 'Lee (1.0.0) (Lee Framework)';
    }

	/************************************************************************/
	/* HTTP Caching *********************************************************/
	/************************************************************************/

	/**
	 * Set Last-Modified HTTP Response Header
	 *
	 * Set the HTTP 'Last-Modified' header and stop if a conditional
	 * GET request's `If-Modified-Since` header matches the last modified time
	 * of the resource. The `time` argument is a UNIX timestamp integer value.
	 * When the current request includes an 'If-Modified-Since' header that
	 * matches the specified last modified time, the application will stop
	 * and send a '304 Not Modified' response to the client.
	 *
	 * @param  int                       $time The last modified UNIX timestamp
	 * @throws \InvalidArgumentException If provided timestamp is not an integer
	 */
	public function lastModified($time) {
		if (is_integer($time)) {
			$this->response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s T', $time));
			if ($time === strtotime($this->request->headers->get('IF_MODIFIED_SINCE'))) {
				$this->halt(304);
			}
		} else {
			throw new \InvalidArgumentException('Lee::lastModified only accepts an integer UNIX timestamp value.');
		}
	}

	/**
	 * Set ETag HTTP Response Header
	 *
	 * Set the etag header and stop if the conditional GET request matches.
	 * The `value` argument is a unique identifier for the current resource.
	 * The `type` argument indicates whether the etag should be used as a strong or
	 * weak cache validator.
	 *
	 * When the current request includes an 'If-None-Match' header with
	 * a matching etag, execution is immediately stopped. If the request
	 * method is GET or HEAD, a '304 Not Modified' response is sent.
	 *
	 * @param  string                    $value The etag value
	 * @param  string                    $type  The type of etag to create; either "strong" or "weak"
	 * @throws \InvalidArgumentException If provided type is invalid
	 */
	public function etag($value, $type = 'strong') {
		//Ensure type is correct
		if (!in_array($type, ['strong', 'weak'])) {
			throw new \InvalidArgumentException('Invalid Lee::etag type. Expected "strong" or "weak".');
		}

		//Set etag value
		$value = '"' . $value . '"';
		if ($type === 'weak') {
			$value = 'W/' . $value;
		}
		$this->response['ETag'] = $value;

		//Check conditional GET
		if ($etagsHeader = $this->request->headers->get('IF_NONE_MATCH')) {
			$etags = preg_split('@\s*,\s*@', $etagsHeader);
			if (in_array($value, $etags) || in_array('*', $etags)) {
				$this->halt(304);
			}
		}
	}

	/**
	 * Set Expires HTTP response header
	 *
	 * The `Expires` header tells the HTTP client the time at which
	 * the current resource should be considered stale. At that time the HTTP
	 * client will send a conditional GET request to the server; the server
	 * may return a 200 OK if the resource has changed, else a 304 Not Modified
	 * if the resource has not changed. The `Expires` header should be used in
	 * conjunction with the `etag()` or `lastModified()` methods above.
	 *
	 *                              If int, a UNIX timestamp;
	 * @param string|int $time If string, a time to be parsed by `strtotime()`;
	 */
	public function expires($time) {
		if (is_string($time)) {
			$time = strtotime($time);
		}
		$this->response->headers->set('Expires', gmdate('D, d M Y H:i:s T', $time));
	}

	/************************************************************************/
	/* HTTP Cookies *********************************************************/
	/************************************************************************/

	/**
	 * Set HTTP cookie to be sent with the HTTP response
	 *
	 *                                  If integer, should be UNIX timestamp;
	 *                                  If string, converted to UNIX timestamp with `strtotime`;
	 *                              HTTPS connection to/from the client
	 * @param string     $name     The cookie name
	 * @param string     $value    The cookie value
	 * @param int|string $time     The duration of the cookie;
	 * @param string     $path     The path on the server in which the cookie will be available on
	 * @param string     $domain   The domain that the cookie is available to
	 * @param bool       $secure   Indicates that the cookie should only be transmitted over a secure
	 * @param bool       $httponly When TRUE the cookie will be made accessible only through the HTTP protocol
	 */
	public function setCookie($name, $value, $options = []) {
		$options = array_merge($this->config('cookies'), $options);
		$settings = [
			'value'    => $value,
			'expires'  => $options['expires'],
			'path'     => $options['path'],
			'domain'   => $options['domain'],
			'secure'   => $options['secure'],
			'httponly' => $options['httponly'],
		];
		$this->response->cookies->set($name, $settings);
	}

	/**
	 * Get value of HTTP cookie from the current HTTP request
	 *
	 * Return the value of a cookie from the current HTTP request,
	 * or return NULL if cookie does not exist. Cookies created during
	 * the current request will not be available until the next request.
	 *
	 * @param  string        $name
	 * @param  bool          $deleteIfInvalid
	 * @return string|null
	 */
	public function getCookie($name, $deleteIfInvalid = true) {
		// Get cookie value
		$value = $this->request->cookies->get($name);
		var_dump($value);
		// Decode if encrypted
		if ($this->config('cookies.encrypt')) {
			$value = \Lee\Http\Util::decodeSecureCookie(
				$value,
				$this->config('cookies.secret_key'),
				$this->config('cookies.cipher'),
				$this->config('cookies.cipher_mode')
			);
			var_dump($value);
			if ($value === false && $deleteIfInvalid) {
				$this->deleteCookie($name);
			}
		}

		/**
		 * transform $value to @return doc requirement.
		 * \Lee\Http\Util::decodeSecureCookie -  is able
		 * to return false and we have to cast it to null.
		 */
		return $value === false ? null : $value;
	}

	/**
	 * Delete HTTP cookie (encrypted or unencrypted)
	 *
	 * Remove a Cookie from the client. This method will overwrite an existing Cookie
	 * with a new, empty, auto-expiring Cookie. This method's arguments must match
	 * the original Cookie's respective arguments for the original Cookie to be
	 * removed. If any of this method's arguments are omitted or set to NULL, the
	 * default Cookie setting values (set during Lee::init) will be used instead.
	 *
	 *                              HTTPS connection from the client
	 * @param string $name     The cookie name
	 * @param string $path     The path on the server in which the cookie will be available on
	 * @param string $domain   The domain that the cookie is available to
	 * @param bool   $secure   Indicates that the cookie should only be transmitted over a secure
	 * @param bool   $httponly When TRUE the cookie will be made accessible only through the HTTP protocol
	 */
	public function deleteCookie($name, $path = null, $domain = null, $secure = null, $httponly = null) {
		$settings = [
			'domain'   => is_null($domain) ? $this->config('cookies.domain') : $domain,
			'path'     => is_null($path) ? $this->config('cookies.path') : $path,
			'secure'   => is_null($secure) ? $this->config('cookies.secure') : $secure,
			'httponly' => is_null($httponly) ? $this->config('cookies.httponly') : $httponly,
		];
		$this->response->cookies->remove($name, $settings);
	}

	/************************************************************************/
	/* Helper Methods *******************************************************/
	/************************************************************************/

	/**
	 * Get the absolute path to this Lee application's root directory
	 *
	 * This method returns the absolute path to the Lee application's
	 * directory. If the Lee application is installed in a public-accessible
	 * sub-directory, the sub-directory path will be included. This method
	 * will always return an absolute path WITH a trailing slash.
	 *
	 * @return string
	 */
	public function root() {
		return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . rtrim($this->request->getRootUri(), '/') . '/';
	}

	/**
	 * Clean current output buffer
	 */
	protected function cleanBuffer() {
		if (ob_get_level() !== 0) {
			ob_clean();
		}
	}

	/**
	 * Stop
	 *
	 * The thrown exception will be caught in application's `call()` method
	 * and the response will be sent as is to the HTTP client.
	 *
	 * @throws \Lee\Exception\Stop
	 */
	public function stop() {
		throw new \Lee\Exception\Stop();
	}

	/**
	 * Halt
	 *
	 * Stop the application and immediately send the response with a
	 * specific status and body to the HTTP client. This may send any
	 * type of response: info, success, redirect, client error, or server error.
	 * If you need to render a template AND customize the response status,
	 * use the application's `render()` method instead.
	 *
	 * @param int    $status  The HTTP response status
	 * @param string $message The HTTP response body
	 */
	public function halt($status, $message = '') {
		$this->cleanBuffer();
		$this->response->status($status);
		$this->response->body($message);
		$this->stop();
	}

	/**
	 * Pass
	 *
	 * The thrown exception is caught in the application's `call()` method causing
	 * the router's current iteration to stop and continue to the subsequent route if available.
	 * If no subsequent matching routes are found, a 404 response will be sent to the client.
	 *
	 * @throws \Lee\Exception\Pass
	 */
	public function pass() {
		$this->cleanBuffer();
		throw new \Lee\Exception\Pass();
	}

	/**
	 * Set the HTTP response Content-Type
	 * @param string $type The Content-Type for the Response (ie. text/html)
	 */
	public function contentType($type) {
		$this->response->headers->set('Content-Type', $type);
	}

	/**
	 * Set the HTTP response status code
	 * @param int $code The HTTP response status code
	 */
	public function status($code) {
		$this->response->setStatus($code);
	}

	/**
	 * Get the URL for a named route
	 * @param  string            $name   The route name
	 * @param  array             $params Associative array of URL parameters and replacement values
	 * @throws \RuntimeException If named route does not exist
	 * @return string
	 */
	public function urlFor($name, $params = []) {
		return $this->request->getRootUri() . $this->router->urlFor($name, $params);
	}

	/**
	 * Redirect
	 *
	 * This method immediately redirects to a new URL. By default,
	 * this issues a 302 Found response; this is considered the default
	 * generic redirect response. You may also specify another valid
	 * 3xx status code if you want. This method will automatically set the
	 * HTTP Location header for you using the URL parameter.
	 *
	 * @param string $url    The destination URL
	 * @param int    $status The HTTP redirect status code (optional)
	 */
	public function redirect($url, $status = 302) {
		$this->response->redirect($url, $status);
		$this->halt($status);
	}

	/**
	 * RedirectTo
	 *
	 * Redirects to a specific named route
	 *
	 * @param string $route  The route name
	 * @param array  $params Associative array of URL parameters and replacement values
	 */
	public function redirectTo($route, $params = [], $status = 302) {
		$this->redirect($this->urlFor($route, $params), $status);
	}

	/************************************************************************/
	/* Flash Messages *******************************************************/
	/************************************************************************/

	/**
	 * Set flash message for subsequent request
	 * @param string $key
	 * @param mixed  $value
	 */
	public function flash($key, $value) {
		if (isset($this->environment['lee.flash'])) {
			$this->environment['lee.flash']->set($key, $value);
		}
	}

	/**
	 * Set flash message for current request
	 * @param string $key
	 * @param mixed  $value
	 */
	public function flashNow($key, $value) {
		if (isset($this->environment['lee.flash'])) {
			$this->environment['lee.flash']->now($key, $value);
		}
	}

	/**
	 * Keep flash messages from previous request for subsequent request
	 */
	public function flashKeep() {
		if (isset($this->environment['lee.flash'])) {
			$this->environment['lee.flash']->keep();
		}
	}

	/**
	 * Get all flash messages
	 */
	public function flashData() {
		if (isset($this->environment['lee.flash'])) {
			return $this->environment['lee.flash']->getMessages();
		}
	}

	/****************************************************************************/
	/* Middleware ***************************************************************/
	/****************************************************************************/

	/**
	 * add middleware
	 *
	 * This method prepends new middleware to the application middleware stack.
	 * The argument must be an instance that subclasses Lee_Middleware.
	 *
	 * @param \Lee\Middleware
	 */
	public function middleware(\Lee\Middleware $newMiddleware) {
		if (in_array($newMiddleware, $this->middleware)) {
			$middleware_class = get_class($newMiddleware);
			throw new \RuntimeException("Circular Middleware setup detected. Tried to queue the same Middleware instance ({$middleware_class}) twice.");
		}
		$newMiddleware->setApplication($this);
		$newMiddleware->setNextMiddleware($this->middleware[0]);
		array_unshift($this->middleware, $newMiddleware);
	}

	/****************************************************************************/
	/* Runner *******************************************************************/
	/****************************************************************************/

	/**
	 * Run
	 *
	 * This method invokes the middleware stack, including the core Lee application;
	 * the result is an array of HTTP status, header, and body. These three items
	 * are returned to the HTTP client.
	 */
	public function run() {
		//Invoke middleware and application stack
		$this->middleware[0]->call();

		$this->response()->send();
		$this->applyHook('lee.after');
	}

	/**
	 * Call
	 *
	 * This method finds and iterates all route objects that match the current request URI.
	 */
	public function call() {
		try {
			/*if (isset($this->environment['lee.flash'])) {
				$this->view()->setData('flash', $this->environment['lee.flash']);
			}*/
			$this->applyHook('lee.before');
			$this->applyHook('lee.before.router');
			$dispatched    = false;
			$matchedRoutes = $this->router->getMatchedRoutes($this->request->getMethod(), $this->request->getResourceUri(), $this->request->getHost());
			foreach ($matchedRoutes as $route) {
				try {
					$this->applyHook('lee.before.dispatch');
					$dispatched = $route->dispatch();
					$this->applyHook('lee.after.dispatch');
					if ($dispatched) {
						break;
					}
				} catch (\Lee\Exception\Pass $e) {
					continue;
				}
			}
			if (!$dispatched) {
				$this->notFound();
			}
			$this->applyHook('lee.after.router');
		} catch (\Lee\Exception\Stop $e) {
            $this->response()->write(ob_get_clean());
        }
	}

	public function __get($name) {
		return $this->container->get($name);
	}

	public function __set($name, $value) {
		$this->container->set($name, $value);
	}

	public function __isset($name) {
		return $this->container->has($name);
	}

	public function __unset($name) {
		$this->container->remove($name);
	}

}
