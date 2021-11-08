<?php

/**
 * @package  App
 */

namespace MyApp;

/**
 * FuelPHP 本体の設定を変更できるやつ、主に phpunit で使う
 */
class MyFuel
{
	/**
	 * Fuel を読み込み
	 *
	 * phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceAfterOpenBracket
	 * phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
	 */
	protected static function load(): void
	{
		if (! defined('DOCROOT')) {
		//if (! class_exists(\Autoloader::class)) {
			define('DOCROOT',  __DIR__ . '/../../../public/');

			define('FUELPATH', __DIR__  . '/../..');
			define('APPPATH',  FUELPATH . '/app/');
			define('PKGPATH',  FUELPATH . '/packages/');
			define('COREPATH', FUELPATH . '/core/');

			require COREPATH . 'classes/autoloader.php';
			class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

			//define('FUEL_START_TIME', microtime(true));
			//define('FUEL_START_MEM', memory_get_usage());

			require APPPATH . '/bootstrap.php';

			restore_error_handler();
		}
	}

	public static function createServerHost(): void
	{
		chdir(FUELPATH . '/../public/');
		$parsed = parse_url(\Config::get('base_url'));
		$_SERVER['HTTP_HOST'] = $parsed['host'];
		$_SERVER['REQUEST_URI'] = $parsed['path'];
	}

	/**
	 * 環境を変更
	 * @param string $name  環境の定数 ['production', 'staging', 'development', 'test']
	 */
	public static function env(string $name): void
	{
		// Fuel のコアを読み込む
		//var_dump($name);
		static::load();

		// 環境を切り替え
		\Fuel::$env = $name;
	}

	/**
	 * oil コマンドを実行
	 * @param  string $command  コマンド名
	 * @return mixed            コマンドの戻り値
	 */
	public static function oil(string $command)
	{
		$prev_flag = \Fuel::$is_cli;
		\Fuel::$is_cli = true;

		\Package::load('oil');
		$result = \Oil\Refine::run($command);

		\Fuel::$is_cli = $prev_flag;
		return $result;
	}
}
