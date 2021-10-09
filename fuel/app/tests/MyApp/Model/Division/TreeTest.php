<?php

/**
 * @package  App\Test
 */

namespace Test\MyApp\Model;

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

		\DBUtil::truncate_table('divisions');
	}

	public function test_create()
	{
		$gunma = DivisionTable::makeFromPath('群馬県');
		$isesaki = DivisionTable::makeFromPath('群馬県/伊勢崎市');
		$sawa = DivisionTable::makeFromPath('群馬県/佐波郡');
		DivisionTable::makeFromPath('群馬県/佐波郡/赤堀町');
		DivisionTable::makeFromPath('群馬県/佐波郡/境町');
		DivisionTable::makeFromPath('群馬県/佐波郡/東村');
		DivisionTable::makeFromPath('群馬県/佐波郡/玉村町');

		$tree = Tree::create($gunma);
		$this->assertSame($gunma, $tree->self());

		$this->assertSame(['市' => 1, '郡' => 1, '町' => 3, '村' => 1], $tree->suffixes());

		$subtree = $tree->get_by_suffix('市')->get(0);
		$this->assertSame($isesaki->id_path, $subtree->self()->id_path);

		$subtree = $tree->get_by_suffix('郡')->get(0);
		$this->assertSame($sawa->id_path, $subtree->self()->id_path);
		$this->assertSame(['町' => 3, '村' => 1], $subtree->suffixes());

		$country_iterator = $subtree->get_by_suffix('町村');
		$this->assertSame(4, count($country_iterator));
	}
}
