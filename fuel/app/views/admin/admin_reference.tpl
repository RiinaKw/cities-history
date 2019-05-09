
			<header class="clearfix">
				<div class="float-left">
					<h2>Reference Manage</h2>
				</div>
				<nav class="float-right">
					<button class="btn btn-success mb-1" data-toggle="modal" data-target="#add-reference">
						<i class="fa fa-plus"></i>
						参照を追加
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
								<th scope="col">Date</th>
								<th scope="col">Description</th>
							</tr>
						</thead>
						<tbody>
{{foreach from=$dates item=date}}
							<tr data-id="{{$date.id|escape}}" data-date="{{$date.date|date_format2:'Y-m-d'}}">
								<td>{{$date.date|date_format2:'Y(Jk)-m-d'}}</td>
								<td class="description">{{$date.description|escape}}</td>
							</tr>
{{/foreach}}
						</tbody>
					</table>
				</section>
			</div>

{{$components.add_reference}}
{{$components.edit_reference}}
{{$components.delete_reference}}
