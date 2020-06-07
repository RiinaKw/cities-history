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
		$ignore_table = '';
		foreach ($without as $table) {
			$ignore_table .= "--ignore-table={$db}.{$table} ";
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
		if ( \Fuel::$env == 'staging' ) {
			$command = 'FUEL_ENV=staging ' . $command;
		}

		exec($command);

		echo "Complete!\n";
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
			echo "Not found : {$file}\n";
			return;
		}
		$path = escapeshellcmd($path);

		// truncate tables
		$truncate_tables = [];
		$tables = \DB::query('SHOW TABLES')->execute()->as_array();
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

		echo "Restore database from {$file} into {$db}...\n";

		// do truncate
		\DB::query('SET FOREIGN_KEY_CHECKS=0;')->execute();
		foreach ($truncate_tables as $table => $dummy) {
			echo "truncate table {$table}...\n";
			\DBUtil::truncate_table($table);
		}
		\DB::query('SET FOREIGN_KEY_CHECKS=1;')->execute();

		echo "restore db...\n";

		$command = "mysql"
			. " -u{$user} {$password} -h {$host} -P {$port} {$db}"
			. " < {$path}";
		if ( \Fuel::$env == 'staging' ) {
			$command = 'FUEL_ENV=staging ' . $command;
		}

		exec($command);

        echo "\nComplete!\n";
	}

}
