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
	// Property to implement in the model
	// protected static $getter_class = Getter::class;

	public function getter(): Getter
	{
		$class = static::$getter_class;
		return new $class($this);
	}
}
