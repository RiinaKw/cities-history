<?php

/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Tasks;

class Db
{
	protected static function connection()
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

	public static function backup($file = '')
	{
		$connection = static::connection();

		// db connection data
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
				exit;
			}
		} else {
			$file = date('YmdHis') . '_' . $db . '.sql';
		}

		// target path
		$config = \Config::load('common.php');
		$path = APPPATH . $config['backup_dir'] . '/' . $file;
		$path = escapeshellcmd($path);

		// ignore table
		$without = explode(',', \Cli::option('without'));
		if ($without) {
			$ignore_table = '';
			foreach ($without as $table) {
				if ($table) {
					$ignore_table .= "--ignore-table={$db}.{$table} ";
				}
			}
		}

		echo "Backup database from {$db} to {$file}...\n";

		if ($password) {
			$password = '-p' . $password;
		} else {
			$password = '';
		}

		$only_data = '-t';
		$complete_insert = '--complete-insert';

		$command = 'mysqldump'
			. " -u{$user} {$password} -h {$host} {$db}"
			. " {$ignore_table} {$only_data} {$complete_insert}"
			. " > {$path}";

		exec($command);

		if (! \File::exists($path)) {
			echo "Error : cannot create dump file.\n";
			exit(1);
		}
		echo "Complete!\n";
		exit(0);
	}

	public static function restore($file)
	{
		$connection = static::connection();

		// db connection data
		$host = escapeshellcmd($connection['host']);
		$port = escapeshellcmd($connection['port']);
		$db = escapeshellcmd($connection['db']);
		$user = escapeshellcmd($connection['user']);
		$password = escapeshellcmd($connection['password']);

		// target path
		$config = \Config::load('common.php');
		$path = APPPATH . $config['backup_dir'] . '/' . $file;
		$realpath = realpath($path);
		if (! $realpath) {
			echo "Not found : {$file}", PHP_EOL;
			return;
		}
		//$path = escapeshellcmd($path);

		// truncate tables
		$truncate_tables = [];
		$tables = \DB::query('SHOW TABLES')->execute();
		$column = 'Tables_in_' . $db;
		foreach ($tables as $table) {
			$table_name = $table[$column];
			$truncate_tables[$table_name] = true;
		}

		// ignore tables
		$without = explode(',', \Cli::option('without'));
		$ignore_table = [];
		foreach ($without as $table) {
			unset($truncate_tables[$table]);
		}

		echo "Restore database from {$file} into {$db}...", PHP_EOL;

		\DB::query("SET GLOBAL max_allowed_packet=16777216;")->execute();

		// do truncate
		\DB::query('SET FOREIGN_KEY_CHECKS=0;')->execute();
		foreach ($truncate_tables as $table => $dummy) {
			echo "truncate table {$table}...\n";
			\DBUtil::truncate_table($table);
		}
		\DB::query('SET FOREIGN_KEY_CHECKS=1;')->execute();

		echo PHP_EOL, 'prepare sql...', PHP_EOL;

		$filesize = filesize($path);

		$restore_table = 'restore';

		\DBUtil::truncate_table($restore_table);
		$fp = fopen($path, 'r');

		while ($sql = stream_get_line($fp, 500000, ";")) {
			$expected_last = ['/' => 0, ')' => 0, "\n" => 0];
			$expedted_last6 = ['TABLES' => 0, ' WRITE' => 0];
			while (
					! array_key_exists(substr($sql, -1), $expected_last)
					&& ! array_key_exists(substr($sql, -6), $expedted_last6)
			) {
				$sql .= stream_get_line($fp, 500000, ";");
				$last = substr($sql, -1);
			}
			echo sprintf('sql of %d bytes loaded', strlen($sql)), PHP_EOL;
			$sql = trim($sql);

			\DB::insert($restore_table)
				->set(['sql' => $sql])
				->execute();
		}
		fclose($fp);

		echo PHP_EOL, 'restore db...', PHP_EOL;

		$query = \DB::select()->from($restore_table)->execute();
		$row = \DB::select([\DB::expr('COUNT(*)'), 'row_count'])->from($restore_table)->execute()->as_array();
		$count = (int)$row[0]['row_count'];

		$fail = 0;
		$i = 1;
		foreach ($query as $row) {
			$sql = $row['sql'] . ';';

			//echo sprintf('[%d] ', $row['id']);
			echo sprintf('%d / %d', $i, $count), PHP_EOL;
			$sql_first = substr($sql, 0, 100);
			if (! \DB::query($sql)->execute()) {
				echo 'sql : ', $sql_first, PHP_EOL, 'failed', PHP_EOL, PHP_EOL;
				++$fail;
			} else {
				//echo 'sql : ', $sql_first, PHP_EOL, 'success', PHP_EOL, PHP_EOL;
			}
			++$i;
		}

		if ($fail === 0) {
			echo PHP_EOL, 'Complete!', PHP_EOL;
			exit(0);
		} else {
			echo PHP_EOL, 'Some failed', PHP_EOL;
			exit(1);
		}
	}
}
