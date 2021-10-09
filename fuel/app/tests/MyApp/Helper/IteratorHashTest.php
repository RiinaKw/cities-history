<?php

namespace Test\MyApp\Helper;

use PHPUnit\Framework\TestCase;
use MyApp\Helper\IteratorHash;

class IteratorHashTest extends TestCase
{
	public function test_init()
	{
		$hash = new IteratorHash();
		$this->assertSame([], $hash->array());
	}

	public function test_set()
	{
		$hash = new IteratorHash();
		$hash->push('foo', 'bar');
		$hash->push('foo', 'buz');

		$iterator = $hash->get('foo');
		$this->assertSame(['bar', 'buz'], $iterator->array());
	}

	public function test_count()
	{
		$hash = new IteratorHash();
		$this->assertSame(0, count($hash));

		$hash->push('foo', 'bar');
		$hash->push('boo', 'buz');

		$this->assertSame(2, count($hash));
	}
}
