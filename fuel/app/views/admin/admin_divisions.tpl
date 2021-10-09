{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left mb-2">
					<h2>Division Manage</h2>
				</div>
				<nav class="float-right mb-2">
					<form class="form-inline" action="#" method="get">
						<div class="input-group">
							<select class="form-control" name="filter">
{{foreach from=$filters key=val item=label}}
								<option value="{{$val}}"{{if $filter == $val}} selected="selected"{{/if}}>
									{{$label}}
								</option>
{{/foreach}}
							</select>
							<span class="input-group-append">
								<button class="btn btn-primary">検索</button>
							</span>
						</div>
					</form>
				</nav>
			</header>

			<div>
				<section>
{{foreach from=$divisions item=division}}
{{assign var=pmodel value=$division->pmodel()}}
					<article class="card mb-4">
						<header class="card-header px-3 pb-1">
							<h3 class="h5 float-left">
								<a href="{{$pmodel->url()}}">{{$division->path}}</a>
							</h3>
							<a class="float-left ml-4" href="{{$division.url_belongto}}">所属自治体</a>
						</header>
						<div class="card-body p-3">
{{if ! $pmodel->isValid('kana')}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-keyboard"></i>
								<strong>Warning!</strong>
								かなが入力されていません。
							</div>
{{/if}}
{{if ! $pmodel->isValid('start')}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-history"></i>
								<strong>Warning!</strong>
								開始イベントが指定されていません。
							</div>
{{/if}}
{{if ! $pmodel->isValid('end')}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-history"></i>
								<strong>Warning!</strong>
								終了イベントが指定されていません。
							</div>
{{/if}}
{{if ! $pmodel->isValid('code')}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-ban"></i>
								<strong>Warning!</strong>
								全国地方公共団体コードが指定されていません。
							</div>
{{/if}}
{{if ! $pmodel->isValid('source')}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-file"></i>
								<strong>Warning!</strong>
								出典が入力されていません。
							</div>
{{/if}}
{{if $pmodel->isWikipedia()}}
							<div class="alert alert-warning" role="alert">
								<i class="fa fa-fw fa-file"></i>
								<strong>Warning!</strong>
								出典が Wikipedia です。
							</div>
{{/if}}
{{if $pmodel->isValidAll()}}
							<div class="alert alert-success" role="alert">
								<i class="fa fa-fw fa-check"></i>
								<strong>Yeah!</strong>
								データは完璧です！
							</div>
{{/if}}
						</div>
					</article>
{{/foreach}}
				</section>
			</div>

{{/block}}
