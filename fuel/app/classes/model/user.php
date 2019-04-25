<?php

class Model_User extends Model_Base
{
	protected static $_table_name	= 'users';
	protected static $_primary_key	= 'id';
	protected static $_created_at	= 'created_at';
	protected static $_updated_at	= 'updated_at';
	protected static $_deleted_at	= 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation($is_new = false, $factory = null)	// 引数は単なる識別子、何でもいい
	{
		$validation = Validation::forge($factory);
		$validation->add_callable(new Helper_MyValidation());

		$arr = explode('.', $factory);
		$name = $arr[0];
		$id = $arr[1];

		$password = Input::post('password');

		// 入力ルール
		$field = $validation->add('login_id', 'ログインID')
			->add_rule('required')
			->add_rule('max_length', 100)
			->add_rule('unique', self::$_table_name.'.login_id.'.$id);

		$field = $validation->add('password', 'パスワード')
			->add_rule('max_length', 256);
		if ($is_new)
		{
			$field->add_rule('required');
		}
		$field = $validation->add('password_confirm', 'パスワード（確認）')
			->add_rule('max_length', 256);
		if ($is_new)
		{
			$field->add_rule('required');
		}
		$field->add_rule([
				'password_confirm' => function($password_confirm) use ($password)
				{
					if ($password === $password_confirm) {
						return true;
					} else {
						Validation::active()->set_message('password_confirm', 'パスワードが一致しません。');
						return false;
					}
				}
			]);

		return $validation;
	} // function validation()

	// Blowfish アルゴリズムを使ったパスワードのハッシュ化
	static function crypt_password($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	} // function crypt_password()

	// ログイン
	public static function login($login_id, $password)
	{
		// login_id に一致するレコードを取得
		$admin = self::find_one_by_login_id($login_id);
		if ($admin)
		{
			// パスワードが一致するかチェック
			$password_match = password_verify($password, $admin->password_crypt);
			if ($password_match)
			{
				// ログイン成功
				return $admin;
			}
		}
		return false;
	}

	// remember-me クッキーに使用するユニークIDを生成
	public static function create_remember_me_hash()
	{
		$query = DB::select('id', 'remember_me_hash')
			->from(self::$_table_name)
			->where('remember_me_hash', '!=', null);
		$result = $query->execute()->as_array();

		$table = array();
		foreach ($result as $item)
		{
			$table[] = $item['remember_me_hash'];
		}
		return Helper_Random::forge($table);
	} // functino create_remember_me_hash()
} // class Model_Admin
