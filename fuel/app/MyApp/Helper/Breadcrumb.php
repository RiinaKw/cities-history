<?php

/**
 * @package  App\Helper
 */

namespace MyApp\Helper;

use Model_Division;
use Helper_Uri;

class Breadcrumb
{
	public static function division(
		Model_Division $division,
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
				$parent = Model_Division::find($id);
				$fullname = $parent->fullname;
				$cur_path .= ($cur_path ? '/' : '') . $fullname;
				$breadcrumbs[$fullname] = Helper_Uri::create($item_uri, ['path' => $cur_path]);
			}
		}

		return $breadcrumbs;
	}
	// function breadcrumb()
}
