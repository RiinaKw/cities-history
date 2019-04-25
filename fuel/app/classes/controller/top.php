<?php
/**
 * The Top Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Top extends Controller_Base
{
	// remember me に使用するクッキー名
	const COOKIE_REMEMBER_ME = 'admin_hash';

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
		$admin_id = Session::get('admin.id');
		$admin = Model_Admin::find_by_pk($admin_id);
		if ($admin)
		{
			// 既にログインしている場合は認証をすっ飛ばす
			Helper_Uri::redirect('list');
		}
		else
		{
			// remember me クッキーをチェック
			$remember_me_hash = Cookie::get(self::COOKIE_REMEMBER_ME);
			if ($remember_me_hash)
			{
				// ハッシュ値から管理者情報を復元
				$admin = Model_Admin::find_one_by_remember_me_hash($remember_me_hash);
				if ($admin && ! $admin->deleted_at)
				{
					$this->_remember_me($admin);

					// ログイン成功時の処理
					$this->_login_success($admin);
				}
			}
		}
		// ログインしていなければログインページへリダイレクト
		Helper_Uri::redirect('login');
	}

	/**
	 * remember me クッキーをセット
	 *
	 * @access  protected
	 * @return  Response
	 */
	protected function _remember_me($admin)
	{
		$hash = Model_Admin::create_remember_me_hash();
		Cookie::set(self::COOKIE_REMEMBER_ME, $hash, self::COOKIE_REMEMBER_ME_EXPIRE);
		$admin->remember_me_hash = $hash;
		$admin->save();
	}

	/**
	 * ログイン成功時のセッション保存とリダイレクト処理
	 *
	 * @access  protected
	 * @return  Response
	 */
	protected function _login_success($admin)
	{
		// ログインに成功したら管理者ダッシュボードへリダイレクト
		$admin->frozen(true);
		Session::set('admin.id', $admin->id);
		Helper_Uri::redirect('list');
	}

	/**
	 * ログイン
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_login()
	{
		$admin_id = Session::get('admin.id');
		$admin = Model_Admin::find_by_pk($admin_id);
		if ($admin)
		{
			// 既にログインしている場合は認証をすっ飛ばす
			Helper_Uri::redirect('admin.dashboard');
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
				$admin = Model_Admin::login($login_id, $password);
				if ($admin)
				{
					if (Input::post('remember-me'))
					{
						// ログイン状態を保存する
						$this->_remember_me($admin);
					}
					// ログインに成功
					$this->_login_success($admin);
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
		$view->url_login = Helper_Uri::create('login');

		return $view;
	}

	/**
	 * ログアウト
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_logout()
	{
		$admin_id = Session::get('admin.id');
		$admin = Model_Admin::find_by_pk($admin_id);
		if ($admin)
		{
			// remember me キーを削除
			$admin->is_new(false);
			$admin->frozen(false);
			$admin->remember_me_hash = null;
			$admin->save();
		}

		// remember me クッキーを削除
		Cookie::delete(self::COOKIE_REMEMBER_ME);

		// セッションを全削除
		Session::delete('admin');
		Session::delete('admin_data');

		// トップページへリダイレクト
		Helper_Uri::redirect('list');
	}
}
