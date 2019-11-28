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

		// get view parameters
		$param = $this->_view->get();

		// create title
		$page_title = $param['title'];
		$site_title = Config::get('common.title') . '（' . Config::get('common.title_ja') . '）';
		if ($page_title)
		{
			$title = $page_title . ' - ' . $site_title;
		}
		else
		{
			$title = $site_title;
		}

		// set to template
		$this->q = $q;
		$this->user = $this->_user;

		$this->title = $title;
		$this->page_title = $page_title;
		$this->og_type = isset($param['og_type']) ? $param['og_type'] : '';
		$this->nav_item = isset($param['nav_item']) ? $param['nav_item'] : '';
		$this->breadcrumbs = isset($param['breadcrumbs']) ? $param['breadcrumbs'] : [];

		$this->url_root = Helper_Uri::root();
		$this->url_login = Helper_Uri::create('login', [], ['url' => Helper_Uri::current()]);
		$this->url_logout = Helper_Uri::create('logout', [], ['url' => Helper_Uri::current()]);
		$this->url_search = Helper_Uri::create('search');
		$this->url_about = Helper_Uri::create('about');
		$this->url_link = Helper_Uri::create('link');
		$this->url_admin_divisions = Helper_Uri::create('admin.divisions.list');
		$this->url_admin_reference = Helper_Uri::create('admin.reference.list');
	} // function after()
} // class Presenter_Layout
