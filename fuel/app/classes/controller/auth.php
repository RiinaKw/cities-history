<?php

/**
 * The Auth Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Auth extends Controller_Base
{
	// cookie name for remember-me
	protected const COOKIE_REMEMBER_ME = 'user_hash';

	// expire of remember-me (30 days)
	protected const COOKIE_REMEMBER_ME_EXPIRE = 60 * 60 * 24 * 30;

	/**
	 * Index
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		if ($this->user()) {
			// already logined
			Helper_Uri::redirect('top');
		} else {
			// check remember-me cookie
			$remember_me_hash = Cookie::get(self::COOKIE_REMEMBER_ME);
			if ($remember_me_hash) {
				// restore user from remember-me cookie hash
				$user = Model_User::find_one_by_remember_me_hash($remember_me_hash);
				if ($user && ! $user->deleted_at) {
					$this->_remember_me($user);

					// login success
					$this->_login_success($user);
				}
			}
		}
		// if is not logined, redirect to login form
		Helper_Uri::redirect('login');
	}
	// function action_index()

	/**
	 * set remember-me cookie
	 *
	 * @access  protected
	 * @return  Response
	 */
	protected function _remember_me($user)
	{
		$hash = Model_User::create_remember_me_hash();
		Cookie::set(self::COOKIE_REMEMBER_ME, $hash, self::COOKIE_REMEMBER_ME_EXPIRE);
		$user->remember_me_hash = $hash;
		$user->save();
	}
	// function _remember_me()

	/**
	 * Log in
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_login()
	{
		$redirect = Input::get('url');
		if ($this->user()) {
			// already logined
			if ($redirect) {
				// redirect to previous page
				Response::redirect($redirect);
			} else {
				Helper_Uri::redirect('top');
			}
		}

		$error_string = '';
		if (Input::post()) {
			// get form input
			$login_id = Input::post('login_id');
			$password = Input::post('password');
			if ($login_id !== '' && $password !== '') {
				// validate for login
				$user = Model_User::login($login_id, $password);
				if ($user) {
					if (Input::post('remember-me')) {
						// set remember-me
						$this->_remember_me($user);
					}
					// login success
					Model_Activity::insert_log([
						'user_id' => $user->id,
						'target' => 'login',
						'target_id' => null,
					]);
					Session::set('user_id', $user->id);
					Response::redirect($redirect);
				} else {
					// wrong input
					$error_string = 'ユーザ名またはパスワードが違います。';
				}
			} else {
				// empty input
				$error_string = 'ユーザ名とパスワードを入力してください。';
			}
		}

		// create Presenter object
		$content = Presenter::forge('login', 'view', null, 'login.tpl');
		$content->error_string = $error_string;

		return $content;
	}
	// function action_login()

	/**
	 * Lot out
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_logout()
	{
		$redirect = Input::get('url');
		if ($this->user()) {
			// forget remember-me
			$user = $this->user();
			$user->remember_me_hash = null;
			$user->save();
		}

		// delete remember-me cookie
		Cookie::delete(self::COOKIE_REMEMBER_ME);

		// delete all sessions
		Session::delete('user_id');
		Session::delete('user_data');

		// redirect to previous page
		Response::redirect($redirect);
	}
	// function action_logout()
}
// class Controller_Auth
