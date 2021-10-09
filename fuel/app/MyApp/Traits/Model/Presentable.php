<?php

namespace MyApp\Traits\Model;

use MyApp\Abstracts\PresentationModel;

/**
 * プレゼンテーションモデルを呼び出せるモデルのトレイト
 *
 * @package  App\Traits\Model
 */
trait Presentable
{
	// Property to implement in the model
	// protected const PMODEL_CLASS = Getter::class;

	/**
	 * プレゼンテーションモデルを取得
	 * @return \MyApp\Abstracts\PresentationModel  プレゼンテーションモデル
	 */
	public function pmodel(): PresentationModel
	{
		$const_name = static::class . '::PMODEL_CLASS';
		if (! defined($const_name)) {
			throw new \Exception("constant '{$const_name}' must be implemented");
		}
		$class = static::PMODEL_CLASS;
		return new $class($this);
	}
}
