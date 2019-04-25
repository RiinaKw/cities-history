
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
							<h3 class="h5">
								<a href="{{$division.url_detail}}">{{$division->path}}</a>
							</h3>
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
{{if $division->valid_kana && $division->valid_start_event && $division->valid_end_event}}
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
