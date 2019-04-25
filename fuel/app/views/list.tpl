
			<h2>List</h2>
			<div class="col-md-6 offset-md-3">
				<section class="city-list">
{{foreach from=$divisions item=division}}
					<article>
						<header>
							<h3><a href="{{$division.url_detail}}">{{$division->get_fullname()}}</a></h3>
						</header>
						<section class="cities">
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
						</section>
						<section class="countries">
							<ul>
{{foreach from=$division.countries item=country}}
								<li>
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
										</section>
									</article>
								</li>
{{/foreach}}
							</ul>
						</section>
					</article>
{{/foreach}}
				</section>
			</div>
