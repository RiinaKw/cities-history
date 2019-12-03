{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>Pages Manage</h2>
				</div>
				<nav class="float-right">
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-reference">
						<i class="fa fa-plus"></i>
						ページを追加
					</button>
				</nav>
			</header>

{{if $flash}}
{{if $flash.status == 'success'}}
				<div class="alert alert-success" role="alert">
					<strong>Success!</strong>
					{{$flash.message}}
				</div>
{{/if}}
{{/if}}

			<div>
				<section>
					<table class="table table-hover">
						<thead class="thead-light">
							<tr>
								<th scope="col">Title</th>
								<th scope="col">Updated</th>
							</tr>
						</thead>
						<tbody>
{{foreach from=$pages item=page}}
							<tr>
								<td>{{$page.title|escape}}</td>
								<td>{{$page.updated_at|escape}}</td>
							</tr>
{{foreachelse}}
							<tr>
								<td colspan="2">
									<div class="text-center">固定ページがありません。</div>
								</td>
							</tr>
{{/foreach}}
						</tbody>
					</table>
				</section>
			</div>

{{/block}}
