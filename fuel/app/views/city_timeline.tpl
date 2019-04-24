
			<header class="clearfix">
				<div class="float-left">
					<h2>{{$path}}</h2>
					<p>{{$path_kana}}</p>
				</div>
				<button class="btn btn-success float-right" data-toggle="modal" data-target="#change-division">自治体変更</button>
			</header>
			<ul>
				<li><a href="{{$url_detail}}">自治体詳細</a></li>
				<li><a href="{{$url_belongto}}">所属自治体</a></li>
			</ul>

			<div class="col-md-6 offset-md-3 pb-3">
				<section class="timeline">
{{foreach name=events from=$events item=event}}
					<article
						class="editable {{if $event->birth}}birth{{/if}} {{if $event->live}}live{{/if}} {{if $event->death}}death{{/if}}"
						data-event-id="{{$event.event_id}}">
						<header class="clearfix">
							<h3 class="float-left">{{$event.type}}</h3>
							<time class="float-right">{{$event.date}}</time>
						</header>
						<ul>
{{foreach from=$event.divisions item=division}}
							<li>
								<a href="{{$division->url_detail}}">
									{{$division.fullname}}, {{$division.division_result}}
								</a>
							</li>
{{/foreach}}
						</ul>
					</article>
{{foreachelse}}
					<p>no events</p>
{{/foreach}}
					<span class="add"><i class="fas fa-plus"></i> 追加</span>
				</section>
			</div>

			<div id="change-division" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<form class="modal-content" action="{{$url_edit}}" method="post">
						<div class="modal-header">
							<h5 class="modal-title">自治体を変更……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<label class="row">
								<span class="col-4">所属自治体</span>
								<div class="col-8">
									<input class="form-control" type="text" name="parent" value="{{$division->get_parent_path()}}">
								</div>
							</label>
							<label class="row">
								<span class="col-4">自治体名</span>
								<div class="col-8">
									<input class="form-control" type="text" name="name" value="{{$division->name}}">
								</div>
							</label>
							<label class="row">
								<span class="col-4">自治体名かな</span>
								<div class="col-8">
									<input class="form-control" type="text" name="name_kana" value="{{$division->name_kana}}">
								</div>
							</label>
							<label class="row">
								<span class="col-4">接尾語</span>
								<div class="col-8">
									<input class="form-control" type="text" name="postfix" value="{{$division->postfix}}">
								</div>
							</label>
							<label class="row">
								<span class="col-4">接尾語かな</span>
								<div class="col-8">
									<input class="form-control" type="text" name="postfix_kana" value="{{$division->postfix_kana}}">
								</div>
							</label>
							<label class="row">
								<span class="col-4">識別名</span>
								<div class="col-8">
									<input class="form-control" type="text" name="identify" value="{{$division->identify}}">
								</div>
							</label>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
								<button type="submit" class="btn btn-primary">変更を保存</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div id="change-event" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<form class="modal-content" action="#" method="post">
						<div class="modal-header">
							<h5 class="modal-title">イベントを変更……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<input type="hidden" id="path" name="path" value="" />
							<input type="hidden" id="event-id" value="" />
							<label class="row">
								<span class="col-4">イベント種別</span>
								<div class="col-8">
									<input class="form-control" type="text" id="type" name="type">
								</div>
							</label>
							<label class="row">
								<span class="col-4">日付</span>
								<div class="col-8">
									<input class="form-control" type="text" id="date" name="date">
								</div>
							</label>
							<table class="table table-sm table-borderless">
								<thead>
									<tr>
										<th scope="col" style="width: 50%;">自治体</th>
										<th scope="col" style="width: 20%;">結果</th>
										<th scope="col" style="width: 10%;">新設</th>
										<th scope="col" style="width: 10%;">廃止</th>
										<th scope="col" style="width: 10%;">削除</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<span class="row_add"><i class="fas fa-plus"></i> 追加</span>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
								<button type="submit" class="btn btn-primary">変更を保存</button>
								<button type="submit" class="btn btn-danger">削除</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>

$(document).on("click", ".add", function(){
	var $modal = $('#change-event').modal();
	$(".modal-title", $modal).text("イベントを追加…");
	$("form", $modal).attr("action", "{{$url_event_add}}");
	$("#event-id", $modal).val("");
	$("#path", $modal).val("");
	$("#type", $modal).val("");
	$("#date", $modal).val("");
	$("tbody", $modal).empty();
	$(".btn-danger", $modal).hide();
	var path = $("h2").html();
	$("#path", $modal).val(path);
});

$(document).on("dblclick", ".editable", function(){
	var $modal = $('#change-event').modal();
	$(".modal-title", $modal).text("イベントを変更…");
	var event_id = $(this).data("event-id");
	var path = $("h2").html();
	var type = $("h3", $(this)).html();
	var date = $("time", $(this)).html();
	var url = "{{$url_event_edit}}".replace(":id", event_id);
	$("form", $modal).attr("action", url);
	$("#event-id", $modal).val(event_id);
	$("#path", $modal).val(path);
	$("#type", $modal).val(type);
	$("#date", $modal).val(date);
	$(".btn-danger", $modal).show();

	$.ajax({
		type: "get",
		url: "{{$url_event_detail}}".replace(":id", event_id),
	})
	.done(function(data, message, xhr){
		var $tbody = $("tbody", $modal).empty();
		for (idx in data) {
			var detail = data[idx];
			var $tr = $("<tr />").appendTo($tbody);

			var $input_id = $('<input type="hidden">').appendTo($tr);
			$input_id.attr("value", detail.id).attr("name", "id["+idx+"]");

			var $td_division = $("<td />").appendTo($tr);
			var $input_division = $('<input type="text">').addClass("form-control");
			$input_division.attr("value", detail.path).attr("name", "division["+idx+"]");
			$input_division.appendTo($td_division);

			$input_division.devbridgeAutocomplete({
				serviceUrl: "{{$root}}/division/list.json"
			});

			var $td_result = $("<td />").appendTo($tr);
			var $input_result = $('<input type="text">').addClass("form-control");
			$input_result.attr("value", detail.result).attr("name", "result["+idx+"]");
			$input_result.appendTo($td_result);

			var $td_birth = $("<td />").appendTo($tr);
			var $input_birth = $('<input type="checkbox">');
			$input_birth.attr("value", "true").attr("name", "birth["+idx+"]");
			$input_birth.appendTo($td_birth);
			if (detail.birth) {
				$input_birth.prop("checked", true);
			}

			var $td_death = $("<td />").appendTo($tr);
			var $input_death = $('<input type="checkbox">');
			$input_death.attr("value", "true").attr("name", "death["+idx+"]");
			$input_death.appendTo($td_death);
			if (detail.death) {
				$input_death.prop("checked", true);
			}

			var $td_delete = $("<td />").appendTo($tr);
			var $input_delete = $('<input type="checkbox">');
			$input_delete.attr("value", "true").attr("name", "delete["+idx+"]");
			$input_delete.appendTo($td_delete);
		}
	});
});

$(document).on("click", "#change-event .row_add", function() {
	var $modal = $('#change-event');
	var $tbody = $("tbody", $modal);

	var idx = $("tbody tr", $modal).length;

	var $tr = $("<tr />").appendTo($tbody);

	var $input_id = $('<input type="hidden">').appendTo($tr);
	$input_id.attr("value", "new").attr("name", "id["+idx+"]");

	var $td_division = $("<td />").appendTo($tr);
	var $input_division = $('<input type="text">');
	$input_division.addClass("form-control").attr("name", "division["+idx+"]");
	$input_division.appendTo($td_division);

	$input_division.devbridgeAutocomplete({
		serviceUrl: "{{$root}}/division/list.json"
	});

	var $td_result = $("<td />").appendTo($tr);
	var $input_result = $('<input type="text">');
	$input_result.addClass("form-control").attr("name", "result["+idx+"]");
	$input_result.appendTo($td_result);

	var $td_birth = $("<td />").appendTo($tr);
	var $input_birth = $('<input type="checkbox">');
	$input_birth.attr("value", "true").attr("name", "birth["+idx+"]");
	$input_birth.appendTo($td_birth);

	var $td_death = $("<td />").appendTo($tr);
	var $input_death = $('<input type="checkbox">');
	$input_death.attr("value", "true").attr("name", "death["+idx+"]");
	$input_death.appendTo($td_death);

	var $td_delete = $("<td />").appendTo($tr);
	var $input_delete = $('<input type="checkbox">');
	$input_delete.attr("value", "true").attr("name", "delete["+idx+"]");
	$input_delete.appendTo($td_delete);
});

$(document).on("click", "#change-event .btn-danger", function(){
	var $modal = $('#change-event');
	var event_id = $("#event-id", $modal).val();
	var url = "{{$url_event_delete}}".replace(":id", event_id);
	$("form", $modal).attr("action", url);
});
			</script>
