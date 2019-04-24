<?php
return array(
	'_root_'  => 'list/index',
	'_400_'   => 'error/400',    // The main 400 route
	'_403_'   => 'error/403',    // The main 403 route
	'_404_'   => 'error/404',    // The main 404 route
	'_500_'   => 'error/500',    // The main 500 route

	'add(/:path)?'  => 'add/index',

	'division/list' => 'rest/division/list',

	'event/add'          => 'event/add',
	'event/(.+?)/edit'   => 'event/edit/$1',
	'event/(.+?)/delete' => 'event/delete/$1',
	'event/(.+?)'        => 'rest/event/detail/$1',

	'division/belongto/:path' => 'division/belongto',
	'division/edit/:path'     => 'division/edit',
	'division/:path'          => 'division/detail',
);
