<?php
return array(
	'_root_'  => 'top/index',
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
	'division/children/:path' => 'division/children',
	'division/:path'          => 'division/detail',

	'list/:path'         => 'list/detail',
	'list'               => 'list/index',

	'admin/divisions/:path'        => 'admin/divisions/index',

	'admin/reference/add'          => 'admin/reference/add',
	'admin/reference/(.+?)/edit'   => 'admin/reference/edit/$1',
	'admin/reference/(.+?)/delete' => 'admin/reference/delete/$1',
	'admin/reference'              => 'admin/reference/list',

	'admin/page'              => 'admin/page/list',

	'admin/db'          => 'admin/db/index',
	'admin/db/backup'   => 'admin/db/backup',
	'admin/db/restore'  => 'admin/db/restore',
	'admin/db/delete'   => 'admin/db/delete',
	'admin/db/download' => 'admin/db/download',

	'page/:slug' => 'page/detail',
);
