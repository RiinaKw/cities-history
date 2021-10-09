<?php

/**
 * @package  App\Test
 */

namespace Test\MyApp\Helper;

use PHPUnit\Framework\TestCase;
use MyApp\Helper\CountHash;

class CountHashTest extends TestCase
{
	public function test_init()
	{
		$hash = new CountHash();
		$this->assertSame([], $hash->array());
	}

	public function test_set()
	{
		$hash = new CountHash();
		$hash->increment('foo');

		$this->assertSame(['foo' => 1], $hash->array());

		$hash->increment('foo');
		$hash->increment('bar');
		$this->assertSame(['foo' => 2, 'bar' => 1], $hash->array());
	}

	public function test_get()
	{
		$hash = new CountHash();
		$hash->increment('foo');
		$hash->increment('foo');
		$this->assertSame(2, $hash->get('foo'));

		$this->assertSame(0, $hash->get('bar'));
	}

	public function test_count()
	{
		$hash = new CountHash();
		$this->assertSame(0, count($hash));

		$hash->increment('foo');
		$hash->increment('foo');
		$hash->increment('bar');

		$this->assertSame(2, count($hash));
	}
}
