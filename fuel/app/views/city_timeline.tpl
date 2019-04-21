
			<h2>{{$path}}</h2>
			<ul>
				<li><a href="{{$url_detail}}">自治体詳細</a></li>
				<li><a href="{{$url_belongto}}">所属自治体</a></li>
			</ul>

			<div class="col-md-6 offset-md-3">
				<section class="timeline">
{{foreach name=events from=$events item=event}}
					<article
						class="editable {{if $event->birth}}birth{{/if}} {{if $event->live}}live{{/if}} {{if $event->death}}death{{/if}}"
						data-event-id="{{$event.event_id}}">
						<header class="clearfix">
							<h3 class="float-left">{{$event.division_result}}</h3>
							<time class="float-right">{{$event.date}}</time>
						</header>
						<ul>
{{foreach from=$event.divisions item=division}}
							<li>
								<a href="{{$division->url_detail}}">
									{{$division.name}}, {{$division.division_result}}
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

			<div id="change-event" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
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
							<p>モーダルボディの本文。</p>
							<div class="row">
								<label class="col-4">イベント種別</label>
								<div class="col-8">
									<input class="form-control" type="text" id="type" name="type">
								</div>
							</div>
							<div class="row">
								<label class="col-4">日付</label>
								<div class="col-8">
									<input class="form-control" type="text" id="date" name="date">
								</div>
							</div>
							<table class="table table-sm table-borderless">
								<thead>
									<tr>
										<th scope="col" style="width: 70%;">自治体</th>
										<th scope="col" style="width: 20%;">結果</th>
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
			var $td_result = $("<td />").appendTo($tr);
			var $input_result = $('<input type="text">').addClass("form-control");
			$input_result.attr("value", detail.result).attr("name", "result["+idx+"]");
			$input_result.appendTo($td_result);
			var $td_delete = $("<td />").appendTo($tr);
			var $input_delete = $('<input type="checkbox">');
			$input_delete.attr("value", "true").attr("name", "delete["+idx+"]");
			$input_delete.appendTo($td_delete);
		}
	});
});

$(document).on("click", "#change-event .row_add", function(){
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
	var $td_result = $("<td />").appendTo($tr);
	var $input_result = $('<input type="text">');
	$input_result.addClass("form-control").attr("name", "result["+idx+"]");
	$input_result.appendTo($td_result);
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
