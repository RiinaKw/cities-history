<?php

class Helper_Uri
{
	public static function root()
	{
		// 各種サーバ情報を取得
		$protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
		$server = $_SERVER['HTTP_HOST'];

		$path = dirname($_SERVER['SCRIPT_NAME']);
		$path = str_replace('\\', '/', $path);

		// publicディレクトリへの絶対URLを生成
		$uri = $protocol.'://'.$server.$path;
		if (preg_match('/\/$/', $uri))
		{
			// スラッシュで終わる場合は削除
			$uri = substr($uri, 0, -1);
		}
		return $uri;
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
			// スラッシュで始まる場合は削除
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

	// URLを取得
	public static function current($param = true)
	{
		$path = '/';
		if ($param)
		{
			$protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
			$server = $_SERVER['HTTP_HOST'];
			$path = $_SERVER['REQUEST_URI'];
			return $protocol.'://'.$server.$path;
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
