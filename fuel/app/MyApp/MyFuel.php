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
			define('DOCROOT',  __DIR__);
			define('FUELPATH', __DIR__  . '/../..');
			define('APPPATH',  FUELPATH . '/app/');
			define('PKGPATH',  FUELPATH . '/packages/');
			define('COREPATH', FUELPATH . '/core/');
			require COREPATH . 'classes/autoloader.php';

			class_alias('Fuel\\Core\\Autoloader', 'Autoloader');
			require APPPATH . '/bootstrap.php';

			restore_error_handler();
		}
	}

	/**
	 * 環境を変更
	 * @param string $name  環境の定数 ['production', 'staging', 'development', 'test']
	 */
	public static function env(string $name): void
	{
		// Fuel のコアを読み込む
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
		\Package::load('oil');
		return \Oil\Refine::run($command);
	}
}
