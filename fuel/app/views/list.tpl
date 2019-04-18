
			<h2>List</h2>
			<div class="col-md-6 offset-md-3">
				<section class="city-list">
{{foreach from=$divisions item=division}}
					<article>
						<header>
							<h3><a href="{{$division.path}}">{{$division.name}}</a></h3>
						</header>
					</article>
{{/foreach}}
				</section>
			</div>
