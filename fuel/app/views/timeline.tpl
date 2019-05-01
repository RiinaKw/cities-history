
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$path|escape}}</h2>
					<p>{{$path_kana}}</p>
				</div>
{{if $user}}
				<nav class="float-right">
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-division">
						<i class="fa fa-plus"></i>
						自治体追加
					</button>
					<button class="btn btn-primary mb-1" data-toggle="modal" data-target="#edit-division">
						<i class="fa fa-edit"></i>
						自治体変更
					</button>
					<button class="btn btn-danger mb-1" data-toggle="modal" data-target="#delete-division">
						<i class="fa fa-trash"></i>
						自治体削除
					</button>
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
							<time class="float-right">{{$event.date|date_format2:'Y(Jk)-m-d'}}</time>
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
{{if $user}}
					<span class="add"><i class="fas fa-plus"></i> イベントを追加…</span>
{{/if}}
				</section>
			</div>

{{if $user}}

{{$components.add_division}}
{{$components.edit_division}}
{{$components.delete_division}}
{{$components.change_event}}

{{/if}}
