<?php

use PHPUnit\Framework\TestCase;
use MyApp\Helper\Number;

class NumberTest extends TestCase
{
	public function test_no_unit()
	{
		$units = ['B', 'KiB', 'MiB', 'GiB'];

		$result = Number::format(1, 1024, $units);
		$this->assertSame('1 B', $result);

		$result = Number::format(1000, 1024, $units);
		$this->assertSame('1000 B', $result);
	}

	public function test_any_unit()
	{
		$units = ['B', 'KiB', 'MiB', 'GiB'];

		$result = Number::format(1024, 1024, $units);
		$this->assertSame('1 KiB', $result);

		$result = Number::format(2000, 1024, $units);
		$this->assertSame('1.95 KiB', $result);

		$result = Number::format(2000, 1024, $units, 5);
		$this->assertSame('1.95313 KiB', $result);

		$result = Number::format(1048576, 1024, $units);
		$this->assertSame('1 MiB', $result);
	}
}
