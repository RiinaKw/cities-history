<?php

return array(
	'top'   => '/',
	'login'   => 'login',
	'logout'  => 'logout',
	'search'  => 'search',

	'user' => array(
		'divisions' => 'user/divisions',
	),
	'event' => array(
		'add'      => 'event/add',
		'detail'   => 'event/:id.json',
		'edit'     => 'event/:id/edit',
		'delete'   => 'event/:id/delete',
	),
	'division' => array(
		'detail'   => 'division/:path',
		'belongto' => 'division/belongto/:path',
		'add'      => 'division/add',
		'edit'     => 'division/edit/:path',
		'delete'   => 'division/delete/:path',
	),
	'list' => '/'
);
