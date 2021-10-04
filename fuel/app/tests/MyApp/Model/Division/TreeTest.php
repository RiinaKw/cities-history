<?php

use PHPUnit\Framework\TestCase;
use MyApp\Model\Division\Tree;
use Model_Division;

class TreeTest extends TestCase
{

	protected function setUp(): void
	{
		// load the fuel core
		define('DOCROOT',  __DIR__);
		define('FUELPATH', __DIR__  . '/../../../../..');
		define('APPPATH',  FUELPATH . '/app/');
		define('PKGPATH',  FUELPATH . '/packages/');
		define('COREPATH', FUELPATH . '/core/');
		require COREPATH . 'classes/autoloader.php';

		class_alias('Fuel\\Core\\Autoloader', 'Autoloader');
		require APPPATH . '/bootstrap.php';

		restore_error_handler();
	}
	public function test_usual()
	{
		$division = Model_Division::find(1);
		$this->assertNotNull($division);
	}
}
