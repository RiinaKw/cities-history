
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$path|escape}}</h2>
					<p>{{$path_kana}}</p>
				</div>
{{if $admin}}
				<nav class="float-right">
					<button class="btn btn-success" data-toggle="modal" data-target="#add-division">自治体追加</button>
					<button class="btn btn-primary" data-toggle="modal" data-target="#edit-division">自治体変更</button>
					<button class="btn btn-danger" data-toggle="modal" data-target="#delete-division">自治体削除</button>
				</nav>
{{/if}}
			</header>
			<ul>
				<li><a href="{{$url_detail}}">自治体詳細</a></li>
				<li><a href="{{$url_belongto}}">所属自治体</a></li>
			</ul>

			<div class="col-md-6 offset-md-3 pb-3">
				<section class="timeline">
{{foreach name=events from=$events item=event}}
					<article
						class="editable {{if $event->birth}}birth{{/if}} {{if $event->live}}live{{/if}} {{if $event->death}}death{{/if}}"
						data-event-id="{{$event.event_id}}">
						<header class="clearfix">
							<h3 class="float-left">{{$event.type|escape}}</h3>
							<time class="float-right">{{$event.date|escape}}</time>
						</header>
						<ul>
{{foreach from=$event.divisions item=division}}
							<li>
								<a href="{{$division->url_detail|escape}}">
									{{$division.fullname|escape}}, {{$division.division_result|escape}}
								</a>
							</li>
{{/foreach}}
						</ul>
					</article>
{{foreachelse}}
					<p>no events</p>
{{/foreach}}
{{if $admin}}
					<span class="add"><i class="fas fa-plus"></i> 追加</span>
{{/if}}
				</section>
			</div>

{{if $admin}}

{{$components.add_division}}
{{$components.edit_division}}
{{$components.delete_division}}
{{$components.change_event}}

{{/if}}
