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

		<!-- Begin page content -->
		<main role="main" class="container">

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

{{$content}}

		</main>

		<footer class="footer">
			<div class="container">
				<span class="text-muted">copyright</span>
			</div>
		</footer>
	</body>
</html>
