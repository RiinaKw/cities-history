<?php
/**
 * The Auth Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Auth extends Controller_Base
{
	// remember me に使用するクッキー名
	const COOKIE_REMEMBER_ME = 'user_hash';

	// remember me の有効期限（30日）
	const COOKIE_REMEMBER_ME_EXPIRE = 60 * 60 * 24 * 30;

	/**
	 * トップページ
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		if ($this->_user)
		{
			// 既にログインしている場合は認証をすっ飛ばす
			Helper_Uri::redirect('top');
		}
		else
		{
			// remember me クッキーをチェック
			$remember_me_hash = Cookie::get(self::COOKIE_REMEMBER_ME);
			if ($remember_me_hash)
			{
				// ハッシュ値から管理者情報を復元
				$user = Model_User::find_one_by_remember_me_hash($remember_me_hash);
				if ($user && ! $user->deleted_at)
				{
					$this->_remember_me($user);

					// ログイン成功時の処理
					$this->_login_success($user);
				}
			}
		}
		// ログインしていなければログインページへリダイレクト
		Helper_Uri::redirect('login');
	} // function action_index()

	/**
	 * remember me クッキーをセット
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
	} // function _remember_me()

	/**
	 * ログイン
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_login()
	{
		$redirect = Input::get('url');
		if ($this->_user)
		{
			// 既にログインしている場合は認証をすっ飛ばす
			if ($redirect)
			{
				Response::redirect($redirect);
			}
			else
			{
				Helper_Uri::redirect('top');
			}
		}

		$error_string = '';
		if(Input::post())
		{
			// 入力値を取得
			$login_id = Input::post('login_id');
			$password = Input::post('password');
			if ($login_id !== '' && $password !== '')
			{
				// ログインチェック
				$user = Model_User::login($login_id, $password);
				if ($user)
				{
					if (Input::post('remember-me'))
					{
						// ログイン状態を保存する
						$this->_remember_me($user);
					}
					// ログインに成功
					$user->frozen(true);
					Session::set('user', $user);
					Response::redirect($redirect);
				}
				else
				{
					$error_string = 'ユーザ名またはパスワードが違います。';
				}
			}
			else
			{
				$error_string = 'ユーザ名とパスワードを入力してください。';
			}
		}

		$view = View_Smarty::forge('login.tpl');
		$view->error_string = $error_string;
		$view->url_login = Helper_Uri::create('login', [], ['url' => Input::get('url')]);

		return $view;
	} // function action_login()

	/**
	 * ログアウト
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_logout()
	{
		$redirect = Input::get('url');
		if ($this->_user)
		{
			// remember me キーを削除
			$this->_user->is_new(false);
			$this->_user->frozen(false);
			$this->_user->remember_me_hash = null;
			$this->_user->save();
		}

		// remember me クッキーを削除
		Cookie::delete(self::COOKIE_REMEMBER_ME);

		// セッションを全削除
		Session::delete('user');
		Session::delete('user_data');

		// 見ていたページにリダイレクト
		Response::redirect($redirect);
	} // function action_logout()
} // class Controller_Top
