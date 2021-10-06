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
	 * 関連するモデルのクラス名を返す
	 */
	abstract protected static function getModelClass(): string;

	/**
	 * モデルを取得する
	 * @param  int   $id                      レコードの ID
	 * @param  bool  $force                   削除済みのレコードを取得するかどうか
	 * @return \MyApp\Abstracts\ActiveRecord  取得されたモデル
	 * @throws \HttpBadRequestException  ID が不正な場合（引数のタイプヒンティングで制限かかってるから必要ないかも？）
	 * @throws \HttpBadRequestException  レコードが存在しない場合、あるいは削除済みの場合
	 */
	protected static function getModel(int $id, bool $force = false): ActiveRecord
	{
		if (! $id || ! is_numeric($id)) {
			throw new \HttpBadRequestException('不正なIDです。');
		}

		$model = static::getModelClass();
		if ($force) {
			$obj = $model::find_deleted($id);
		} else {
			$obj = $model::find($id);
		}

		if (! $obj) {
			throw new \HttpNotFoundException('参照が見つかりません。');
		}
		if (! $force && $obj->deleted_at) {
			throw new \HttpNotFoundException('削除済みです。');
		}
		return $obj;
	}
}
