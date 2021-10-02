<?php

class Helper_String
{
	public static function to_hiragana($str)
	{
		return mb_convert_kana($str, "HVc");
	}
	// function to_hiragana()
}
// class Helper_String
