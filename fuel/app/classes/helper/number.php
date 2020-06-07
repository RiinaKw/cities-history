<?php

class Helper_Number
{
	public static function bytes_format($bytes)
	{
		$unit = ['B', 'KiB', 'MiB', 'GiB'];

		$idx = 0;
		while ($bytes >= 1024) {
			$bytes /= 1024;
			++$idx;
		}
		$bytes = round($bytes, 2);
		return $bytes . ' ' . $unit[$idx];
	} // function bytes_format()
} // class Helper_Number
