<?php

/**
 * @package  App\Task
 */

namespace Fuel\Tasks;

use MyApp\Helper\CLI\Color;

class Db
{
	private const DELAY = 10000;
	private const RESTORE_TABLE = 'restore';

	/**
	 * 進捗が分かりやすいよう、実行を敢えて遅らせる
	 */
	private static function delay(): void
	{
		usleep(static::DELAY);
	}

	/**
	 * データベース接続情報を取得
	 * @return array  接続情報
	 */
	private static function connection(): array
	{
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

	/**
	 * データベースのバックアップを実行
	 * @param  string $file  出力するファイル名、指定がない場合はタイムスタンプを使用
	 * @return int           エラーがなければ 0
	 */
	public static function backup(string $file = ''): int
	{
		$connection = static::connection();
		$host = escapeshellcmd($connection['host']);
		$port = escapeshellcmd($connection['port']);
		$db = escapeshellcmd($connection['db']);
		$user = escapeshellcmd($connection['user']);
		$password = escapeshellcmd($connection['password']);

		// file name
		if ($file) {
			// check that the file name is correct
			if (! preg_match('/[A-Za-z0-9\-_]+?\.(sql|dump)/', $file)) {
				echo "Invalid file name\n";
				return 1;
			}
		} else {
			$file = date('YmdHis') . '_' . $db . '.sql';
		}

		// target path
		$config = \Config::load('common.php');
		$path = APPPATH . $config['backup_dir'] . '/' . $file;

		// ignore table
		$without = explode(',', \Cli::option('without'));
		$without[] = static::RESTORE_TABLE;
		$without[] = 'migration';
		if ($without) {
			$ignore_table = '';
			foreach ($without as $table) {
				if ($table) {
					$ignore_table .= "--ignore-table={$db}.{$table} ";
				}
			}
		}

		$db_str = Color::color($db, 'purple');
		$file_str = Color::color($file, 'light_green');
		echo "Backup database from {$db_str} to file {$file_str}...\n";

		if ($password) {
			$password = '-p' . $password;
		} else {
			$password = '';
		}

		$only_data = '-t';
		$complete_insert = '--complete-insert';

		// db connection data
		$connection = static::connection();
		$host = escapeshellcmd($connection['host']);
		$port = escapeshellcmd($connection['port']);
		$db = escapeshellcmd($connection['db']);
		$user = escapeshellcmd($connection['user']);
		$password = escapeshellcmd($connection['password']);

		$path_escaped = escapeshellcmd($path);
		$command = 'mysqldump'
			. " -u{$user} {$password} -h {$host} -P {$port} {$db}"
			. " {$ignore_table} {$only_data} {$complete_insert}"
			. " > {$path_escaped}";

		exec($command);

		if (! \File::exists($path)) {
			echo Color::failure('Error : cannot create dump file'), PHP_EOL;
			return 1;
		}
		echo Color::success('Complete!'), PHP_EOL;
		return 0;
	}

	/**
	 * 読み込むファイルを決定
	 * @param  string $file  ファイル名、空の場合は選択肢を提示
	 * @return string        ファイルパス
	 */
	private static function path(string $file): string
	{
		$config = \Config::load('common.php');
		$dir = APPPATH . $config['backup_dir'];

		if (! $file) {
			$files = [];
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				$ext = pathinfo($file)['extension'];
				if ($ext === 'sql') {
					$files[] = $file;
				}
			}
			closedir($dh);
			$files = array_reverse($files);
			foreach ($files as $key => $file) {
				++$key;
				echo Color::color("  [{$key}]", 'green'), ' : ', Color::color($file, 'cyan'), PHP_EOL;
			}

			$choice = 0;
			do {
				echo PHP_EOL, 'enter file number > ';

				$stdin = fopen('php://stdin', 'r');
				$choice = trim(fgets($stdin, 64));

				if (is_numeric($choice) && array_key_exists($choice - 1, $files)) {
					break;
				} else {
					echo Color::failure("invalid choise '{$choice}'"), PHP_EOL;
				}
			} while (1);

			$file = $files[$choice - 1];
		}

		$realpath = realpath($dir . '/' . $file);
		if (! $realpath) {
			echo Color::failure("Not found : '{$file}'");
			return 1;
		}
		return $realpath;
	}

	/**
	 * テーブル一覧を取得
	 * @param  string $db           データベース名
	 * @return array<string, bool>  テーブル名をキーとした配列
	 */
	private static function tables(string $db): array
	{
		$tables = [];
		$column = 'Tables_in_' . $db;
		foreach (\DB::query('SHOW TABLES')->execute() as $table) {
			$table_name = $table[$column];
			$tables[$table_name] = true;
		}
		return $tables;
	}

	/**
	 * リストア用のテーブルを初期化
	 */
	private static function truncateRestore(): void
	{
		\DBUtil::truncate_table(static::RESTORE_TABLE);
	}

	/**
	 * 配列で指定されたテーブルを空にする
	 * @param array<string, bool> $tables  テーブル一覧
	 */
	private static function truncate(array $tables): void
	{
		\DB::query('SET FOREIGN_KEY_CHECKS=0;')->execute();
		foreach (array_keys($tables) as $table) {
			static::delay();
			echo
				'truncate table ',
				Color::color("'{$table}'", 'light_yellow'),
				PHP_EOL;
			\DBUtil::truncate_table($table);
		}
		\DB::query('SET FOREIGN_KEY_CHECKS=1;')->execute();
	}

	/**
	 * ファイルから SQL を読み込む
	 * @param string $path           ファイル名
	 */
	private static function loadSQL(string $path): void
	{
		$fp = fopen($path, 'r');

		while ($sql = stream_get_line($fp, 16777216, ";")) {
			static::delay();
			$expected_last = ['/' => 0, ')' => 0, "\n" => 0];
			$expedted_last6 = ['TABLES' => 0, ' WRITE' => 0];
			while (
					! array_key_exists(substr($sql, -1), $expected_last)
					&& ! array_key_exists(substr($sql, -6), $expedted_last6)
			) {
				$sql .= stream_get_line($fp, 16777216, ";");
			}
			echo
				'loaded ',
				Color::color(strlen($sql) . ' bytes', 'green'),
				' of sql',
				PHP_EOL;
			$sql = trim($sql);

			\DB::insert(static::RESTORE_TABLE)
				->set(['sql' => $sql])
				->execute();
		}
		fclose($fp);
	}

	/**
	 * データベースのリストアを実行
	 * @param  string $file  ファイル名、空の場合は選択肢を提示
	 * @return int           エラーがなければ 0
	 */
	public static function restore(string $file = ''): int
	{
		// db connection data
		$db = static::connection()['db'];

		// target path
		$path = static::path($file);

		// do you really do?
		$file = basename($path);
		echo
			'restore from ',
			Color::color("'{$file}'", 'cyan'),
			', press ',
			Color::color("'y'", 'green'),
			' to continue > ';
		$stdin = fopen('php://stdin', 'r');
		$choice = trim(fgets($stdin, 64));
		if (strtolower($choice) !== 'y') {
			echo Color::color("aborted", 'red'), PHP_EOL;
			return 1;
		}

		echo
			'Restore database from ',
			Color::color("'{$file}'", 'cyan'),
			' into database ',
			Color::color("'{$db}'", 'purple'),
			'...',
			PHP_EOL;

		// find truncate tables
		$truncate_tables = static::tables($db);

		// without ignore tables
		$without = explode(',', \Cli::option('without'));
		$without[] = 'migration';
		foreach ($without as $table) {
			unset($truncate_tables[$table]);
		}

		// do truncate
		static::truncate($truncate_tables);

		echo PHP_EOL, 'prepare sql...', PHP_EOL;

		// truncate restore table
		static::truncateRestore();

		static::loadSQL($path);

		echo PHP_EOL, 'restore db...', PHP_EOL;

		$query = \DB::select()->from(static::RESTORE_TABLE)->execute();
		$rows = \DB::select([\DB::expr('COUNT(*)'), 'row_count'])
			->from(static::RESTORE_TABLE)
			->execute()->as_array();
		$count = (int)$rows[0]['row_count'];

		try {
			\DB::query("SET GLOBAL max_allowed_packet=16777216;")->execute();
			foreach ($query as $key => $row) {
				static::delay();
				echo sprintf('%d/%d ', $key + 1, $count);
				$sql = $row['sql'] . ';';

				$sql_first = str_replace("\n", ' ', substr($sql, 0, 100));
				echo Color::color($sql_first, 'cyan'), PHP_EOL;
				echo '  -> ';
				if (\DB::query($sql)->execute()) {
					echo Color::color("success", 'green'), PHP_EOL, PHP_EOL;
				} else {
					echo Color::color("failed", 'green'), PHP_EOL, PHP_EOL;
				}
			}
		} catch (\Exception $e) {
			$message = substr($e->getMessage(), 0, 300);
			echo Color::failure($message), PHP_EOL, PHP_EOL;
			echo Color::failure('Some failed'), PHP_EOL;
			return 1;
		}

		static::truncateRestore();
		echo Color::success('Complete!'), PHP_EOL;
		return 0;
	}
}
