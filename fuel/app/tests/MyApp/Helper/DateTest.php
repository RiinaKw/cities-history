<?php

/**
 * @package  App\Test
 */

namespace Test\MyApp\Helper;

use PHPUnit\Framework\TestCase;
use MyApp\Helper\Date;

class DateTest extends TestCase
{
	public function test_usual()
	{
		$formatted = Date::format('Y-m-d H:i:s', '2021-10-31');
		$this->assertSame('2021-10-31 00:00:00', $formatted);

		$formatted = Date::format('Y-m-d H:i:s', '2019-02-31 3:4:5');
		$this->assertSame('2019-03-03 03:04:05', $formatted);
	}

	public function test_ja()
	{
		$formatted = Date::format('JK年 x', '2021-10-02');
		$this->assertSame('令和3年 土', $formatted);

		$formatted = Date::format('Ep時', mktime(13, 0, 0));
		$this->assertSame('午後1時', $formatted);
	}
}
