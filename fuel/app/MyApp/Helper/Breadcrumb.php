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
		Model_Division $division = null,
		string $secondary_label = null,
		string $secondary_uri = null
	): array {
		$breadcrumbs = [
			'Top' => Helper_Uri::create('top'),
		];
		if ($secondary_label) {
			$breadcrumbs[$secondary_label] = Helper_Uri::create($secondary_uri);
		}

		if ($division) {
			$ids = explode('/', substr($division->id_path, 0, -1));
			foreach ($ids as $id) {
				$parent = Model_Division::find($id);
				if (! $parent) {
					break;
				}
				$breadcrumbs[$parent->fullname] = $parent->pmodel()->url();
			}
		}

		return $breadcrumbs;
	}
	// function division()
}
