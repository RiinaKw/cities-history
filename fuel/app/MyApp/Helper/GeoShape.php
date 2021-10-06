<?php

/**
 */

namespace MyApp\Helper;

/**
 * GeoShape に関するヘルパークラス
 *
 * @package  App\Helper
 * @todo  Controller_Geoshape に書いてる curl 関連もこっちに持ってこよう
 */
class GeoShape
{
	/**
	 * URL 解析に利用する正規表現
	 * @var string
	 */
	protected static $re = '/^https?:\/\/geoshape\.ex\.nii\.ac\.jp\/city\/geojson\/(.+)$/';

	/**
	 * GeoShape の URL を正規化する
	 * @param  string $url  元の URL
	 * @return string       正規化された URL
	 */
	public static function unify(string $url): string
	{
		return preg_replace(static::$re, '$1', $url);
	}
}
