<?php

use PHPUnit\Framework\TestCase;
use MyApp\MyFuel;
use MyApp\Model\Division\Tree;
use MyApp\Table\Division as DivisionTable;

class TreeTest extends TestCase
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
		$gunma = DivisionTable::create(
			[
				'fullname' => '群馬県',
			],
			null
		);
		$isesaki = DivisionTable::create(
			[
				'fullname' => '伊勢崎市',
			],
			$gunma
		);
		$sawa = DivisionTable::create(
			[
				'fullname' => '佐波郡',
			],
			$gunma
		);
		DivisionTable::create(
			[
				'fullname' => '赤堀町',
			],
			$sawa
		);
		DivisionTable::create(
			[
				'fullname' => '境町',
			],
			$sawa
		);
		DivisionTable::create(
			[
				'fullname' => '東村',
			],
			$sawa
		);
		DivisionTable::create(
			[
				'fullname' => '玉村町',
			],
			$sawa
		);

		$tree = Tree::create($gunma);
		$this->assertSame($gunma, $tree->self());

		$this->assertSame(['市' => 1, '郡' => 1, '町' => 3, '村' => 1], $tree->suffixes());

		$iterator = $tree->get_by_suffix('市');
		$subtree = $iterator->get(0);
		$this->assertSame($isesaki->id_path, $subtree->self()->id_path);

		$iterator = $tree->get_by_suffix('郡');
		$subtree = $iterator->get(0);
		$this->assertSame($sawa->id_path, $subtree->self()->id_path);
		$this->assertSame(['町' => 3, '村' => 1], $subtree->suffixes());

		$country_iterator = $subtree->get_by_suffix('町村');
		$this->assertSame(4, count($country_iterator));
	}
}
