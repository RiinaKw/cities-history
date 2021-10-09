<?php

use MyApp\Abstracts\Controller;
use MyApp\Helper\Uri;

/**
 * 固定ページの表示を管理するコントローラ
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Page extends Controller
{
	use MyApp\Traits\Controller\ModelRelated;

	/**
	 * 関連するモデルのクラス名
	 * @return string
	 */
	protected static function getModelClass(): string
	{
		return Model_Page::class;
	}

	/**
	 * 関連するモデルで検索に使うカラム名
	 * @return string
	 */
	protected static function getModelKey(): ?string
	{
		return 'slug';
	}

	/**
	 * 検索で見つからなかった場合のメッセージ
	 * @param  int|string                         $value  getModelKey() で指定したキーに対する値
	 * @param  \MyApp\Abstracts\ActiveRecord|null $obj    削除済みを取得した場合、そのオブジェクト
	 * @return string
	 */
	protected static function notFound($value, Model_Page $obj = null)
	{
		if ($obj) {
			return "削除済みのページです。 slug : {$value}";
		} else {
			return "ページが見つかりません。 slug : {$value}";
		}
	}

	/**
	 * 何もせずトップにリダイレクトするだけ
	 * @return Response
	 */
	public function action_index(): Response
	{
		Uri::redirect('top');
	}
	// function action_index()

	/**
	 * 固定ページを表示
	 * @param  string   $slug  ページ名
	 * @return Response
	 */
	public function action_detail(string $slug): Response
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
