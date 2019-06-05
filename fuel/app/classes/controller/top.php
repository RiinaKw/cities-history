<?php
/**
 * The Top Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Top extends Controller_Base
{
	public function action_index()
	{
		$divisions = Model_Division::get_top_level();
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		// ビューを設定
		$content = View_Smarty::forge('top.tpl');
		$content->divisions = $divisions;
		$content->url_add = Helper_Uri::create('division.add');
		$content->url_all_list = Helper_Uri::create('list.index');

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$content->components = $components;

		$this->_view->content = $content;
		$this->_view->title = '都道府県一覧';
		$this->_view->description = '全国の都道府県一覧';
		$this->_view->og_type = 'article';

		return $this->_view;
	} // function action_index()
} // class Controller_Top
