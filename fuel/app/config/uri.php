<?php

return array(
	'top'   => '/',
	'login'   => 'login',
	'logout'  => 'logout',
	'search'  => 'search',

	'admin' => array(
		'divisions' => 'admin/divisions/:path',
		'reference' => array(
			'list'   => 'admin/reference',
			'add'    => 'admin/reference/add',
			'edit'   => 'admin/reference/:id/edit',
			'delete' => 'admin/reference/:id/delete',
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
		'index' => 'list',
		'division' => 'list/:path',
	),
	'geoshape' => 'geoshape?path=:path',
);
