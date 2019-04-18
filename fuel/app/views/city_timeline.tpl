
			<h2>{{$path}}</h2>
			<div class="col-md-6 offset-md-3">
				<div class="timeline">
{{foreach from=$events item=event}}
					<article>
						<header>
							{{$event.type}}
							<time class="float-right">{{$event.date|date_format:'Y-m-d'}}</time>
						</header>
						{{$event|var_dump}}
					</article>
{{/foreach}}
				</div>
			</div>
