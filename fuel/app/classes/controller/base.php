<?php
/**
 * The Base Controller.
 *
 * コントローラの基底クラス
 *
 * @package  app
 * @extends  Controller
 */
abstract class Controller_Base extends Controller
{
	protected $_view = null;
	protected $_user = null;
	
	public function before()
	{
		parent::before();

		Config::load('uri', true);
		Config::load('common', true);

		$admin_id = Session::get('user.id');
		$this->_user = Model_User::find_by_pk($admin_id);
		$q = Input::get('q');
		
		$this->_view = View_Smarty::forge('layout.tpl');
		
		$this->_view->set_global('title', '');
		$this->_view->set_global('nav_item', '');
		
		$this->_view->set_global('user', $this->_user);
		$this->_view->set_global('q', $q);
		
		$this->_view->set_global('root', Helper_Uri::root());
		$this->_view->set_global('url_login', Helper_Uri::create('login', [], ['url' => Helper_Uri::current()]));
		$this->_view->set_global('url_logout', Helper_Uri::create('logout', [], ['url' => Helper_Uri::current()]));
		$this->_view->set_global('url_search', Helper_Uri::create('search'));
		$this->_view->set_global('url_admin_divisions', Helper_Uri::create('admin.divisions', ['path' => '']));
		$this->_view->set_global('url_admin_reference', Helper_Uri::create('admin.reference.list'));
	} // function before()

	public function after($response)
	{
		$response = parent::after($response);
		return $response;
	} // function after()
} // class Controller_Base
