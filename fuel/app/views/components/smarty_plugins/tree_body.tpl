<ul class="divisions">
{{foreach from=$iterator item=subtree}}
	<li>
		<article>
			{{$subtree|tree_header:3:$indentType}}
		</article>
	</li>
{{/foreach}}
</ul>
