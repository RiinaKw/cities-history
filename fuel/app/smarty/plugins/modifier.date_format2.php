<?php

function smarty_modifier_date_format2($input, $format)
{
	return MyApp\Helper\Date::format($format, $input);
}
