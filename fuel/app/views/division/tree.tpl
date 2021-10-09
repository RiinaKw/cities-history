{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$parent->path|escape}}</h2>
					<p>{{$parent->getter()->path_kana}}</p>
{{if $parent->government_code}}
					<p>全国地方公共団体コード : {{$parent->government_code|escape}}</p>
{{/if}}
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

			<nav class="list-nav">
				<ol>
					<li class="{{if ! $date}}active{{/if}}">
						<a href="{{$url_all}}">
							すべて
						</a>
					</li>
{{foreach from=$reference_dates item=cur_date}}
					<li class="{{if $year == $cur_date.year && $month == $cur_date.month && $day == $cur_date.day}}active{{/if}}">
						<a href="{{$cur_date.url}}">
							{{$cur_date.date|date_format2:'Y(Jk)-m-d'}} - {{$cur_date.description|escape}}
						</a>
					</li>
{{/foreach}}
					<li>
						<form id="select-date" class="form-inline my-2">
							<span class="mr-2">任意の日付 :</span>
							<div class="input-group input-group-sm mr-2">
								<select class="form-control" id="year" name="year">
{{foreach from=$year_list item=cur_year}}
									<option value="{{$cur_year}}" {{if $year == $cur_year}}selected="selected"{{/if}}>{{$cur_year}}</option>
{{/foreach}}
								</select>
								<label class="input-group-append" for="year">
									<i class="input-group-text">年</i>
								</label>
							</div>
							<div class="input-group input-group-sm mr-2">
								<select class="form-control" id="month" name="month">
{{foreach from=$month_list item=cur_month}}
									<option value="{{$cur_month}}" {{if $month == $cur_month}}selected="selected"{{/if}}>{{$cur_month}}</option>
{{/foreach}}
								</select>
								<label class="input-group-append" for="month">
									<i class="input-group-text">月</i>
								</label>
							</div>
							<div class="input-group input-group-sm mr-2">
								<select class="form-control" id="day" name="day">
{{foreach from=$day_list item=cur_day}}
									<option value="{{$cur_day}}" {{if $day == $cur_day}}selected="selected"{{/if}}>{{$cur_day}}</option>
{{/foreach}}
								</select>
								<label class="input-group-append" for="day">
									<i class="input-group-text">日</i>
								</label>
							</div>
							<button class="btn btn-sm btn-success">表示</button>
						</form>
					</li>
				</ol>
			</nav>

			<section>
				<h3>
					{{$parent->pmodel()->htmlAnchor()}}
					{{$parent->pmodel()->htmlDebugCode()}}
				</h3>
				<p class="count">{{$tree->pmodel()->suffiexes()}}</p>

				<div class="grid-container">

{{if $tree->get_by_suffix('支庁') }}
					<section class="grid departs">
						{{$tree->get_by_suffix('支庁')|tree_body:6:tab}}
					</section><!-- .grid.departs -->
{{/if}}

{{if $tree->get_by_suffix('区') }}
					<section class="grid wards">
						{{$tree->get_by_suffix('区')|tree_body:6:tab}}
					</section><!-- .grid.wards -->
{{/if}}

{{if $tree->get_by_suffix('市') }}
					<section class="grid city">
						<ul class="divisions">
{{foreach name=city from=$tree->get_by_suffix('市') item=subtree}}
{{assign var=isSuper value=(bool)$subtree->get_by_suffix('区')}}

{{if $isSuper}}{{* 政令指定都市の場合 *}}
							<li>
								<article>
									{{$subtree|tree_header:9:tab}}
									{{$subtree->get_by_suffix('区')|tree_body:9:tab}}
								</article>
							</li>
{{else}}
							<li>
								<article>
									{{$subtree|tree_header:9:tab}}
								</article>
							</li>
{{/if}}
{{if ! $smarty.foreach.city.last && $isSuper}}
						</ul>
					</section><!-- .grid.cities -->
					<section class="grid cities">
						<ul class="divisions">
{{/if}}
{{/foreach}}
						</ul>
					</section><!-- .grid.cities -->
{{/if}}

{{if $tree->get_by_suffix('町村') }}
					<section class="grid towns">
						{{$tree->get_by_suffix('町村')|tree_body:6:tab}}
					</section><!-- .grid.towns -->
{{/if}}

{{if $tree->get_by_suffix('郡') }}
{{foreach name=city from=$tree->get_by_suffix('郡') item=subtree}}
					<section class="grid countries">
						<article>
							{{$subtree|tree_header:7:tab}}
							{{$subtree->get_by_suffix('町村')|tree_body:7:tab}}
						</article>
					</section><!-- .grid.countries -->
{{/foreach}}
{{/if}}

				</div><!-- .grid-container -->
			</section>

{{if $user}}

{{$components.add_division}}

{{/if}}

{{/block}}
