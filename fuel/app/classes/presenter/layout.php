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

		// レイアウトテンプレートの初期値
		$this->_layout = View_Smarty::forge('layout.tpl');
		$this->_layout->title = '';
		$this->_layout->title_ja = '';
		$this->_layout->description = '';
		$this->_layout->og_type = '';
		$this->_layout->nav_item = '';
		$this->_layout->breadcrumbs = [];

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
		$param['title'] = $title;

		// レイアウトテンプレートに値をセット
		$this->_layout->set($param);
		$this->_layout->content = $this->_view;

		// 各種変数
		$this->_layout->set_global('user', $this->_user);
		$this->_layout->set_global('q', $q);

		$this->_layout->set_global('url_root', Helper_Uri::root());
		$this->_layout->set_global('url_login', Helper_Uri::create('login', [], ['url' => Helper_Uri::current()]));
		$this->_layout->set_global('url_logout', Helper_Uri::create('logout', [], ['url' => Helper_Uri::current()]));
		$this->_layout->set_global('url_search', Helper_Uri::create('search'));
		$this->_layout->set_global('url_admin_divisions', Helper_Uri::create('admin.divisions.list'));
		$this->_layout->set_global('url_admin_reference', Helper_Uri::create('admin.reference.list'));

		// ビューをレイアウトに差し替え
		$this->_view = $this->_layout;
	} // function after()
} // class Presenter_Layout
