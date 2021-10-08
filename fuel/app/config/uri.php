<?php

return array(
	'top'   => '/',
	'login'   => 'login',
	'logout'  => 'logout',
	'search'  => 'search',
	'about'   => 'page/about',
	'link'    => 'page/link',

	'admin' => array(
		'division' => array(
			'list'    => 'admin/division',
			'add'     => 'admin/division/add',
			'add_csv' => 'admin/division/add_csv',
			'edit'    => 'admin/division/edit/:path',
			'delete'  => 'admin/division/delete/:path',
			'detail'  => 'admin/division/:path',
		),
		'reference' => array(
			'list'   => 'admin/reference',
			'add'    => 'admin/reference/add',
			'edit'   => 'admin/reference/:id/edit',
			'delete' => 'admin/reference/:id/delete',
		),
		'page' => array(
			'list'   => 'admin/page',
			'add'    => 'admin/page/add',
			'edit'   => 'admin/page/:id/edit',
			'delete' => 'admin/page/:id/delete',
		),
		'db' => array(
			'list'     => 'admin/db',
			'backup'   => 'admin/db/backup',
			'restore'  => 'admin/db/restore/:file',
			'delete'   => 'admin/db/delete/:file',
			'download' => 'admin/db/download/:file',
		),
	),
	'event' => array(
		'add'      => 'event/add',
		'detail'   => 'event/:id.json',
		'edit'     => 'event/:id/edit',
		'delete'   => 'event/:id/delete',
	),
	'division' => array(
		'detail'   => 'division/:path',
		'children' => 'division/children/:path?label=:label&start=:start&end=:end',
		'tree'     => 'tree/:path',
	),
	'geoshape' => 'geoshape?path=:path',
);
