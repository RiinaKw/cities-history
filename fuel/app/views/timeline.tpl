{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>
						{{$path|escape}}
{{if $belongs_division}}
						{{strip}}
							（
							<a href="{{$belongs_division->pmodel()->url()}}">
								{{$belongs_division->fullname|escape}}
							</a>
							）
						{{/strip}}
{{/if}}
					</h2>
					<p>{{$path_kana}}</p>
{{if $division->government_code}}
					<p >全国地方公共団体コード : {{$division->government_code|escape}}</p>
{{/if}}
{{if $division->source}}
					<p class="source">出典 :<br />{{$division->pmodel()->source()}}</p>
{{/if}}
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

			<nav class="timeline-nav">
				<ul>
					<li class="{{if $current == 'detail'}}active{{/if}}">
						<a href="{{$url_detail_timeline}}">自治体タイムライン</a>
					</li>
					<li class="">
						<a href="{{$url_detail}}">所属自治体</a>
					</li>
					<li>
						所属自治体タイムライン
						<ul>
{{foreach from=$url_children_timeline key=label item=url}}
							<li class="{{if $current == $label}}active{{/if}}">
								<a href="{{$url}}">{{$label}}</a>
							</li>
{{/foreach}}
						</ul>
					</li>
				</ul>
		</nav>

			<div class="col-md-10 offset-md-1 pb-3">
				<section class="timeline">
{{foreach name=events from=$events item=event}}
					<article
						class="row editable {{if $event->birth}}birth{{/if}} {{if $event->live}}live{{/if}} {{if $event->death}}death{{/if}}"
						data-event-id="{{$event.event_id}}">
						<section class="col-sm-7">
							<header>
								<div class="clearfix">
									<h3 class="float-left">{{$event.title|escape}}</h3>
									<time class="float-right" datetime="{{$event.date|escape}}">{{$event.date|date_format2:'Y(Jk)-m-d'}}</time>
								</div>
{{if $event.comment}}
								<p class="comment">{{$event.comment|escape}}</p>
{{/if}}
{{if $event.source}}
								<p class="source">出典 :<br />{{$event->get_source()}}</p>
								<p class="source_preformat">{{$event->source|escape}}</p>
{{/if}}
							</header>
							<ul class="details">
{{foreach from=$event.divisions item=d}}
{{if ! $d->is_refer}}
								<li class="{{$d->li_class}}">
									<span class="result badge font-weight-light">{{$d.result|escape}}</span>
									<a class="{{if $d.is_unfinished}}unfinished{{/if}}"
										href="{{$d->pmodel()->url()|escape}}"
										data-toggle="tooltip"
										title="{{$d->get_path()|escape}}">
{{if $division.id == $d.id}}
										<b>{{$d->get_fullname()|escape}}</b>
{{else}}
										{{$d->get_fullname()|escape}}
{{/if}}
									</a>
								</li>
{{/if}}
{{/foreach}}
							</ul>
						</section>
						<div class="map col-sm-5 mb-4" id="map-{{$event.event_id}}">
							<div class="loading">
								<i class="fa-3x fas fa-spinner fa-spin"></i>
							</div>
						</div>
						<script>
							$(function(){
								var shapes = [];
{{foreach from=$event.divisions item=d}}
{{if $d && $d->pmodel()->geoshape()}}
								shapes.push({
									url: "{{$d->pmodel()->geoshape()}}",
									split: "{{if isset($d.split)}}{{$d.split}}{{/if}}"
								});
{{/if}}
{{/foreach}}
								if (shapes.length) {
									var id = "map-{{$event.event_id}}"
									$("#" + id).show();
									create_map(id, shapes);
								}
							});
						</script>
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

{{/block}}
