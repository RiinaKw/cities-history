<?php

namespace MyApp\Traits\Model;

use MyApp\Abstracts\Getter;

/**
 * ゲッターを呼び出せるモデルのトレイト
 *
 * @package  App\Traits\Model
 */
trait Gettable
{
	// Constants to implement in the model
	// protected const GETTER_CLASS = Getter::class;

	/**
	 * ゲッターを取得
	 * @return \MyApp\Abstracts\Getter  ゲッター
	 */
	public function getter(): Getter
	{
		$const_name = static::class . '::GETTER_CLASS';
		if (! defined($const_name)) {
			throw new \Exception("constant '{$const_name}' must be implemented");
		}
		$class = static::GETTER_CLASS;
		return new $class($this);
	}
}
