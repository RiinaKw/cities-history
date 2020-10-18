<?php

function smarty_modifier_date_format2($input, $format)
{
	return Helper_Date::format($format, $input);
}
