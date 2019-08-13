
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

			<nav class="list-nav">
				<ol>
					<li class="{{if ! $date}}active{{/if}}">
						<a href="{{$url_all}}">
							すべて
						</a>
					</li>
{{foreach from=$reference_dates item=cur_date}}
					<li class="{{if $date == $cur_date.date}}active{{/if}}">
						<a href="{{$cur_date.url|escape}}">
							{{$cur_date.date|date_format2:'Y(Jk)-m-d'}} - {{$cur_date.description|escape}}
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
							<h3>
								<a class="{{if $division.is_unfinished}}unfinished{{/if}}" href="{{$division.url_detail}}">
									{{$division->get_fullname()}}
								</a>
							</h3>
							<p class="count">{{strip}}
								{{foreach from=$count[$division->id] key=postfix item=count}}
									{{if $count}}
										{{$count}}{{$postfix}}
									{{/if}}
								{{/foreach}}
							{{/strip}}</p>
						</header>
						<div class="grid-container">
{{if $division.children['支庁']}}
							<section class="grid departs">
								<ul>
{{foreach from=$division.children['支庁'] item=depart}}
									<li>
										<article>
											<header>
												<h4>
													<a class="{{if $depart.is_unfinished}}unfinished{{/if}}" href="{{$depart.url_detail}}">
														{{$depart->get_fullname()}}
													</a>
												</h4>
												<p class="count">{{strip}}
													{{if isset($count[$depart->id])}}
														{{foreach from=$count[$depart->id] key=postfix item=count}}
															{{if $count}}
																{{$count}}{{$postfix}}
															{{/if}}
														{{/foreach}}
													{{/if}}
												{{/strip}}</p>
											</header>
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.departs -->
{{/if}}
{{if $division.children['区']}}
							<section class="grid wards">
								<ul>
{{foreach from=$division.children['区'] item=ward}}
									<li>
										<article>
											<header>
												<h4>
													<a class="{{if $ward.is_unfinished}}unfinished{{/if}}" href="{{$ward.url_detail}}">
														{{$ward->get_fullname()}}
													</a>
												</h4>
											</header>
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.wards -->
{{/if}}
{{if $division.children['市']}}
							<section class="grid cities">
								<ul>
{{foreach from=$division.children['市'] item=city}}
									<li>
										<article>
											<header>
												<h4>
													<a class="{{if $city.is_unfinished}}unfinished{{/if}}" href="{{$city.url_detail}}">
														{{$city->get_fullname()}}
													</a>
												</h4>
{{if isset($city->wards)}}
												<p class="count">{{strip}}
													{{$city->wards_count}}区
												{{/strip}}</p>
{{/if}}
											</header>
{{if isset($city->wards)}}
											<ul>
{{foreach from=$city->wards item=ward}}
												<li>
													<article>
														<header>
															<h5>
																<a class="{{if $ward.is_unfinished}}unfinished{{/if}}" href="{{$ward.url_detail}}">
																	{{$ward->get_fullname()}}
																</a>
															</h5>
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
{{if $division.children['町村']}}
							<section class="grid countries">
								<ul>
{{foreach from=$division.children['町村'] item=town}}
									<li>
										<article>
											<header>
												<h4>
													<a class="{{if $town.is_unfinished}}unfinished{{/if}}" href="{{$town.url_detail}}">
														{{$town->get_fullname()}}
													</a>
												</h4>
											</header>
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.wards -->
{{/if}}
{{foreach from=$division.children['郡'] item=country}}
							<section class="grid countries">
								<article>
									<header>
										<h4>
											<a class="{{if $country.is_unfinished}}unfinished{{/if}}" href="{{$country.url_detail}}">
												{{$country->get_fullname()}}
											</a>
										</h4>
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
														<h5>
															<a class="{{if $town.is_unfinished}}unfinished{{/if}}" href="{{$town.url_detail}}">
																{{$town->get_fullname()}}
															</a>
														</h5>
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
