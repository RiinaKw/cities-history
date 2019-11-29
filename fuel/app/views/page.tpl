{{extends file='layout.tpl'}}

{{block name=content}}
		<main id="{{$page->slug|escape}}">
			<div class="main-contents container">
				<div class="inner-container">
					<h2>{{$page->title|escape}}</h2>
{{$content}}
				</div>
			</div>
		</main>

{{/block}}
