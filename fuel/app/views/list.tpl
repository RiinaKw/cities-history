{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$division->path|escape}}</h2>
					<p>{{$path_kana}}</p>
{{if $division->government_code}}
					<p>全国地方公共団体コード : {{$division->government_code|escape}}</p>
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
						<a href="{{$cur_date.url|escape}}">
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
					{{$division->pmodel()->htmlAnchor()}}
					{{$division->pmodel()->htmlDebugCode()}}
				</h3>
				<p class="count">{{strip}}
					{{foreach from=$tree->suffix_count() key=suffix item=cur_count}}
						{{if $cur_count}}
							{{$cur_count}}{{$suffix}}
						{{/if}}
					{{/foreach}}
				{{/strip}}</p>

				<div class="grid-container">

{{if $tree->get_by_suffix('支庁') }}
					<section class="grid departs">
						<ul class="divisions">
{{foreach from=$tree->get_by_suffix('支庁') item=subtree}}
{{assign var=division value=$subtree->self()}}
							<li>
								<article>
									<header>
										<h4>
											{{$division->pmodel()->htmlAnchor()}}
											{{$division->pmodel()->htmlDebugCode()}}
										</h4>
									</header>
								</article>
							</li>
{{/foreach}}
						</ul>
					</section><!-- .grid.departs -->
{{/if}}

{{if $tree->get_by_suffix('区') }}
					<section class="grid wards">
						<ul class="divisions">
{{foreach from=$tree->get_by_suffix('区') item=subtree}}
{{assign var=division value=$subtree->self()}}
							<li>
								<article>
									<header>
										<h4>
											{{$division->pmodel()->htmlAnchor()}}
											{{$division->pmodel()->htmlDebugCode()}}
											{{$division->pmodel()->htmlBelongs()}}
										</h4>
									</header>
								</article>
							</li>
{{/foreach}}
						</ul>
					</section><!-- .grid.wards -->
{{/if}}

{{if $tree->get_by_suffix('市') }}
					<section class="grid city">
						<ul class="divisions">
{{foreach name=city from=$tree->get_by_suffix('市') item=subtree}}
{{assign var=division value=$subtree->self()}}
							<li>
								<article>
									<header>
										<h4>
											{{$division->pmodel()->htmlAnchor()}}
											{{$division->pmodel()->htmlDebugCode()}}
											{{$division->pmodel()->htmlBelongs()}}
										</h4>
{{if $subtree->suffix_count('区')}}
										<p class="count">{{strip}}
											{{foreach from=$subtree->suffix_count() key=suffix item=cur_count}}
												{{if $cur_count}}
													{{$cur_count}}{{$suffix}}
												{{/if}}
											{{/foreach}}
										{{/strip}}</p>
{{/if}}
									</header>
{{if $subtree->get_by_suffix('区')}}
									<ul class="divisions">
{{foreach from=$subtree->get_by_suffix('区') item=wards}}
{{assign var=division value=$wards->self()}}
										<li>
											<article>
												<header>
													<h4>
														{{$division->pmodel()->htmlAnchor()}}
														{{$division->pmodel()->htmlDebugCode()}}
														{{$division->pmodel()->htmlBelongs()}}
													</h4>
												</header>
											</article>
										</li>
{{/foreach}}
									</ul>
{{/if}}
								</article>
							</li>
{{if ! $smarty.foreach.city.last && $subtree->get_by_suffix('区')}}
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
						<ul class="divisions">
{{foreach from=$tree->get_by_suffix('町村') item=subtree}}
{{assign var=division value=$subtree->self()}}
							<li>
								<article>
									<header>
										<h4>
											{{$division->pmodel()->htmlAnchor()}}
											{{$division->pmodel()->htmlDebugCode()}}
											{{$division->pmodel()->htmlBelongs()}}
										</h4>
									</header>
								</article>
							</li>
{{/foreach}}
						</ul>
					</section><!-- .grid.towns -->
{{/if}}

{{if $tree->get_by_suffix('郡') }}
{{foreach name=city from=$tree->get_by_suffix('郡') item=subtree}}
{{assign var=division value=$subtree->self()}}
					<section class="grid countries">
						<article>
							<header>
								<h4>
									{{$division->pmodel()->htmlAnchor()}}
									{{$division->pmodel()->htmlDebugCode()}}
									{{$division->pmodel()->htmlBelongs()}}
								</h4>
								<p class="count">{{strip}}
									{{foreach from=$subtree->suffix_count() key=suffix item=cur_count}}
										{{if $cur_count}}
											{{$cur_count}}{{$suffix}}
										{{/if}}
									{{/foreach}}
								{{/strip}}</p>
							</header>
{{if $subtree->get_by_suffix('町村')}}
							<ul class="divisions">
{{foreach from=$subtree->get_by_suffix('町村') item=wards}}
{{assign var=division value=$wards->self()}}
								<li>
									<article>
										<header>
											<h5>
												{{$division->pmodel()->htmlAnchor()}}
												{{$division->pmodel()->htmlDebugCode()}}
												{{$division->pmodel()->htmlBelongs()}}
											</h5>
										</header>
									</article>
								</li>
{{/foreach}}
							</ul>
{{/if}}
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
