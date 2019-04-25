<?php

class Helper_Random
{
	public static function forge($exists)
	{
		do
		{
			$rand = uniqid(mt_rand() , true);
			$id = sha1($rand);
		}
		while (in_array($id, $exists));
		return $id;
	} // function forge()

	public static function forge_from_session_keys($session_name)
	{
		$sess = Session::get($session_name);
		if ( ! $sess)
		{
			$sess = array();
		}
		$arr = array();
		foreach ($sess as $key => $item)
		{
			$arr[] = $key;
		}
		return self::forge($arr);
	} // function forge_from_session_keys()
	
	public static function string($length)
	{
		return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
	}
}
