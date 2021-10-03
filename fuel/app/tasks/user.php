<?php

/**
 * @package  App\Task
 */

namespace Fuel\Tasks;

class User
{
	public static function create($name, $password)
	{
		try {
			\Model_User::create($name, $password);
			echo "User created\n";
		} catch (\Exception $e) {
			echo $e->getMessage();
			return;
		}
	}
	// function create()
}
/* End of file tasks/robots.php */
