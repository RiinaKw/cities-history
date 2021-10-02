<?php

class Helper_Session
{
	public static function user(): ?Model_User
	{
		$user_id = Session::get('user_id');
		return $user_id ? Model_User::find_by_pk($user_id) : null;
	}
	// function user()
}
// class Helper_Session
