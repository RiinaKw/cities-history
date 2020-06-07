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
	public static function backup($file = '')
	{
		$config = \Config::load(\Fuel::$env . '/db.php');
		$connection = $config['default']['connection'];

		preg_match('/^.+?:host=(?<host>.+?);dbname=(?<db>.+?)$/', $connection['dsn'], $matches);
		var_dump($matches);

		// db connection data
		$host = escapeshellcmd($matches['host']);
		$db = escapeshellcmd($matches['db']);
		$user = escapeshellcmd($connection['username']);
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

		exec($command);

		echo "Complete!\n";

	}

}
