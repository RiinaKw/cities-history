<?php

/**
 * @package  App\Task
 */

namespace Fuel\Tasks;

use MyApp\Model\Backup;
use MyApp\Model\File;
use MyApp\Helper\CLI as MyCLI;
use MyApp\Helper\CLI\Color;

class Db
{
	/**
	 * データベースのバックアップを実行
	 *
	 * @param  string $file  出力するファイル名、指定がない場合はタイムスタンプを使用
	 * @return int           エラーがなければ 0
	 */
	public static function backup(string $filename = ''): int
	{
		try {
			$path = Backup::correctPath($filename);
			$db = Backup::db();

			$db_str = Color::color($db, 'purple');
			$file_str = Color::color(basename($path), 'light_green');
			echo "Backup database from {$db_str} to file {$file_str}...\n";

			do {
				echo
					'press ',
					Color::color("'y'", 'green'),
					' to continue, press any other key to exit > ';
				$choice = strtolower(MyCLI::prompt());

				if ($choice === '') {
					continue;
				} elseif ($choice !== 'y') {
					echo Color::color("aborted", 'red'), PHP_EOL;
					return 1;
				}
			} while ($choice !== 'y');

			// ignore table
			$without = explode(',', \Cli::option('without'));

			if (! Backup::export($path, $without)) {
				echo Color::failure('Error : cannot create dump file'), PHP_EOL;
				return 1;
			}
			echo Color::success('Complete!'), PHP_EOL;
			return 0;
		} catch (\Exception $e) {
			echo Color::failure('ERROR!'), PHP_EOL;
			echo Color::color($e->getMessage(), 'white', 'red'), PHP_EOL, PHP_EOL;
			echo Color::failure('abort'), PHP_EOL;
			return 1;
		}
	}
	// function backup()

	/**
	 * ファイル一覧を整形して表示
	 *
	 * @return array<int, \MyApp\Model\File>  ファイルの配列（作成日の降順）
	 */
	private static function showFiles(): array
	{
		$files = Backup::files();
		foreach ($files as $key => $file) {
			++$key;
			$date = date('Y-m-d H:i:s', $file->modified_at);
			echo
				'  ',
				Color::color("[{$key}]", 'green'),
				' : ',
				Color::color($file->name, 'cyan'),
				', modified on ',
				Color::color($date, 'yellow'),
				PHP_EOL;
		}
		return $files;
	}
	// function showFiles()

	/**
	 * 読み込むファイルを決定
	 *
	 * @param  string            $file  ファイル名、空の場合は選択肢を提示
	 * @return \MyApp\Model\File         ファイルオブジェクト
	 */
	private static function useFile(string $filename): File
	{
		$file = null;
		if ($filename) {
			$file = Backup::find($filename);
		} else {
			$files = static::showFiles();

			$choice = 0;
			echo PHP_EOL;
			do {
				echo 'enter file number > ';
				$choice = MyCLI::prompt();

				if ($choice === '') {
					continue;
				} elseif (is_numeric($choice) && array_key_exists($choice - 1, $files)) {
					break;
				} else {
					echo Color::failure("invalid choise '{$choice}'"), PHP_EOL;
				}
			} while (1);

			$file = $files[$choice - 1];
		}

		return $file;
	}
	// function useFile()

	/**
	 * データベースのリストアを実行
	 *
	 * @param  string $file  ファイル名、空の場合は選択肢を提示
	 * @return int           エラーがなければ 0
	 */
	public static function restore(string $file = ''): int
	{
		// target path
		$file = static::useFile($file);

		// do you really do?
		echo
			'restore from ',
			Color::color("'{$file->name}'", 'cyan'),
			PHP_EOL;

		do {
			echo
				'press ',
				Color::color("'y'", 'green'),
				' to continue, press any other key to exit > ';
			$choice = strtolower(MyCLI::prompt());

			if ($choice === '') {
				continue;
			} elseif ($choice !== 'y') {
				echo Color::color("aborted", 'red'), PHP_EOL;
				return 1;
			}
		} while ($choice !== 'y');

		try {
			// db connection data
			$db = Backup::db();

			echo
				PHP_EOL,
				'Restore database from ',
				Color::color("'{$file->name}'", 'cyan'),
				' into database ',
				Color::color("'{$db}'", 'purple'),
				'...',
				PHP_EOL;

			$without = explode(',', \Cli::option('without'));
			Backup::truncate($without);

			echo PHP_EOL, 'restore db...', PHP_EOL;
			Backup::import($file->path);

			echo Color::success('Complete!'), PHP_EOL;
			return 0;
		} catch (\Exception $e) {
			echo Color::failure('ERROR!'), PHP_EOL;
			echo Color::color($e->getMessage(), 'white', 'red'), PHP_EOL, PHP_EOL;
			echo Color::failure('Some failed'), PHP_EOL;
			return 1;
		}
	}
	// function restore()
}
