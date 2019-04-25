<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>{{$title}}</title>

	{{Asset::css('bootstrap.min.css')}}
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	{{Asset::css('style.css')}}

	{{Asset::js('jquery-3.3.1.min.js')}}
	{{Asset::js('bootstrap.min.js')}}
	{{Asset::js('jquery.autocomplete.min.js')}}
	</head>
	<body>
		<!-- Fixed navbar -->
		<header class="navbar navbar-expand-md navbar-light">
			<a class="navbar-brand" href="#">Cities History</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item active">
						<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Link</a>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled" href="#">Disabled</a>
					</li>
				</ul>
				<form class="form-inline mt-2 mt-md-0 mr-2" method="get" action="{{$url_search}}">
					<input class="form-control mr-sm-2" type="text" name="q" value="{{$q}}" placeholder="Search" aria-label="Search">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
				</form>
{{if $admin}}
				<a class="btn btn-outline-primary mt-sm-2 mt-md-0" href="{{$url_logout}}">Logout</a>
{{else}}
				<a class="btn btn-outline-primary mt-sm-2 mt-md-0" href="{{$url_login}}">Login</a>
{{/if}}
			</div>
		</header>

{{if isset($breadcrumbs) && $breadcrumbs}}
		<nav aria-label="パンくずリスト">
			<ol class="breadcrumb">
{{foreach from=$breadcrumbs key=name item=url}}
{{if $url}}
				<li class="breadcrumb-item"><a href="{{$url}}">{{$name}}</a></li>
{{else}}
				<li class="breadcrumb-item active" aria-current="page"><b>{{$name}}</b></li>
{{/if}}
{{/foreach}}
			<ol>
		</nav>
{{/if}}

		<!-- Begin page content -->
		<main role="main" class="container pb-2">

{{$content}}

		</main>

		<footer class="footer text-center mt-auto py-3">
			<div class="container">
				<span class="text-muted">{{Config::get('copyright.0')}}</span>
			</div>
		</footer>
	</body>
</html>
