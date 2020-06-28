<?php

class Helper_Uri
{
	public static function root()
	{
		// get server information
		$base = Config::get('base_url');
		if ($base)
		{
			if (preg_match('/\/$/', $base))
			{
				// remove last slash
				$base = substr($base, 0, -1);
			}
			return $base;
		}
		else
		{
			$protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
			$server = $_SERVER['HTTP_HOST'];

			$path = dirname($_SERVER['SCRIPT_NAME']);
			$path = str_replace('\\', '/', $path);

			// absolute URL for public dir
			$uri = $protocol.'://'.$server.$path;
			if (preg_match('/\/$/', $uri))
			{
				// remove last slash
				$uri = substr($uri, 0, -1);
			}
			return $uri;
		}
	} // function root()

	public static function create($config, $params = [], $get_params = [])
	{
		$root = self::root();
		$path = Config::get('uri.'.$config);
		if ($path === null)
		{
			throw new Exception('path missing "'.$config.'"');
		}
		if (strpos($path, '/') === 0)
		{
			// remove last slash
			$path = substr($path, 1);
		}
		return Uri::create(
			$root.'/'.$path,
			$params,
			$get_params
		);
	} // function create()

	public static function is($uri, $params = [], $get_params = [])
	{
		$uri = self::create($uri, $params, $get_params);
		return ($uri == self::current());
	} // function is()

	public static function redirect($uri, $params = [], $get_params = [])
	{
		$uri = self::create($uri, $params, $get_params);
		Response::redirect($uri);
	} // function redirect()

	// get current uri
	public static function current($param = true)
	{
		Config::load(\Fuel::$env . '/config', 'env');
		$base_url = Config::get('env.base_url');
		if (strpos($base_url, '/') === 0)
		{
			// remove last slash
			$base_url = substr($base_url, 1);
		}

		$path = '/';
		if ($param)
		{
			$path = $_SERVER['REQUEST_URI'];
			return $base_url.$path;
		}
		else if (isset($_SERVER['PATH_INFO']))
		{
			$path = $_SERVER['PATH_INFO'];
			return self::root().$path;
		}
	} // function current()

	public static function is_top()
	{
		$root = self::root();
		$uri = self::current();

		$path = str_replace($root, '', $uri);
		return ($path === '' || $path === '/');
	} // function is_top()

	public static function is_admin()
	{
		$root = self::root();
		$uri = self::current();

		$path = str_replace($root, '', $uri);

		$admin_prefix = '/admin';
		$cmp = strncmp($path, $admin_prefix, strlen($admin_prefix));

		return ($cmp == 0);
	} // function is_admin()
} // class Helper_Uri
