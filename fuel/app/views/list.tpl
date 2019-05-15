
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
				</nav>
{{/if}}
			</header>

			<nav>
				<ol>
					<li>
						<a href="{{$url_all}}">
							すべて
						</a>
					</li>
{{foreach from=$reference_dates item=date}}
					<li>
						<a href="{{$date.url|escape}}">
							{{$date.date|date_format2:'Y(Jk)-m-d'}} - {{$date.description|escape}}
						</a>
					</li>
{{/foreach}}
				</ol>
			</nav>

			<div>
				<section class="pref-list">
{{foreach from=$divisions item=division}}
					<article class="">
						<header>
							<h3><a href="{{$division.url_detail}}">{{$division->get_fullname()}}</a></h3>
							<p class="count">{{strip}}
								{{foreach from=$count[$division->id] key=postfix item=count}}
									{{if $count}}
										{{$count}}{{$postfix}}
									{{/if}}
								{{/foreach}}
							{{/strip}}</p>
						</header>
						<div class="grid-container">
{{if $division.belongto['区']}}
							<section class="grid wards">
								<ul>
{{foreach from=$division.belongto['区'] item=ward}}
									<li>
										<article>
											<header>
												<h4><a href="{{$ward.url_detail}}">{{$ward->get_fullname()}}</a></h4>
											</header>
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.wards -->
{{/if}}
{{if $division.belongto['市']}}
							<section class="grid cities">
								<ul>
{{foreach from=$division.belongto['市'] item=city}}
									<li>
										<article>
											<header>
												<h4><a href="{{$city.url_detail}}">{{$city->get_fullname()}}</a></h4>
											</header>
{{if isset($city->wards)}}
											<p class="count">{{strip}}
												{{$city->wards_count}}区
											{{/strip}}</p>
											<ul>
{{foreach from=$city->wards item=ward}}
												<li>
													<article>
														<header>
															<h5><a href="{{$ward.url_detail}}">{{$ward->get_fullname()}}</a></h5>
														</header>
													</article>
												</li>
{{/foreach}}
											</ul>
{{/if}}
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.cities -->
{{/if}}
{{if $division.belongto['町村']}}
	<section class="grid countries">
		<ul>
{{foreach from=$division.belongto['町村'] item=town}}
			<li>
				<article>
					<header>
						<h4><a href="{{$town.url_detail}}">{{$town->get_fullname()}}</a></h4>
					</header>
				</article>
			</li>
{{/foreach}}
		</ul>
	</section><!-- .grid.wards -->
{{/if}}
{{foreach from=$division.belongto['郡'] item=country}}
							<section class="grid countries">
								<article>
									<header>
										<h4><a href="{{$country.url_detail}}">{{$country->get_fullname()}}</a></h4>
										<p class="count">{{strip}}
											{{foreach from=$count[$country->id] key=postfix item=count}}
												{{if $count}}
													{{$count}}{{$postfix}}
												{{/if}}
											{{/foreach}}
										{{/strip}}</p>
									</header>
									<section class="towns">
										<ul>
{{foreach from=$country.towns item=town}}
											<li>
												<article>
													<header>
														<h5><a href="{{$town.url_detail}}">{{$town->get_fullname()}}</a></h5>
													</header>
												</article>
											</li>
{{/foreach}}
										</ul>
									</section><!-- .towns -->
								</article>
							</section><!-- .grid -->
{{/foreach}}
						</div><!-- .grid-container -->
					</article>
{{/foreach}}
				</section>
			</div>

{{if $user}}

{{$components.add_division}}

{{/if}}
