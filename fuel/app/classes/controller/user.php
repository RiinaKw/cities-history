<?php
/**
 * The User Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_User extends Controller_Layout
{
	public function action_divisions()
	{
		$divisions = Model_Division::get_all();
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

			$division->valid_kana = $division->name_kana && $division->postfix_kana;
			$division->valid_start_event = !! $division->start_event_id;
			$division->valid_end_event = !! $division->end_event_id;
			$division->valid_government_code = ($division->postfix == '郡') || !! $division->government_code;
		}

		// ビューを設定
		$content = View_Smarty::forge('user/user_divisions.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '自治体一覧');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_index()
} // class Controller_List
