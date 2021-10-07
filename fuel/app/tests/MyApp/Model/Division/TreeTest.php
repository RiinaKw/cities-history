<?php

use PHPUnit\Framework\TestCase;
use MyApp\Model\Division\Tree;
use MyApp\MyFuel;

class TreeTest extends TestCase
{
	protected function setUp(): void
	{
		// テスト環境でマイグレーションを実行
		MyFuel::env('test');
		MyFuel::oil('migrate');

		DBUtil::truncate_table('divisions');
	}

	protected function insert(array $input, Model_Division $parent = null): Model_Division
	{
		$input = array_merge(
			[
				'id_path' => '',
				'name' => '',
				'name_kana' => '',
				'suffix' => '',
				'suffix_kana' => '',
				'search_path' => '',
				'search_path_kana' => '',
				'fullname' => '',
				'path' => '',
			],
			$input
		);

		$division = Model_Division::create2($input, $parent);
		$division->save();

		return $division;
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
			],
			$gunma
		);
		Model_Division::create2(
			[
				'fullname' => '赤堀町',
			],
			$sawa
		);
		Model_Division::create2(
			[
				'fullname' => '境町',
			],
			$sawa
		);
		Model_Division::create2(
			[
				'fullname' => '東村',
			],
			$sawa
		);
		Model_Division::create2(
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
