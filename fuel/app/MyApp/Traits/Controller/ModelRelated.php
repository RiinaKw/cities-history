<?php

namespace MyApp\Traits\Controller;

use MyApp\Abstracts\ActiveRecord;

/**
 * モデルと関連付けるコントローラのトレイト
 *
 * @package  App\Controller\Trait
 */
trait ModelRelated
{
	/**
	 * 関連するモデルのクラス名とカラム名
	 * @var array<string, string>
	 */
	//protected const MODEL_RELATED = [
	//	'model' => ActiveRecord::class,
	//	'key' => 'slug',
	//];

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	abstract static protected function notFound($value, ActiveRecord $obj = null): string;

	/**
	 * モデルを取得する
	 * @param  int|string  $value             getModelKey() で指定したキーに対する値
	 * @param  bool        $force             削除済みのレコードを取得するかどうか
	 * @return \MyApp\Abstracts\ActiveRecord  取得されたモデル
	 * @throws \HttpBadRequestException  レコードが存在しない場合、あるいは削除済みの場合
	 */
	protected static function getModel($value, bool $force = false): ActiveRecord
	{
		$config_constant = static::class . '::MODEL_RELATED';
		if (! defined($config_constant)) {
			throw new \Exception("constant '{$config_constant}' must be implemented");
		}
		$model = static::MODEL_RELATED['model'];
		$key = static::MODEL_RELATED['key'];

		if ($force) {
			$model::disable_filter();
		}
		$obj = $model::query()->where($key, $value)->get_one();
		if ($force) {
			$model::enable_filter();
		}
		if (! $obj) {
			$message = static::notFound($value);
			throw new \HttpNotFoundException($message);
		}
		if ($obj->deleted_at) {
			$message = static::notFound($value, $obj);
			throw new \HttpNotFoundException($message);
		}
		return $obj;
	}
	// function getModel()
}
// trait ModelRelated
