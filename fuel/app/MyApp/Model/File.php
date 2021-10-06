<?php

/**
 * @package  App\Model
 */

namespace MyApp\Model;

class File
{
	protected $fullpath = '';
	protected $props = [];

	public function __construct($path)
	{
		if (! realpath($path)) {
			throw new \Exception('File not found');
		}
		$this->fullpath = realpath($path);
		$this->refresh();
	}

	public static function create($path)
	{
		if (realpath($path)) {
			throw new \Exception('File already exists');
		}
		touch($path);
		return new static($path);
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
			'size'        => filesize($this->fullpath),
			'created_at'  => filectime($this->fullpath),
			'modified_at' => filemtime($this->fullpath),
		];
		return $this;
	}

	public function __get(string $key)
	{
		return $this->props[$key] ?? null;
	}
}
