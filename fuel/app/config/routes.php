<?php
return array(
	'_root_'  => 'list/index',
	'_400_'   => 'error/400',    // The main 400 route
	'_403_'   => 'error/403',    // The main 403 route
	'_404_'   => 'error/404',    // The main 404 route
	'_500_'   => 'error/500',    // The main 500 route

	'login'    => 'auth/login',
	'logout'   => 'auth/logout',

	'search'   => 'list/search',

	'division/list' => 'rest/division/list',

	'event/add'          => 'event/add',
	'event/(.+?)/edit'   => 'event/edit/$1',
	'event/(.+?)/delete' => 'event/delete/$1',
	'event/(.+?)'        => 'rest/event/detail/$1',

	'division/add'            => 'division/add',
	'division/edit/:path'     => 'division/edit',
	'division/delete/:path'   => 'division/delete',
	'division/belongto/:path' => 'division/belongto',
	'division/:path'          => 'division/detail',

	'list/:path'          => 'list/index',

	'admin/divisions/:path'        => 'admin/divisions/index',

	'admin/reference/add'          => 'admin/reference/add',
	'admin/reference/(.+?)/edit'   => 'admin/reference/edit/$1',
	'admin/reference/(.+?)/delete' => 'admin/reference/delete/$1',
	'admin/reference'              => 'admin/reference/list',
);
