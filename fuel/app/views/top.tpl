{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$title}}</h2>
				</div>
{{if $user}}
				<nav class="float-right">
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-division">
						<i class="fa fa-plus"></i>
						自治体追加
					</button>
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-divisions-csv">
						<i class="fa fa-table"></i>
						一括追加
					</button>
				</nav>
{{/if}}
			</header>

			<section class="toplevel-list">
				<ol class="row">
{{foreach from=$divisions item=division}}
{{assign var=url value=\Helper_Division::url($division)}}
					<li class="col-6 col-sm-4 col-md-3 col-lg-2">
						<a class="{{if $division.is_unfinished}}unfinished{{/if}}" href="{{$url}}">
							{{$division->get_fullname()}}
						</a>
					</li>
{{/foreach}}
				</ol>
			</section>

{{if $user}}

{{$components.add_division}}
{{$components.add_divisions_csv}}

{{/if}}

{{/block}}
