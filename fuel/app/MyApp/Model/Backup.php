<?php

/**
 * @package  App\Model
 */

namespace MyApp\Model;

use MyApp\Model\File;

class Backup
{
	/**
	 * バックアップファイルの保存先ディレクトリ
	 *
	 * static::connection() で決定する
	 * @var string
	 */
	protected static $dir = '';

	/**
	 * バックアップファイルとして認識するファイルの拡張子
	 * @var string
	 */
	protected static $ext = 'sql';

	/**
	 * データベース接続情報
	 * @var array<string, mixed>
	 */
	protected static $connection = [];

	/**
	 * データベース接続情報を設定
	 *
	 * @return array<string, mixed>  接続情報の連想配列
	 */
	private static function connection(): array
	{
		if (! static::$connection) {
			$config = \Config::load(\Fuel::$env . '/db.php');
			$connection = $config['default']['connection'];

			preg_match('/^.+?:host=(?<host>.+?);dbname=(?<db>.+?)$/', $connection['dsn'], $matches);

			return [
				'dsn' => $connection['dsn'],
				'host' => $matches['host'],
				'port' => 3306,
				'db' => $matches['db'],
				'user' => $connection['username'],
				'password' => $connection['password'],
			];
		}
		return static::$connection;
	}

	/**
	 * 対象のデータベース名
	 *
	 * @return string
	 */
	public static function db(): string
	{
		return static::connection()['db'];
	}

	/**
	 * テーブル一覧を取得
	 *
	 * @return array<string, bool>  テーブル名をキーとした配列
	 */
	private static function tables(): array
	{
		$tables = [];
		$column = 'Tables_in_' . static::db();
		foreach (\DB::query('SHOW TABLES')->execute() as $table) {
			$table_name = $table[$column];
			$tables[$table_name] = true;
		}
		return $tables;
	}

	/**
	 * すべてのテーブルを空にする
	 *
	 * @param array<int, string> $without  除外するテーブル
	 */
	public static function truncate(array $without = []): void
	{
		// find truncate tables
		$truncate_tables = static::tables();

		$without[] = 'migration';
		$without[] = 'users';
		foreach ($without as $table) {
			unset($truncate_tables[$table]);
		}

		\DB::query('SET FOREIGN_KEY_CHECKS=0;')->execute();
		foreach (array_keys($truncate_tables) as $table) {
			\DBUtil::truncate_table($table);
		}
		\DB::query('SET FOREIGN_KEY_CHECKS=1;')->execute();
	}

	/**
	 * mysql からエクスポート
	 *
	 * @param  string $path                        出力先のファイルパス
	 * @param  array<int, string>  $ignore_tables  除外するテーブル
	 * @return bool                                成功したかどうか
	 */
	public static function export(string $path, array $ignore_tables = []): bool
	{
		$connection = static::connection();
		$host = escapeshellcmd($connection['host']);
		$port = escapeshellcmd($connection['port']);
		$db = escapeshellcmd($connection['db']);
		$user = escapeshellcmd($connection['user']);
		$password = escapeshellcmd($connection['password']);

		$ignore_tables[] = 'migration';
		$ignore_tables[] = 'users';
		if ($ignore_tables) {
			$ignore_tables_str = '';
			foreach ($ignore_tables as $table) {
				if ($table) {
					$ignore_tables_str .= "--ignore-table={$db}.{$table} ";
				}
			}
		}

		if ($password) {
			$password = '-p' . $password;
		} else {
			$password = '';
		}

		$only_data = '-t';
		$complete_insert = '--complete-insert';

		$path_escaped = escapeshellcmd($path);
		$command = 'mysqldump'
			. " -u{$user} {$password} -h {$host} -P {$port} {$db}"
			. " {$ignore_tables_str} {$only_data} {$complete_insert}"
			. " > {$path_escaped}";
		exec($command);

		return realpath($path) !== false;
	}

	/**
	 * mysql へインポート
	 *
	 * @param string $path  入力元のファイルパス
	 */
	public static function import(string $path): void
	{
		$file = new File($path);

		$connection = static::connection();
		$host = escapeshellcmd($connection['host']);
		$port = escapeshellcmd($connection['port']);
		$db = escapeshellcmd($connection['db']);
		$user = escapeshellcmd($connection['user']);
		$password = escapeshellcmd($connection['password']);

		$path_escaped = escapeshellcmd($file->path);
		$command = 'mysql'
			. " -u {$user} -p{$password} -h {$host} -P {$port} -D {$db}"
			. " < {$path_escaped}";
		exec($command);
	}

	/**
	 * 正しいファイル名を生成する
	 *
	 * @param  string $filename  指定がある場合はファイル名
	 * @return string            正規化されたフルパス
	 */
	public static function correctPath(string $filename = ''): string
	{
		// file name
		if ($filename !== '') {
			// check that the file name is correct
			if (! preg_match('/[A-Za-z0-9\-_]+?\.(sql|dump)/', $filename)) {
				throw new \Exception("Invalid backup file name '{$filename}'");
			}
		} else {
			$filename = date('YmdHis') . '_' . static::db() . '.sql';
		}
		static::initDir();
		return static::$dir . DIRECTORY_SEPARATOR . $filename;
	}
	// function correctPath()

	/**
	 * バックアップディレクトリ設定
	 */
	private static function initDir(): void
	{
		if (! static::$dir) {
			$config = \Config::load('common.php');
			$app_path = __DIR__ . '/../../../app';
			static::$dir = realpath($app_path . '/' . $config['backup_dir']);
		}
	}
	// function initDir()

	/**
	 * バックアップディレクトリから sql ファイルの一覧を取得
	 *
	 * @return array<string, \MyApp\Model\File>  ファイル名をキーとした連想配列
	 */
	private static function openDir(): array
	{
		static::initDir();

		$files = [];
		$dh = opendir(static::$dir);
		while (($name = readdir($dh)) !== false) {
			$file = new File(static::$dir . '/' . $name);
			if ($file->ext === static::$ext) {
				$files[$file->name] = $file;
			}
		}
		closedir($dh);
		return $files;
	}
	// function openDir()

	/**
	 * sql ファイルの一覧を取得
	 *
	 * @return array<int, \MyApp\Model\File>  ファイルの配列（作成日の降順）
	 */
	public static function files(): array
	{
		$files = static::openDir();

		usort($files, function ($a, $b) {
			return $a->created_at < $b->created_at;
		});

		return $files;
	}
	// function files()

	/**
	 * ファイル名からファイルオブジェクトを決定
	 *
	 * @param  string $filename  ファイル名
	 * @return \MyApp\Model\File  対象のファイルオブジェクト
	 */
	public static function find(string $filename): ?File
	{
		$files = static::openDir();
		return $files[$filename] ?? null;
	}
	// function find()
}
