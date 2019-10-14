<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>{{$title}}</title>
		<meta name="description" content="{{$description}}" />
{{if isset($robots)}}
		<meta name="robots" content="{{$robots}}" />
{{/if}}
		<meta property="og:url" content="{{Helper_Uri::current()}}" />
		<meta property="og:title" content="{{$title}}" />
		<meta property="og:site_name" content="{{Config::get('common.title')}}" />
		<meta property="og:description" content="{{$description}}" />
		<meta property="og:image" content="{{Asset::get_file('icon.png', 'img')}}">
		<meta property="og:type" content="{{$og_type}}" />
		<meta property="og:locale" content="ja_JP" />

		<meta property="fb:app_id" content="801803333535562" />

		<!-- jquery -->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

		<!-- bootstrap -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

		<!-- font awesome -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" />

		<!-- Leaflet interactive maps library -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@0.7.3/dist/leaflet.css"/>
		<script src="https://unpkg.com/leaflet@0.7.3/dist/leaflet.js"></script>

	{{Asset::css('style.css')}}
	{{Asset::js('jquery.autocomplete.min.js')}}

	{{Asset::js('geoshape.js')}}
	</head>
	<body>
		{{Fuel::$env}}
		<!-- Fixed navbar -->
		<header class="navbar fixed-top navbar-expand-md navbar-light">
			<h1>
				<a class="navbar-brand" href="{{$url_root}}">
					{{Config::get('common.title')}}
				</a>
			</h1>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
{{if $user}}
					<li class="nav-item">
						<a class="nav-link {{if $nav_item == 'division'}}active{{/if}}"
							href="{{$url_admin_divisions}}">
							<i class="fa fa-map"></i>
							Division
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{if $nav_item == 'reference'}}active{{/if}}"
							href="{{$url_admin_reference}}">
							<i class="fa fa-calendar-alt"></i>
							Date Reference
						</a>
					</li>
{{/if}}
				</ul>
				<form class="form-inline mt-2 mt-md-0 mr-2" method="get" action="{{$url_search}}">
					<div class="input-group">
						<input class="form-control" type="search" name="q" value="{{$q}}" placeholder="Search" aria-label="Search">
						<span class="input-group-append">
							<button class="btn btn-outline-default mb-2 my-sm-0" type="submit">
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</form>
{{if $user}}
				<a class="btn btn-outline-danger mt-sm-2 mt-md-0" href="{{$url_logout}}">Logout</a>
{{else}}
				<a class="btn btn-outline-success mt-sm-2 mt-md-0" href="{{$url_login}}">Login</a>
{{/if}}
			</div>
		</header>

		<div class="main-container">
{{if $breadcrumbs}}
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
{{foreach name=breadcrumbs from=$breadcrumbs key=name item=url}}
{{if $url && ! $smarty.foreach.breadcrumbs.last}}
					<li class="breadcrumb-item"><a href="{{$url}}">{{$name}}</a></li>
{{else}}
					<li class="breadcrumb-item active" aria-current="page"><b>{{$name}}</b></li>
{{/if}}
{{/foreach}}
				<ol>
			</nav>
{{/if}}

			<main role="main" class="container {{strip}}
				{{if ! $breadcrumbs}}
					no-breadcrumb
				{{/if}}
			{{/strip}}">

{{block name=content}}{{/block}}

			</main>
		</div><!-- .main-container -->

		<footer class="footer text-center mt-auto py-3">
			<div class="container">
				<div class="copyright text-muted mx-2">{{Config::get('common.copyright')}}</div>
				<div class="small text-muted">{{Config::get('common.geoshape')}}</div>
			</div>
		</footer>

{{if Fuel::$env == 'production'}}
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-46798910-3"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-46798910-3');
		</script>
{{/if}}
	</body>
</html>
