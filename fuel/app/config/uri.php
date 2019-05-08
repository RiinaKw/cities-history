<?php

return array(
	'top'   => '/',
	'login'   => 'login',
	'logout'  => 'logout',
	'search'  => 'search',

	'admin' => array(
		'divisions' => 'admin/divisions',
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
	'list' => array(
		'index' => '/',
		'division' => 'list/:path',
	),
);
