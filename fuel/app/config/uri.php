<?php

return array(
	'top'   => '/',
	'login'   => 'login',
	'logout'  => 'logout',
	'search'  => 'search',
	'about'   => 'page/about',
	'link'    => 'page/link',

	'admin' => array(
		'divisions' => array(
			'list'   => 'admin/divisions',
			'detail' => 'admin/divisions/:path',
		),
		'reference' => array(
			'list'   => 'admin/reference',
			'add'    => 'admin/reference/add',
			'edit'   => 'admin/reference/:id/edit',
			'delete' => 'admin/reference/:id/delete',
		),
		'page' => array(
			'list'   => 'admin/page',
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
		'add'      => 'division/add',
		'edit'     => 'division/edit/:path',
		'delete'   => 'division/delete/:path',
	),
	'list' => array(
		'division' => 'list/:path',
	),
	'geoshape' => 'geoshape?path=:path',
);
