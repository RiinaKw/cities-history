<?php

/**
 * @package  App\Getter
 */

namespace MyApp\Getter;

use MyApp\Abstracts\Getter;
use Model_Division;

/**
 * 自治体オブジェクトの値を取得・加工することに特化したクラス
 */
class Division extends Getter
{
	protected $model = null;

	public function __construct(Model_Division $model)
	{
		$this->model = $model;
	}

	/**
	 * @todo DB に「fullname」って必要なくない？
	 */
	public function __get(string $key): ?string
	{
		$arr = [
			'path' => function () {
				// 群馬県/甘楽郡(1950-)/下仁田町(1955-)
				$name_arr = [];
				$this->model->id_chain(function ($d) use (&$name_arr) {
					$name_arr[] = $d->getter()->fullname;
				});
				return implode('/', $name_arr);
			},
			'path_kana' => function () {
				// ぐんま・けん/かんら・ぐん/しもにた・まち
				$kana_arr = [];
				$this->model->id_chain(function ($d) use (&$kana_arr) {
					$kana_arr[] = $d->getter()->fullname_kana;
				});
				return implode('/', $kana_arr);
			},
			'search_path' => function () {
				// 群馬県甘楽郡下仁田町
				$name_arr = [];
				$this->model->id_chain(function ($d) use (&$name_arr) {
					$name_arr[] = $d->getter()->search_fullname;
				});
				return implode('', $name_arr);
			},
			'search_path_kana' => function () {
				// ぐんまけんかんらぐんしもにたまち
				$kana_arr = [];
				$this->model->id_chain(function ($d) use (&$kana_arr) {
					$kana_arr[] = $d->getter()->search_fullname_kana;
				});
				return implode('', $kana_arr);
			},
			'fullname' => function () {
				// 下仁田町(1955-)
				return $this->model->name
					. $this->suffix(function ($division) {
						return $division->suffix;
					})
					. ($this->model->identifier ? "({$this->model->identifier})" : '');
			},
			'fullname_kana' => function () {
				// しもにた・まち
				return $this->model->name_kana
					. $this->suffix(function ($division) {
						return '・' . $division->suffix_kana;
					});
			},
			'search_fullname' => function () {
				// 下仁田町
				return $this->model->name
					. $this->suffix(function ($division) {
						return $division->suffix;
					});
			},
			'search_fullname_kana' => function () {
				// しもにたまち
				return $this->model->name_kana
					. $this->suffix(function ($division) {
						return $division->suffix_kana;
					});
			},
			'parent_path' => function () {
				$path = $this->path;
				if (strpos($path, '/') !== false) {
					return dirname($path);
				}
			},
			'belongs_path' => function () {
				$division = $this->belongs(function ($division) {
					return $division->get_path();
				});
			},
			'belongs_name' => function () {
				$division = $this->belongs(function ($division) {
					return $division->fullname;
				});
			},
		];
		return isset($arr[$key]) ? $arr[$key]() : null;
	}
	// function __get()

	protected function suffix(callable $callback)
	{
		return $this->model->show_suffix ? $callback($this->model) : null;
	}

	protected function belongs(callable $callback)
	{
		$division = $this->model->belongs;
		return $division ? $callback($division) : null;
	}
}
