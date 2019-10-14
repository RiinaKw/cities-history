<?php

class Presenter_Layout extends Presenter_Base
{
	protected $_layout;
	protected $_user;

	public function after()
	{
		$user_id = Session::get('user_id');
		$this->_user = Model_User::find_by_pk($user_id);
		$q = Input::get('q');

		// ビューのパラメータを取得
		$param = $this->_view->get();

		// タイトル生成
		$site_title = Config::get('common.title') . '（' . Config::get('common.title_ja') . '）';
		$title = $param['title'];
		if ($title)
		{
			$title .= ' - ' . $site_title;
		}
		else
		{
			$title = $site_title;
		}
		
		// レイアウトテンプレートに値をセット
		$this->q = $q;
		$this->user = $this->_user;
		
		$this->title = $title;
		$this->og_type = isset($param['og_type']) ? $param['og_type'] : '';
		$this->nav_item = isset($param['nav_item']) ? $param['nav_item'] : '';
		$this->breadcrumbs = isset($param['breadcrumbs']) ? $param['breadcrumbs'] : [];
		
		$this->url_root = Helper_Uri::root();
		$this->url_login = Helper_Uri::create('login', [], ['url' => Helper_Uri::current()]);
		$this->url_logout = Helper_Uri::create('logout', [], ['url' => Helper_Uri::current()]);
		$this->url_search = Helper_Uri::create('search');
		$this->url_admin_divisions = Helper_Uri::create('admin.divisions.list');
		$this->url_admin_reference = Helper_Uri::create('admin.reference.list');
	} // function after()
} // class Presenter_Layout
