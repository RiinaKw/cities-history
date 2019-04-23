<?php
/**
 * The View Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_List extends Controller_Layout
{
	public function action_index()
	{
		$divisions = Model_Division::find_all();
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		// ビューを設定
		$content = View_Smarty::forge('list.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_index()
} // class Controller_List
