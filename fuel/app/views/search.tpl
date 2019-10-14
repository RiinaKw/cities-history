{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="mb-4">
				<h2>Search Result</h2>
				<div>
					query : <b>{{$q}}</b>
				</div>
			</header>
			<div>
{{if $divisions}}
				<ul class="search-result">
{{foreach from=$divisions item=division}}
					<li>
						<h5>
							<a class="{{if $division.is_unfinished}}unfinished{{/if}}" href="{{$division.url_detail}}">
								{{$division->path}}
							</a>
						</h5>
					</li>
{{/foreach}}
				</ul>
{{else}}
				<p>no results.</p>
{{/if}}
			</div>

{{/block}}
