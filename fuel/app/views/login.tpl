<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>ログイン - {{Config::get('common.title')}}</title>
		<meta name="robots" content="noindex,nofollow" />

		<!-- jquery -->
		<script
			src="https://code.jquery.com/jquery-3.4.1.min.js"
			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			crossorigin="anonymous"></script>

		<!-- bootstrap -->
		<link
			rel="stylesheet"
			href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
			integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
			crossorigin="anonymous" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
			integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
			crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
			integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
			crossorigin="anonymous"></script>

		<!-- font awesome -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
			integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf"
			crossorigin="anonymous" />

	{{Asset::css('style.css')}}
	{{Asset::css('login.css')}}
	</head>
	<body class="text-center">

		<form class="form-signin col-8 offset-4 py-4" action="{{$url_login}}" method="post">
			<header>
				{{Asset::img('icon.png', ['width' => 100])}}
				<h1 class="h3 mt-3 font-weight-normal">{{Config::get('common.title')}}</h1>
				<h2 class="h3 mb-4 font-weight-normal">管理画面</h2>
			</header>
{{if $error_string}}
			<p class="text-danger">{{$error_string}}</p>
{{/if}}
			<label for="login_id" class="sr-only">login ID</label>
			<input class="form-control" type="text" id="login_id" name="login_id" placeholder="Login ID" required="required" autofocus="autofocus" />
			<label for="password" class="sr-only">Password</label>
			<input class="form-control" type="password" id="password" name="password" placeholder="Password" required="required" />
			<div class="checkbox mb-3">
				<label>
					<input type="checkbox" name="remember-me" value="true"> Remember me
				</label>
			</div>
			<nav>
				<button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
			</nav>
		</form>

	</body>
</html>
