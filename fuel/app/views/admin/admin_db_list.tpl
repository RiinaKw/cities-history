{{extends file='layout.tpl'}}

{{block name=content}}
			<header class="clearfix">
				<div class="float-left">
					<h2>DB Manage</h2>
				</div>
				<nav class="float-right">
					<button class="btn btn-success mb-1"
							data-toggle="modal"
							data-target="#add-backup">
						<i class="fa fa-download"></i>
						バックアップを取得...
					</button>
				</nav>
			</header>

{{if $flash}}
{{if $flash.status == 'success'}}
				<div class="alert alert-success" role="alert">
					<strong>Success!</strong>
					{{$flash.message}}
				</div>
{{elseif $flash.status == 'error'}}
				<div class="alert alert-danger" role="alert">
					<strong>Error!</strong>
					{{$flash.message}}
				</div>
{{/if}}
{{/if}}

			<div>
				<section>
					<table class="table table-hover">
						<thead class="thead-light">
							<tr>
								<th scope="col">File name</th>
								<th scope="col">Size</th>
								<th scope="col">Created</th>
							</tr>
						</thead>
						<tbody>
{{foreach from=$files item=file}}
							<tr data-file={{$file->name|escape}}>
								<td>{{$file->name|escape}}</td>
								<td>{{$file->size_formatted|escape}}</td>
								<td>{{$file->created_at|date_format:'%Y-%m-%d %H:%M:%S'}}</td>
							</tr>
{{foreachelse}}
							<tr>
								<td colspan="3">
									<div class="text-center">バックアップがありません。</div>
								</td>
							</tr>
{{/foreach}}
						</tbody>
					</table>
				</section>
			</div>


{{$components.detail}}
{{$components.backup}}
{{$components.restore}}
{{$components.delete}}

{{/block}}
