<?php

class Helper_Breadcrumb
{
	public static function breadcrumb_and_kana($path)
	{
		$breadcrumbs = [
			'一覧' => Helper_Uri::create('top'),
		];
		$arr = explode('/', $path);
		$cur_path = '';
		$cur_kana = '';
		if ($arr)
		foreach ($arr as $name)
		{
			if ($cur_path)
			{
				$cur_path .= '/'.$name;
			}
			else
			{
				$cur_path .= $name;
			}
			$cur_division = Model_Division::get_by_path($cur_path);
			if ($cur_division)
			{
				$cur_kana .= ($cur_kana ? '/' : '').$cur_division->get_kana();
				$breadcrumbs[$name] = Helper_Uri::create('division.detail', ['path' => $cur_path]);
			}
		} // foreach ($arr as $name)

		return [
			'breadcrumbs' => $breadcrumbs,
			'path_kana' => $cur_kana,
		];
	} // function _breadcrumb_and_kana()
} // class Helper_Breadcrumb
