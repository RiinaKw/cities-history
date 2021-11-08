<?php
/**
 * Controller Top class tests
 *
 * @group Model
 * @group Member
 */

use MyApp\PHPUnit\Fuel\RequestWrapper as TestCase;
use MyApp\MyFuel;

class TopTest extends TestCase
{
	protected function setUp(): void
	{
		MyFuel::env('test');
		MyFuel::createServerHost();
	}

	public function test_top()
	{
		$this->request('/');
		$this->assertStatus(200);
		$this->assertContainRe('/Cities History Project/');
	}

	public function test_404()
	{
		$this->request('/noexists');
		$this->assertStatus(404);
		$this->assertContainRe('/Cities History Project/');
		$this->assertContainRe('/ページが見つかりません。/');
		$this->assertContainRe('/path : noexists/');
	}
}
