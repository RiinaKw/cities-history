<?php

class Helper_Breadcrumb
{
	public static function breadcrumb_and_kana($divisions, $root_label = 'Top', $root_uri = 'top', $item_uri = 'division.detail')
	{
		$breadcrumbs = [
			$root_label => Helper_Uri::create($root_uri),
		];

		$cur_path = '';
		$cur_kana = '';
		if ($divisions)
		{
			foreach ($divisions as $parent)
			{
				$fullname = $parent->get_fullname();
				$cur_path .= ($cur_path ? '/' : '') . $fullname;
				$cur_kana .= ($cur_kana ? '/' : '') . $parent->get_fullname_kana();
				$breadcrumbs[$fullname] = Helper_Uri::create($item_uri, ['path' => $cur_path]);
			}
		}

		return [
			'breadcrumbs' => $breadcrumbs,
			'path_kana' => $cur_kana,
		];
	} // function _breadcrumb_and_kana()
} // class Helper_Breadcrumb
