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

/**
 * @package  App\Task
 */
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
