<?php

namespace MyApp\Helper;

use Config;
use Response;
use Model_Division;

/**
 * URI に関するヘルパークラス
 *
 * @package  App\Helper
 */
class Uri
{
	/**
	 * ドキュメントルート
	 * @var string
	 */
	protected static $root = '';

	/**
	 * ドキュメントルートの URI を取得
	 *
	 * @return string  URI
 	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function root()
	{
		if (! static::$root) {
			// get server information
			$base = Config::get('base_url');
			if ($base) {
				if (strrpos($base, '/') === strlen($base) - 1) {
					// remove last slash
					$base = substr($base, 0, -1);
				}
				static::$root = $base;
			} else {
				$protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
				$server = $_SERVER['HTTP_HOST'];

				// 必ずエントリポイントの public/index.php のディレクトリが取得できるってわけか
				$path = dirname($_SERVER['SCRIPT_NAME']);
				$path = str_replace('\\', '/', $path);

				// absolute URI for public dir
				$uri = $protocol . '://' . $server . $path;
				if (strrpos($uri, '/') === strlen($uri) - 1) {
					// remove last slash
					$uri = substr($uri, 0, -1);
				}
				static::$root = $uri;
			}
		}
		return static::$root;
	}
	// function root()

	/**
	 * URI 設定ファイルから該当する URI を生成
	 *
	 * @param  string $config      設定名
	 * @param  array  $params      URI の置換に必要なパラメータ
	 * @param  array  $get_params  URI に繋げるハッシュ
	 * @return string              生成された URI
	 */
	public static function create($config, array $params = [], array $get_params = []): string
	{
		$root = static::root();
		$path = Config::get('uri.' . $config);
		if ($path === null) {
			throw new \Exception('path missing "' . $config . '"');
		}
		if (strpos($path, '/') === 0) {
			// remove last slash
			$path = substr($path, 1);
		}
		return \Uri::create(
			$root . '/' . $path,
			$params,
			$get_params
		);
	}
	// function create()

	/**
	 * URI 設定ファイルから該当する URI を生成し、その URI にリダイレクト
	 * @param  string $config      設定名
	 * @param  array  $params      URI の置換に必要なパラメータ
	 * @param  array  $get_params  URI に繋げるハッシュ
	 */
	public static function redirect(
		string $config,
		array $params = [],
		array $get_params = []
	): void {
		$uri = static::create($config, $params, $get_params);
		Response::redirect($uri, 'location', 303);
	}
	// function redirect()

	/**
	 * redirect() で 301 リダイレクト（恒久的な URL 変更）
	 * @param  string $config      設定名
	 * @param  array  $params      URI の置換に必要なパラメータ
	 * @param  array  $get_params  URI に繋げるハッシュ
	 */
	public static function redirectPermanently(
		string $config,
		array $params = [],
		array $get_params = []
	): void {
		$uri = static::create($config, $params, $get_params);
		Response::redirect($uri, 'location', 301);
	}
	// function redirect()

	/**
	 * 自治体の詳細ページの URL
	 * @param  Model_Division $division  対象の自治体オブジェクト
	 * @return string                    詳細ページの URL
	 */
	public static function division(Model_Division $division): string
	{
		return static::create('division.detail', ['path' => $division->path]);
	}

	/**
	 * 自治体の詳細ページへリダイレクト
	 * @param  Model_Division $division  対象の自治体オブジェクト
	 */
	public static function redirectDivision(Model_Division $division): void
	{
		$uri = static::division($division);
		Response::redirect($uri, 'location', 303);
	}
	// function redirectDivision()

	/**
	 * 現在の URI
	 *
	 * @return string  URI
 	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function current(): string
	{
		$path = ($_SERVER['PATH_INFO'] ?? '');
		$segments = explode('/', $path);
		foreach ($segments as &$item) {
			$item = urlencode($item);
		}
		$path = implode('/', $segments);
		return static::root() . $path;
	}
	// function current()

	/**
	 * ログインページの URI
	 * @return string  URI
	 */
	public static function login(): string
	{
		return static::create('login', [], ['uri' => static::current()]);
	}

	/**
	 * ログアウトページの URI
	 * @return string  URI
	 */
	public static function logout(): string
	{
		return static::create('logout', [], ['uri' => static::current()]);
	}

	/**
	 * 自治体一覧を返す API の URI
	 * @return string  URI
	 */
	public static function restDivisionList(): string
	{
		return static::root() . '/division/list.json';
	}
}
// class Uri
