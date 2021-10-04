<?php

use PHPUnit\Framework\TestCase;
use MyApp\Model\Division\Tree;

class TreeTest extends TestCase
{
	protected function setUp(): void
	{
		// Fuel のコアを読み込む
		if (! defined('DOCROOT')) {
			define('DOCROOT',  __DIR__);
			define('FUELPATH', __DIR__  . '/../../../../..');
			define('APPPATH',  FUELPATH . '/app/');
			define('PKGPATH',  FUELPATH . '/packages/');
			define('COREPATH', FUELPATH . '/core/');
			require COREPATH . 'classes/autoloader.php';

			class_alias('Fuel\\Core\\Autoloader', 'Autoloader');
			require APPPATH . '/bootstrap.php';

			restore_error_handler();
		}

		// 環境をテストに切り替え
		Fuel::$env = Fuel::TEST;

		// テスト環境でマイグレーションを実行
		Package::load('oil');
		Oil\Refine::run('migrate');
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

		$division = Model_Division::forge($input);
		$division->path = $division->fullname;
		$division->save();

		$division->id_path = ($parent ? $parent->id_path : '') . $division->id . '/';
		$division->path = (($parent ? $parent->path . '/' : '') . $division->fullname);
		$division->save();

		return $division;
	}

	public function test_create()
	{
		$gunma = $this->insert(
			[
				'fullname' => '群馬県',
				'name' => '群馬',
				'suffix' => '県',
			],
			null
		);
		$isesaki = $this->insert(
			[
				'fullname' => '伊勢崎市',
				'name' => '伊勢崎',
				'suffix' => '市',
			],
			$gunma
		);
		$sawa = $this->insert(
			[
				'fullname' => '佐波郡',
				'name' => '佐波',
				'suffix' => '郡',
			],
			$gunma
		);
		$akabori = $this->insert(
			[
				'fullname' => '赤堀町',
				'name' => '赤堀',
				'suffix' => '町',
			],
			$sawa
		);
		$sakai = $this->insert(
			[
				'fullname' => '境町',
				'name' => '境',
				'suffix' => '町',
			],
			$sawa
		);
		$azuma = $this->insert(
			[
				'fullname' => '東村',
				'name' => '東',
				'suffix' => '村',
			],
			$sawa
		);
		$tamamura = $this->insert(
			[
				'fullname' => '玉村町',
				'name' => '玉村',
				'suffix' => '町',
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
