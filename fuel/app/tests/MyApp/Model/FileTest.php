<?php

use PHPUnit\Framework\TestCase;
use MyApp\Model\File;

class FileTest extends TestCase
{
	protected $tmp_dir = '';

	protected function setUp(): void
	{
		$this->tmp_dir = realpath(__DIR__ . '../../../../tmp/test_tmp');
	}

	protected function unlinkIfExists($path)
	{
		if (file_exists($path)) {
			unlink($path);
		}
	}

	public function test_load()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);
		$file = new File($path);
		$this->assertSame(realpath($path), $file->path);
		$this->unlinkIfExists($path);
	}

	public function test_property()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);
		$file = new File($path);
		$this->assertSame('file_test.txt', $file->name);
		$this->assertSame('file_test', $file->filename);
		$this->assertSame('txt', $file->ext);
		$this->assertSame($this->tmp_dir, $file->dir);
		$this->unlinkIfExists($path);
	}

	public function test_filename()
	{
		$path = $this->tmp_dir . '/no_ext';
		touch($path);
		$file = new File($path);
		$this->assertSame('no_ext', $file->name);
		$this->assertSame('no_ext', $file->filename);
		$this->assertSame('', $file->ext);
		$this->unlinkIfExists($path);

		$path = $this->tmp_dir . '/.ext_only';
		touch($path);
		$file = new File($path);
		$this->assertSame('.ext_only', $file->name);
		$this->assertSame('', $file->filename);
		$this->assertSame('ext_only', $file->ext);
		$this->unlinkIfExists($path);
	}

	public function test_date()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		$this->unlinkIfExists($path);

		$time = time();
		touch($path, $time);
		$file = new File($path);
		// 同じファイル名だと削除しても作成日時は変わらない？？
		// $this->assertSame(date('Y-m-d H:i:s', $time), date('Y-m-d H:i:s', $file->created_at));
		$this->assertSame(date('Y-m-d H:i:s', $time), date('Y-m-d H:i:s', $file->modified_at));
		$this->unlinkIfExists($path);
	}

	public function test_notfound()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("not found");

		$path = $this->tmp_dir . 'noexists.txt';
		new File($path);
	}

	public function test_size()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);
		$file = new File($path);
		$this->assertSame(0, $file->size);

		$fp = fopen($path, 'wb');
		$string = '1234567890';
		fwrite($fp, $string);
		fclose($fp);

		$this->assertSame(10, $file->refresh()->size);
		$this->unlinkIfExists($path);
	}

	public function test_exists()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);

		$this->assertTrue(File::exists($path));

		$this->unlinkIfExists($path);
		$this->assertFalse(File::exists($path));
	}

	public function test_create()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		$this->unlinkIfExists($path);

		$file = File::create($path);
		$this->assertNotNull($file);
		$this->assertSame(realpath($path), $file->path);
		$this->unlinkIfExists($path);
	}

	public function test_create_fail()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("already exists");

		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);

		File::create($path);
	}

	public function test_delete()
	{
		$path = $this->tmp_dir . '/file_test.txt';
		touch($path);
		$file = new File($path);

		$file->delete();
		$this->assertFalse(file_exists($path));
	}
}
