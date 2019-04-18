<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>Hello</title>

	{{Asset::css('bootstrap.min.css')}}
		<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


	{{Asset::js('jquery-3.3.1.min.js')}}
	{{Asset::js('jquery-ui.min.js')}}
	{{Asset::js('bootstrap.min.js')}}
	</head>
	<body>

		<!-- Begin page content -->
		<main role="main" class="container">

{{$content}}

		</main>

		<footer class="footer">
			<div class="container">
				<span class="text-muted">copyright</span>
			</div>
		</footer>
	</body>
</html>
