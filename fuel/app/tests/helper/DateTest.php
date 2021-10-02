<?php

use PHPUnit\Framework\TestCase;
use MyApp\Helper\Date;

class Test_Helper_Date extends TestCase
{
	public function test_usual()
	{
		$timestamp = mktime(0, 0, 0, 10, 31, 2021);
		$formatted = Date::format('Y-m-d H:i:s', $timestamp);
		$this->assertSame('2021-10-31 00:00:00', $formatted);

		$timestamp = mktime(3, 4, 5, 2, 31, 2019);
		$formatted = Date::format('Y-m-d H:i:s', $timestamp);
		$this->assertSame('2019-03-03 03:04:05', $formatted);
	}

	public function test_ja()
	{
		$timestamp = mktime(0, 0, 0, 10, 2, 2021);
		$formatted = Date::format('JK年 x', $timestamp);
		$this->assertSame('令和3年 土', $formatted);

		$timestamp = mktime(13, 0, 0);
		$formatted = Date::format('Ep時', $timestamp);
		$this->assertSame('午後1時', $formatted);
	}
}
