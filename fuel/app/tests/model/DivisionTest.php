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
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			]
		);

		$this->assertSame(1, (int)$gunma->id);
		$this->assertSame('1/', $gunma->id_path);
		$this->assertSame('群馬県', $gunma->fullname);
		$this->assertSame('群馬', $gunma->name);
		$this->assertSame('県', $gunma->suffix);
		$this->assertSame('ぐんま', $gunma->name_kana);
		$this->assertSame('けん', $gunma->suffix_kana);
		$this->assertSame('群馬県', $gunma->path);
		$this->assertSame('群馬県', $gunma->search_path);
		$this->assertSame('ぐんまけん', $gunma->search_path_kana);
		$this->assertNull($gunma->parent());
	}

	public function test_create_child()
	{
		$gunma = Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			],
			null
		);
		$isesaki = Model_Division::create2(
			[
				'fullname' => '伊勢崎市',
				'name_kana' => 'いせさき',
				'suffix_kana' => 'し',
			],
			$gunma
		);

		$this->assertSame(2, (int)$isesaki->id);
		$this->assertSame('1/2/', $isesaki->id_path);
		$this->assertSame('伊勢崎市', $isesaki->fullname);
		$this->assertSame('伊勢崎', $isesaki->name);
		$this->assertSame('市', $isesaki->suffix);
		$this->assertSame('いせさき', $isesaki->name_kana);
		$this->assertSame('し', $isesaki->suffix_kana);
		$this->assertSame('群馬県/伊勢崎市', $isesaki->path);
		$this->assertSame('群馬県伊勢崎市', $isesaki->search_path);
		$this->assertSame('ぐんまけんいせさきし', $isesaki->search_path_kana);
		$this->assertSame($gunma, $isesaki->parent());
	}

	public function test_create_grandchild()
	{
		$gunma = Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			],
			null
		);
		$sawa = Model_Division::create2(
			[
				'fullname' => '佐波郡',
				'name_kana' => 'さわ',
				'suffix_kana' => 'ぐん',
			],
			$gunma
		);
		$akabori = Model_Division::create2(
			[
				'fullname' => '赤堀町',
				'name_kana' => 'あかぼり',
				'suffix_kana' => 'まち',
			],
			$sawa
		);
		$sakai = Model_Division::create2(
			[
				'fullname' => '境町',
				'name_kana' => 'さかい',
				'suffix_kana' => 'まち',
			],
			$sawa
		);
		$azuma = Model_Division::create2(
			[
				'fullname' => '東村',
				'name_kana' => 'あずま',
				'suffix_kana' => 'むら',
			],
			$sawa
		);
		$tamamura = Model_Division::create2(
			[
				'fullname' => '玉村町',
				'name_kana' => 'たまむら',
				'suffix_kana' => 'まち',
			],
			$sawa
		);

		$this->assertSame(3, (int)$akabori->id);
		$this->assertSame('1/2/3/', $akabori->id_path);
		$this->assertSame('赤堀町', $akabori->fullname);
		$this->assertSame('赤堀', $akabori->name);
		$this->assertSame('町', $akabori->suffix);
		$this->assertSame('あかぼり', $akabori->name_kana);
		$this->assertSame('まち', $akabori->suffix_kana);
		$this->assertSame('群馬県/佐波郡/赤堀町', $akabori->path);
		$this->assertSame('群馬県佐波郡赤堀町', $akabori->search_path);
		$this->assertSame('ぐんまけんさわぐんあかぼりまち', $akabori->search_path_kana);
		$this->assertSame($sawa, $akabori->parent());

		$this->assertSame(4, (int)$sakai->id);
		$this->assertSame('1/2/4/', $sakai->id_path);
		$this->assertSame('境町', $sakai->fullname);
		$this->assertSame('境', $sakai->name);
		$this->assertSame('町', $sakai->suffix);
		$this->assertSame('さかい', $sakai->name_kana);
		$this->assertSame('まち', $sakai->suffix_kana);
		$this->assertSame('群馬県/佐波郡/境町', $sakai->path);
		$this->assertSame('群馬県佐波郡境町', $sakai->search_path);
		$this->assertSame('ぐんまけんさわぐんさかいまち', $sakai->search_path_kana);
		$this->assertSame($sawa, $sakai->parent());

		$this->assertSame(5, (int)$azuma->id);
		$this->assertSame('1/2/5/', $azuma->id_path);
		$this->assertSame('東村', $azuma->fullname);
		$this->assertSame('東', $azuma->name);
		$this->assertSame('村', $azuma->suffix);
		$this->assertSame('あずま', $azuma->name_kana);
		$this->assertSame('むら', $azuma->suffix_kana);
		$this->assertSame('群馬県/佐波郡/東村', $azuma->path);
		$this->assertSame('群馬県佐波郡東村', $azuma->search_path);
		$this->assertSame('ぐんまけんさわぐんあずまむら', $azuma->search_path_kana);
		$this->assertSame($sawa, $azuma->parent());

		$this->assertSame(6, (int)$tamamura->id);
		$this->assertSame('1/2/6/', $tamamura->id_path);
		$this->assertSame('玉村町', $tamamura->fullname);
		$this->assertSame('玉村', $tamamura->name);
		$this->assertSame('町', $tamamura->suffix);
		$this->assertSame('たまむら', $tamamura->name_kana);
		$this->assertSame('まち', $tamamura->suffix_kana);
		$this->assertSame('群馬県/佐波郡/玉村町', $tamamura->path);
		$this->assertSame('群馬県佐波郡玉村町', $tamamura->search_path);
		$this->assertSame('ぐんまけんさわぐんたまむらまち', $tamamura->search_path_kana);
		$this->assertSame($sawa, $tamamura->parent());
	}

	public function test_edit()
	{
		$gunma = Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			],
			null
		);
		Model_Division::create2(
			[
				'fullname' => '伊勢崎市',
				'name_kana' => 'いせさき',
				'suffix_kana' => 'し',
			],
			$gunma
		);
		$sawa = Model_Division::create2(
			[
				'fullname' => '佐波郡',
				'name_kana' => 'さわ',
				'suffix_kana' => 'ぐん',
			],
			$gunma
		);
		Model_Division::create2(
			[
				'fullname' => '赤堀町',
				'name_kana' => 'あかぼり',
				'suffix_kana' => 'まち',
			],
			$sawa
		);

		// 佐波郡を前橋市(2021)にしてみる
		$new = Model_Division::make([
			'fullname' => '前橋市',
			'identifier' => '(2021)',
			'name_kana' => 'まえばし',
			'suffix_kana' => 'し',
		], $sawa)
			->makePath($gunma);

		// 自分自身のパスが正しく設定されているか
		$this->assertSame(3, (int)$new->id);
		$this->assertSame('1/3/', $new->id_path);
		$this->assertSame('前橋市(2021)', $new->fullname);
		$this->assertSame('前橋', $new->name);
		$this->assertSame('市', $new->suffix);
		$this->assertSame('まえばし', $new->name_kana);
		$this->assertSame('し', $new->suffix_kana);
		$this->assertSame('(2021)', $new->identifier);
		$this->assertSame('群馬県/前橋市(2021)', $new->path);
		$this->assertSame('群馬県前橋市', $new->search_path);
		$this->assertSame('ぐんまけんまえばしし', $new->search_path_kana);
		$this->assertSame($gunma, $new->parent());

		// 子孫を更新
		$new->updateChild();

		// 子孫のパスが正しく設定されているか
		$akabori = Model_Division::find(4);
		$this->assertSame('群馬県/前橋市(2021)/赤堀町', $akabori->path);
		$this->assertSame('群馬県前橋市赤堀町', $akabori->search_path);
		$this->assertSame('ぐんまけんまえばししあかぼりまち', $akabori->search_path_kana);
	}

	public function test_createFromPath()
	{
		$gunma = Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			]
		);

		$saba = Model_Division::makeFromPath('群馬県/鯖郡');
		$this->assertSame(2, (int)$saba->id);
		$this->assertSame('1/2/', $saba->id_path);
		$this->assertSame('鯖郡', $saba->fullname);
		$this->assertSame('鯖', $saba->name);
		$this->assertSame('郡', $saba->suffix);
		$this->assertSame('群馬県/鯖郡', $saba->path);
		$this->assertSame('群馬県鯖郡', $saba->search_path);

		$kujira = Model_Division::makeFromPath('群馬県/海豚郡/鯨町');
		$this->assertSame(4, (int)$kujira->id);
		$this->assertSame('1/3/4/', $kujira->id_path);
		$this->assertSame('鯨町', $kujira->fullname);
		$this->assertSame('鯨', $kujira->name);
		$this->assertSame('町', $kujira->suffix);
		$this->assertSame('群馬県/海豚郡/鯨町', $kujira->path);
		$this->assertSame('群馬県海豚郡鯨町', $kujira->search_path);
	}

	public function test_duplicate()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("重複しています");
		$this->expectExceptionMessage("群馬県");

		Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			]
		);
		Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			]
		);
	}

	public function test_duplicateWithPath()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("重複しています");
		$this->expectExceptionMessage("群馬県/鯖郡");

		Model_Division::create2(
			[
				'fullname' => '群馬県',
				'name_kana' => 'ぐんま',
				'suffix_kana' => 'けん',
			]
		);

		Model_Division::makeFromPath('群馬県/鯖郡');
		Model_Division::makeFromPath('群馬県/鯖郡');
	}
}
