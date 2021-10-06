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
	// protected static $pmodel_class = '';

	/**
	 * プレゼンテーションモデルを取得
	 * @return \MyApp\Abstracts\PresentationModel  プレゼンテーションモデル
	 */
	public function pmodel(): PresentationModel
	{
		$class = static::$pmodel_class;
		return new $class($this);
	}
}
