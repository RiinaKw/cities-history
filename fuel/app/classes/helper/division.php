<?php

class Helper_Division
{
	public static function get_children_url($path)
	{
		return [
			'平成' => Helper_Uri::create('division.children', ['label' => '平成', 'path' => $path, 'start' => '1989-01-01', 'end' => '2019-04-01']),
			'昭和後期' => Helper_Uri::create('division.children', ['label' => '昭和後期', 'path' => $path, 'start' => '1950-01-01', 'end' => '1988-12-31']),
			'大正～昭和前期' => Helper_Uri::create('division.children', ['label' => '大正～昭和前期', 'path' => $path, 'start' => '1912-01-01', 'end' => '1949-12-31']),
			'明治' => Helper_Uri::create('division.children', ['label' => '明治', 'path' => $path, 'start' => '1878-01-01', 'end' => '1911-12-31']),
		];
	} // function get_children_url()
} // class Helper_Division
