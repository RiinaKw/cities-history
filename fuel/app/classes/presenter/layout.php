<?php

class Presenter_Layout extends Presenter_Base
{
	protected $_layout;

	public function before()
	{
		if ( ! $this->_layout)
		{
			$this->_layout = View_Smarty::forge('layout.tpl');
		}
	} // function before()

	public function set_global($key, $value)
	{
		$this->before();
		$this->_layout->$key = $value;
	} // function breadcrumbs()

	public function layout()
	{
		$this->before();

		$user_id = Session::get('user_id');
		$this->_user = Model_User::find_by_pk($user_id);
		$q = Input::get('q');

		$this->_layout->content = $this->_view;

		$this->_layout->title = '';
		$this->_layout->description = '';
		$this->_layout->og_type = '';
		$this->_layout->nav_item = '';
		$this->_layout->breadcrumbs = [];

		$this->_layout->set_global('user', $this->_user);
		$this->_layout->set_global('q', $q);

		$this->_layout->set_global('url_root', Helper_Uri::root());
		$this->_layout->set_global('url_login', Helper_Uri::create('login', [], ['url' => Helper_Uri::current()]));
		$this->_layout->set_global('url_logout', Helper_Uri::create('logout', [], ['url' => Helper_Uri::current()]));
		$this->_layout->set_global('url_search', Helper_Uri::create('search'));
		$this->_layout->set_global('url_admin_divisions', Helper_Uri::create('admin.divisions', ['path' => '']));
		$this->_layout->set_global('url_admin_reference', Helper_Uri::create('admin.reference.list'));

		return $this->_layout;
	} // function layout()
} // class Presenter_Layout
