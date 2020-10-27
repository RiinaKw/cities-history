<?php

class Helper_Governmentcode
{
	public static function normalize($code)
	{
		$body = '';
		$check = false;
		if (preg_match('/^(?<body>\d{5})-(?<check>\d)$/', $code, $matches)) {
			// is valid
			$body = $matches['body'];
			$check = $matches['check'];
		} else if (preg_match('/^(?<body>\d{5})(?<check>\d)$/', $code, $matches)) {
			// missing hyphen
			$body = $matches['body'];
			$check = $matches['check'];
		} else if (preg_match('/^\d{5}$/', $code, $matches)) {
			$body = $code;
		} else {
			throw new Exception('Invalid format');
		}

		$arr = str_split($body);
		$times = 6;
		$sum = 0;
		foreach ($arr as $digit) {
			$sum += (int)$digit * $times;
			--$times;
		}
		$remain = $sum % 11;
		$digit = (11 - $remain) % 10;

		if ($check === false) {
			$check = $digit;
		} else {
			if ($digit !== (int)$check) {
				throw new Exception('The check digits does not match');
			}
		}

		return sprintf('%05d-%01d', (int)$body, (int)$check);
	} // function normalize()
} // class Helper_Government_Code
