<?php

namespace MyApp\Helper;

use Model_Division;
use MyApp\Helper\Uri;

/**
 * パンくずリストを生成するヘルパークラス
 *
 * @package  App\Helper
 */
class Breadcrumb
{
	public static function division(
		Model_Division $division = null,
		string $secondary_label = null,
		string $secondary_uri = null
	): array {
		$breadcrumbs = [
			'Top' => Uri::create('top'),
		];
		if ($secondary_label) {
			$breadcrumbs[$secondary_label] = Uri::create($secondary_uri);
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
