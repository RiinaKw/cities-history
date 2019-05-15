
			<header class="clearfix">
				<div class="float-left">
					<h2>Division Manage</h2>
				</div>
			</header>

			<div>
				<section>
{{foreach from=$divisions item=division}}
					<article class="card mb-4">
						<header class="card-header px-3 pb-1">
							<h3 class="h5 float-left">
								<a href="{{$division.url_detail}}">{{$division->path}}</a>
							</h3>
							<a class="float-left ml-4" href="{{$division.url_belongto}}">所属自治体</a>
						</header>
						<div class="card-body p-3">
{{if ! $division->valid_kana}}
							<div class="alert alert-warning" role="alert">
								<strong>Warning!</strong>
								かなが入力されていません。
							</div>
{{/if}}
{{if ! $division->valid_start_event}}
							<div class="alert alert-warning" role="alert">
								<strong>Warning!</strong>
								開始イベントが指定されていません。
							</div>
{{/if}}
{{if ! $division->valid_end_event}}
							<div class="alert alert-warning" role="alert">
								<strong>Warning!</strong>
								終了イベントが指定されていません。
							</div>
{{/if}}
{{if ! $division->valid_government_code}}
							<div class="alert alert-warning" role="alert">
								<strong>Warning!</strong>
								全国地方公共団体コードが指定されていません。
							</div>
{{/if}}
{{if $division->valid_kana && $division->valid_start_event && $division->valid_end_event && $division->valid_government_code}}
							<div class="alert alert-success" role="alert">
								<strong>Yeah!</strong>
								データは完璧です！
							</div>
{{/if}}
						</div>
					</article>
{{/foreach}}
				</section>
			</div>
