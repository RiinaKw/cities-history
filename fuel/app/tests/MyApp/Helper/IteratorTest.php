<?php

use PHPUnit\Framework\TestCase;
use MyApp\Helper\Iterator;

class IteratorTest extends TestCase
{
	public function test_init()
	{
		$iterator = new Iterator();
		$this->assertSame([], $iterator->array());
	}

	public function test_push()
	{
		$iterator = new Iterator();
		$iterator->push(1);
		$this->assertSame([1], $iterator->array());

		$iterator->push(2, 'foo');
		$this->assertSame([1, 'foo' => 2], $iterator->array());
	}

	public function test_get()
	{
		$iterator = new Iterator();
		$iterator->push(1);
		$iterator->push(2, 'foo');

		$this->assertSame(1, $iterator->get(0));
		$this->assertSame(2, $iterator->get('foo'));
	}

	public function test_count()
	{
		$iterator = new Iterator();
		$this->assertSame(0, count($iterator));

		$iterator->push(1);
		$iterator->push(2, 'foo');

		$this->assertSame(2, count($iterator));
	}
}
