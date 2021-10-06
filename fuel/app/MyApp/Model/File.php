<?php

/**
 * @package  App\Model
 */

namespace MyApp\Model;

use MyApp\PresentationModel\File as PModel;
use MyApp\Helper\Number;

class File
{
	protected $fullpath = '';
	protected $props = [];

	public function __construct($path)
	{
		if (! static::exists($path)) {
			throw new \Exception("File '{$path}' not found");
		}
		$this->fullpath = realpath($path);
		$this->refresh();
	}

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	public static function create($path)
	{
		if (static::exists($path)) {
			throw new \Exception("File '{$path}' already exists");
		}
		touch($path);
		return new static($path);
	}

	public static function exists(string $path)
	{
		return file_exists($path);
	}

	public function refresh(): self
	{
		clearstatcache(true, $this->fullpath);
		$info = pathinfo($this->fullpath);
		$this->props = [
			'path'        => $this->fullpath,
			'name'        => $info['basename'] ?? '',
			'dir'         => $info['dirname'] ?? '',
			'filename'    => $info['filename'] ?? '',
			'ext'         => $info['extension'] ?? '',
			'bytes'        => filesize($this->fullpath),
			'created_at'  => filectime($this->fullpath),
			'modified_at' => filemtime($this->fullpath),
		];
		return $this;
	}

	public function __get(string $key)
	{
		return $this->props[$key] ?? null;
	}

	public function delete()
	{
		unlink($this->path);
	}
}
