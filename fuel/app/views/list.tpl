
			<header class="clearfix">
				<div class="float-left">
					<h2>List</h2>
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
						<a href="{{$url_all_list}}">
							すべて
						</a>
					</li>
					<li>
						<a href="{{$meiji_after.url|escape}}">
							明治の大合併直後 - {{$meiji_after.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
					<li>
						<a href="{{$showa_before.url|escape}}">
							昭和の大合併直前 - {{$showa_before.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
					<li>
						<a href="{{$showa_after.url|escape}}">
							昭和の大合併直後 - {{$showa_after.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
					<li>
						<a href="{{$heisei_before.url|escape}}">
							平成の大合併直前 - {{$heisei_before.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
					<li>
						<a href="{{$heisei_after.url|escape}}">
							平成の大合併直後 - {{$heisei_after.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
					<li>
						<a href="{{$now.url|escape}}">
							現在 - {{$now.date|date_format2:'Y(Jk)-m-d'}}
						</a>
					</li>
				</ol>
			</nav>

			<div>
				<section class="pref-list">
{{foreach from=$divisions item=division}}
					<article class="">
						<header>
							<h3><a href="{{$division.url_detail}}">{{$division->get_fullname()}}</a></h3>
						</header>
						<div class="grid-container">
{{if $division.cities}}
							<section class="grid cities">
								<ul>
{{foreach from=$division.cities item=city}}
									<li>
										<article>
											<header>
												<h4><a href="{{$city.url_detail}}">{{$city->get_fullname()}}</a></h4>
											</header>
										</article>
									</li>
{{/foreach}}
								</ul>
							</section><!-- .grid.cities -->
{{/if}}
{{foreach from=$division.countries item=country}}
							<section class="grid countries">
								<article>
									<header>
										<h4><a href="{{$country.url_detail}}">{{$country->get_fullname()}}</a></h4>
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
