<?php

return array(
	'_root_'  => 'top/index',
	'_400_'   => 'error/400',    // The main 400 route
	'_403_'   => 'error/403',    // The main 403 route
	'_404_'   => 'error/404',    // The main 404 route
	'_500_'   => 'error/500',    // The main 500 route

	'session_clear' => 'top/session_clear',
	'session_dump'  => 'top/session_dump',

	'geoshape' => 'geoshape/index',

	'login'    => 'auth/login',
	'logout'   => 'auth/logout',

	'search'   => 'top/search',

	'division/list' => 'rest/division/list',

	'event/add'          => 'admin/event/add',
	'event/(.+?)/edit'   => 'admin/event/edit/$1',
	'event/(.+?)/delete' => 'admin/event/delete/$1',
	'event/(.+?)'        => 'rest/event/detail/$1',

	'admin/division/add'          => 'admin/division/add',
	'admin/division/add_csv'      => 'admin/division/add_csv',
	'admin/division/edit/:path'   => 'admin/division/edit',
	'admin/division/delete/:path' => 'admin/division/delete',
	'admin/division'              => 'admin/division/index',
	'admin/division/:path'        => 'admin/division/index',

	'admin/reference/add'          => 'admin/reference/add',
	'admin/reference/(.+?)/edit'   => 'admin/reference/edit/$1',
	'admin/reference/(.+?)/delete' => 'admin/reference/delete/$1',
	'admin/reference'              => 'admin/reference/list',

	'admin/page'              => 'admin/page/list',
	'admin/page/add'          => 'admin/page/add',
	'admin/page/(.+?)/edit'   => 'admin/page/edit/$1',
	'admin/page/(.+?)/delete' => 'admin/page/delete/$1',

	'admin/db'                => 'admin/db/index',
	'admin/db/backup'         => 'admin/db/backup',
	'admin/db/(.+?)/restore'  => 'admin/db/restore/$1',
	'admin/db/(.+?)/delete'   => 'admin/db/delete/$1',
	'admin/db/(.+?)/download' => 'admin/db/download/$1',

	'page/(.+?)' => 'page/detail/$1',

	'rest/code'       => 'rest/code/index',
	'rest/code/(.+?)' => 'rest/code/detail/$1',

	/* 古い URI のリダイレクト */
	'page'                    => 'redirect/page_index',
	'list/:path'              => 'redirect/list_detail',
	'list'                    => 'redirect/list_index',
	'division/children/:path' => 'redirect/division_children',
	'division/:path'          => 'redirect/division_detail',

	/* 自治体関連の新しい URI */
	'children/:path' => 'division/children',
	'tree/:path'     => 'division/tree',
	':path'          => 'division/detail',
);
