<?php

/**
 * @package  App\Test
 */

namespace Test\MyApp\PresentationModel;

use PHPUnit\Framework\TestCase;
use MyApp\MyFuel;
use MyApp\Table\Division as DivisionTable;

class DivisionTest extends TestCase
{
	protected function setUp(): void
	{
		// テスト環境でマイグレーションを実行
		MyFuel::env('test');
		MyFuel::oil('migrate');

		\DB::query('SET FOREIGN_KEY_CHECKS = 0;')->execute();
		\DBUtil::truncate_table('divisions');
		\DB::query('SET FOREIGN_KEY_CHECKS = 1;')->execute();

		\Config::set('uri.division.detail', '/:path');

		DivisionTable::makeFromPath('群馬県/鯖郡');
	}

	public function test_anchor()
	{
		$gunma = DivisionTable::findByPath('群馬県')->pmodel();
		$this->assertMatchesRegularExpression('/>群馬県</', $gunma->htmlAnchor());
		$this->assertMatchesRegularExpression('/\/public\/群馬県"/', $gunma->htmlAnchor());

		$saba = DivisionTable::findByPath('群馬県/鯖郡')->pmodel();
		$this->assertMatchesRegularExpression('/>鯖郡</', $saba->htmlAnchor());
		$this->assertMatchesRegularExpression('/\/public\/群馬県\/鯖郡"/', $saba->htmlAnchor());
	}

	public function test_anchorPath()
	{
		$saba = DivisionTable::findByPath('群馬県/鯖郡')->pmodel();
		$this->assertMatchesRegularExpression('/>群馬県\/鯖郡</', $saba->htmlAnchorPath());
	}
}
