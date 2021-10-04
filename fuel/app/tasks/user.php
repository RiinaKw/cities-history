<?php

/**
 * @package  App\Task
 */

namespace Fuel\Tasks;

use MyApp\Helper\CLI as MyCLI;
use MyApp\Helper\CLI\Color;
use Model_User;

class User
{
	public static function create(/*$name, $password*/)
	{
		echo Color::color('==== create a new user ====', 'purple'), PHP_EOL, PHP_EOL;

		do {
			echo 'enter user name > ';
			$name = MyCLI::prompt();

			if ($name === '') {
				echo Color::failure('user name must not be empty, try again'), PHP_EOL;
			} else {
				break;
			}
		} while (1);

		echo 'enter password > ';
		$password = MyCLI::promptHidden();

		echo 'enter password again > ';
		$password_confirm = MyCLI::promptHidden();

		if ($password !== $password_confirm) {
			echo Color::failure('password missmatch'), PHP_EOL;
			return 1;
		}

		$name_str = Color::color($name, 'light_green');
		$y = Color::color("'y'", 'green');
		do {
			echo "user name is {$name_str}. Are you OK? press {$y} to continue > ";
			$choice = strtolower(MyCLI::prompt());

			if ($choice !== '' && $choice !== 'y') {
				echo Color::color("canceled to create", 'red'), PHP_EOL;
				return 1;
			}
		} while ($choice !== 'y');

		try {
			Model_User::createUser($name, $password);
			echo Color::success("user created"), PHP_EOL;
		} catch (\Exception $e) {
			echo Color::failure($e->getMessage()), PHP_EOL;
			return;
		}
	}
	// function create()
}
/* End of file tasks/robots.php */
