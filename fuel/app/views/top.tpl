
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
				</nav>
{{/if}}
			</header>

			<section class="toplevel-list">
				<ol class="row">
{{foreach from=$divisions item=division}}
					<li class="col-6 col-sm-4 col-md-3 col-lg-2">
						<a class="{{if $division.is_unfinished}}unfinished{{/if}}" href="{{$division.url_detail}}">
							{{$division->get_fullname()}}
						</a>
					</li>
{{/foreach}}
				</ol>
			</section>

{{if $user}}

{{$components.add_division}}

{{/if}}
