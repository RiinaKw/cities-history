<?php

use MyApp\Abstracts\Controller;
use MyApp\Traits\Controller\ModelRelated;
use MyApp\Helper\Uri;

/**
 * 固定ページの表示を管理するコントローラ
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Page extends Controller
{
	use ModelRelated;

	/**
	 * 関連するモデルのクラス名とカラム名
	 * @var array<string, string>
	 */
	protected const MODEL_RELATED = [
		'model' => Model_Page::class,
		'key' => 'slug',
	];

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound($value, Model_Page $obj = null)
	{
		$key = static::MODEL_RELATED['key'];
		if ($obj) {
			return "削除済みのページです。 {$key} : {$value}";
		} else {
			return "ページが見つかりません。 {$key} : {$value}";
		}
	}

	/**
	 * 何もせずトップにリダイレクトするだけ
	 * @return \Response
	 */
	public function action_index()
	{
		Uri::redirect('top');
	}
	// function action_index()

	/**
	 * 固定ページを表示
	 * @param  string   $slug  ページ名
	 * @return \Response
	 */
	public function action_detail(string $slug)
	{
		$page = static::getModel($slug);

		// create Presenter object
		$content = Presenter_Page::forge();
		$content->page = $page;

		return $content;
	}
	// function action_detail()
}
// class Controller_Page
