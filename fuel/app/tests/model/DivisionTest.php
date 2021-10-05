<?php

use PHPUnit\Framework\TestCase;
use MyApp\MyFuel;

class DivisionTest extends TestCase
{
	protected function setUp(): void
	{
		// テスト環境でマイグレーションを実行
		MyFuel::env('test');
		MyFuel::oil('migrate');

		DBUtil::truncate_table('divisions');
	}

	public function test_create()
	{
		$gunma = Model_Division::create2(
			[
				'fullname' => '群馬県',
			],
			null
		);
		$isesaki = Model_Division::create2(
			[
				'fullname' => '伊勢崎市',
			],
			$gunma
		);
		$sawa = Model_Division::create2(
			[
				'fullname' => '佐波郡',
				'name' => '佐波',
				'suffix' => '郡',
			],
			$gunma
		);
		$akabori = Model_Division::create2(
			[
				'fullname' => '赤堀町',
				'name' => '赤堀',
				'suffix' => '町',
			],
			$sawa
		);
		$sakai = Model_Division::create2(
			[
				'fullname' => '境町',
				'name' => '境',
				'suffix' => '町',
			],
			$sawa
		);
		$azuma = Model_Division::create2(
			[
				'fullname' => '東村',
				'name' => '東',
				'suffix' => '村',
			],
			$sawa
		);
		$tamamura = Model_Division::create2(
			[
				'fullname' => '玉村町',
				'name' => '玉村',
				'suffix' => '町',
			],
			$sawa
		);

		$this->assertSame('群馬県', $gunma->fullname);
		$this->assertSame('群馬', $gunma->name);
		$this->assertSame('県', $gunma->suffix);
		$this->assertSame('群馬県', $gunma->path);
		$this->assertNull($gunma->parent());

		$this->assertSame('伊勢崎市', $isesaki->fullname);
		$this->assertSame('伊勢崎', $isesaki->name);
		$this->assertSame('市', $isesaki->suffix);
		$this->assertSame('群馬県/伊勢崎市', $isesaki->path);
		$this->assertSame($gunma, $isesaki->parent());

		$this->assertSame('佐波郡', $sawa->fullname);
		$this->assertSame('佐波', $sawa->name);
		$this->assertSame('郡', $sawa->suffix);
		$this->assertSame('群馬県/佐波郡', $sawa->path);
		$this->assertSame($gunma, $isesaki->parent());

		$this->assertSame('赤堀町', $akabori->fullname);
		$this->assertSame('赤堀', $akabori->name);
		$this->assertSame('町', $akabori->suffix);
		$this->assertSame('群馬県/佐波郡/赤堀町', $akabori->path);
		$this->assertSame($sawa, $akabori->parent());

		$this->assertSame('境町', $sakai->fullname);
		$this->assertSame('境', $sakai->name);
		$this->assertSame('町', $sakai->suffix);
		$this->assertSame('群馬県/佐波郡/境町', $sakai->path);
		$this->assertSame($sawa, $sakai->parent());

		$this->assertSame('東村', $azuma->fullname);
		$this->assertSame('東', $azuma->name);
		$this->assertSame('村', $azuma->suffix);
		$this->assertSame('群馬県/佐波郡/東村', $azuma->path);
		$this->assertSame($sawa, $azuma->parent());

		$this->assertSame('玉村町', $tamamura->fullname);
		$this->assertSame('玉村', $tamamura->name);
		$this->assertSame('町', $tamamura->suffix);
		$this->assertSame('群馬県/佐波郡/玉村町', $tamamura->path);
		$this->assertSame($sawa, $tamamura->parent());
	}
}
