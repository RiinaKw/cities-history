{{extends file='layout.tpl'}}

{{block name=content}}
{{assign var=pmodel value=$division->pmodel()}}
{{assign var=getter value=$division->getter()}}
			<header class="clearfix">
				<div class="float-left">
					<h2>
						{{$getter->path|escape}}
{{if $division->belongs}}
						{{strip}}
							（
								{{$division->belongs->pmodel()->htmlAnchor()}}
							）
						{{/strip}}
{{/if}}
					</h2>
					<p>{{$getter->path_kana}}</p>
{{if $division->government_code}}
					<p >全国地方公共団体コード : {{$division->government_code|escape}}</p>
{{/if}}
{{if $division->source}}
					<p class="source">出典 :<br />{{$pmodel->source()}}</p>
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
					<li class="{{if $current === 'detail'}}active{{/if}}">
						<a href="{{$pmodel->url()}}">自治体タイムライン</a>
					</li>
					<li class="">
						<a href="{{$pmodel->urlTree()}}">所属自治体</a>
					</li>
					<li>
						所属自治体タイムライン
						<ul>
{{foreach from=$pmodel->urlListChildren() key=label item=url}}
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
						class="row editable {{strip}}
							{{if $event->birth}}birth{{/if}}
							{{if $event->live}}live{{/if}}
							{{if $event->death}}death{{/if}}
						{{/strip}}"
						data-event-id="{{$event->id}}">
						<section class="col-sm-7">
							<header>
								<div class="clearfix">
									<h3 class="float-left">{{$event->title|escape}}</h3>
									<time class="float-right" datetime="{{$event->date|escape}}">
										{{$event.date|date_format2:'Y(Jk)-m-d'}}
									</time>
								</div>
{{if $event->comment}}
								<p class="comment">{{$event->comment|escape}}</p>
{{/if}}
{{if $event->source}}
								<p class="source">出典 :<br />{{$event->pmodel()->source()}}</p>
								<p class="source_preformat">{{$event->source|escape}}</p>
{{/if}}
							</header>
							<ul class="details">
{{foreach from=$event->event_details item=detail}}
{{assign var=div value=$detail->division}}
{{if ! $detail->is_refer}}
								<li class="{{$detail->pmodel()->htmlClass()}}">
									<span class="result badge font-weight-light">{{$detail->result|escape}}</span>
{{if $division->id === $div->id}}
									<b>{{$div->pmodel()->htmlAnchor()}}</b>
{{else}}
									{{$div->pmodel()->htmlAnchor()}}
{{/if}}
								</li>
{{/if}}
{{/foreach}}
							</ul>
						</section>
						<div class="map col-sm-5 mb-4" id="map-{{$event->id}}">
							<div class="loading">
								<i class="fa-3x fas fa-spinner fa-spin"></i>
							</div>
						</div>
						<script>
							$(function(){
								var shapes = [];
{{foreach from=$event->event_details item=detail}}
{{if $detail->pmodel()->geoshape()}}
								shapes.push({
									name: "{{$detail->division->fullname}}",
									url: "{{$detail->pmodel()->geoshape()}}",
									split: {{if $detail->pmodel()->isSplit()}}true{{else}}false{{/if}}
								});
{{/if}}
{{/foreach}}
								if (shapes.length) {
									var id = "map-{{$event->id}}"
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
