<?php

class Helper_Breadcrumb
{
	public static function breadcrumb(
		$division,
		$root_label = 'Top',
		$root_uri = 'top',
		$item_uri = 'division.detail'
	) {
		$breadcrumbs = [
			$root_label => Helper_Uri::create($root_uri),
		];

		$cur_path = '';
		if ($division) {
			$ids = explode('/', substr($division->id_path, 0, -1));
			foreach ($ids as $id) {
				$parent = Model_Division::find_by_pk($id);
				$fullname = $parent->get_fullname();
				$cur_path .= ($cur_path ? '/' : '') . $fullname;
				$breadcrumbs[$fullname] = Helper_Uri::create($item_uri, ['path' => $cur_path]);
			}
		}

		return $breadcrumbs;
	}
	// function breadcrumb()
}
// class Helper_Breadcrumb
