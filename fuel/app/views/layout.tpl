<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>{{$title}} - {{Config::get('common.title')}}</title>

	{{Asset::css('bootstrap.min.css')}}
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	{{Asset::css('style.css')}}
		<link rel="stylesheet" href="https://unpkg.com/leaflet@0.7.3/dist/leaflet.css"/>

	{{Asset::js('jquery-3.3.1.min.js')}}
	{{Asset::js('jquery-ui.min.js')}}
	{{Asset::js('bootstrap.min.js')}}
	{{Asset::js('jquery.autocomplete.min.js')}}
		<script src="https://unpkg.com/leaflet@0.7.3/dist/leaflet.js"></script>
	</head>
	<body>
		<!-- Fixed navbar -->
		<header class="navbar fixed-top navbar-expand-md navbar-light">
			<h1><a class="navbar-brand" href="{{$root}}">Cities History Project</a></h1>
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
{{if isset($breadcrumbs) && $breadcrumbs}}
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

			<main role="main" class="container pb-2">

{{$content}}

			</main>
		</div><!-- .main-container -->

		<footer class="footer text-center mt-auto py-3">
			<div class="container">
				<div class="copyright text-muted mx-2">{{Config::get('common.copyright')}}</div>
				<div class="small text-muted">
					行政区域データは
					<a href="http://geoshape.ex.nii.ac.jp/city/" target="_blank">歴史的行政区域データセットβ版 | Geoshapeリポジトリ</a>
					の図形データを使用しています。
				</div>
			</div>
		</footer>
	</body>
</html>
