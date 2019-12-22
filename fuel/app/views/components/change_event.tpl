
			<div id="change-event" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
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
									<input class="form-control" type="text" id="title" name="title" required="required" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">日付</span>
								<div class="col-8">
									<input class="form-control" type="text" id="date" name="date" required="required" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">注釈</span>
								<div class="col-8">
									<textarea class="form-control" type="text" id="comment" name="comment"></textarea>
								</div>
							</label>
							<table class="table table-sm table-borderless">
								<thead>
									<tr>
										<th class="text-center" scope="col" style="width: 3%;"></th>
										<th scope="col" style="width: 50%;">自治体</th>
										<th scope="col" style="width: 18%;">結果</th>
										<th class="text-center" scope="col" style="width: 6%;">新設</th>
										<th class="text-center" scope="col" style="width: 6%;">廃止 /<br />存続</th>
										<th class="text-center" scope="col" style="width: 6%;">参照</th>
										<th class="text-center" scope="col" style="width: 6%;">削除</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
							<span class="row_add"><i class="fas fa-plus"></i> 追加</span>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">
									<i class="fa fa-times"></i>
									閉じる
								</button>
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-edit"></i>
									保存
								</button>
								<button type="submit" class="btn btn-danger">
									<i class="fa fa-trash"></i>
									削除
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>
function add_row($tbody, idx, detail)
{
	var $tr = $("<tr />").addClass("ui-state-default").appendTo($tbody);

	var $input_id = $('<input type="hidden" />').appendTo($tr);
	$input_id.attr("name", "id["+idx+"]");
	if (detail.id) {
		$input_id.val(detail.id);
	} else {
		$input_id.val("new");
	}
	var $input_no = $('<input type="hidden" />').addClass("row-no").appendTo($tr);
	$input_no.attr("name", "no["+idx+"]").val(idx);

	var $handle = $("<td />").addClass("handle").appendTo($tr);
	$handle.append('<i class="fa fa-bars"></i>');

	var $td_division = $("<td />").appendTo($tr);
	var $input_division = $('<input type="text" />').addClass("form-control");
	$input_division.attr("name", "division["+idx+"]");
	if (detail.path) {
		$input_division.val(detail.path);
	}
	$input_division.appendTo($td_division);
	var $input_geoshape = $('<input type="text" />').addClass("form-control");
	$input_geoshape.attr("name", "geoshape["+idx+"]");
	if (detail.geoshape) {
		$input_geoshape.val(detail.geoshape);
	}
	$input_geoshape.appendTo($td_division);

	$input_division.devbridgeAutocomplete({
		serviceUrl: "{{$url_root}}/division/list.json"
	});

	var $td_result = $("<td />").appendTo($tr);
	var $input_result = $('<input type="text" />').addClass("form-control");
	$input_result.attr("value", detail.result).attr("name", "result["+idx+"]");
	if (detail.result) {
		$input_result.val(detail.result);
	}
	$input_result.appendTo($td_result);

	var $td_birth = $("<td />").addClass("text-center").addClass("checkbox-wrapper").appendTo($tr);
	var $input_birth = $('<input type="checkbox" />');
	$input_birth.attr("value", "true").attr("name", "birth["+idx+"]");
	$input_birth.appendTo($td_birth);
	if (detail.birth) {
		$input_birth.prop("checked", true);
	}

	var $td_death = $("<td />").addClass("text-center").addClass("checkbox-wrapper").appendTo($tr);
	var $input_death = $('<input type="checkbox" />');
	$input_death.attr("value", "true").attr("name", "death["+idx+"]");
	$input_death.appendTo($td_death);
	if (detail.death) {
		$input_death.prop("checked", true);
	}

	var $td_refer = $("<td />").addClass("text-center").addClass("checkbox-wrapper").appendTo($tr);
	var $input_refer = $('<input type="checkbox" />');
	$input_refer.attr("value", "true").attr("name", "refer["+idx+"]");
	$input_refer.appendTo($td_refer);
	if (detail.is_refer) {
		$input_refer.prop("checked", true);
	}

	var $td_delete = $("<td />").addClass("text-center").addClass("checkbox-wrapper").appendTo($tr);
	var $input_delete = $('<input type="checkbox" />');
	$input_delete.attr("value", "true").attr("name", "delete["+idx+"]");
	$input_delete.appendTo($td_delete);
} // function add_row()

$(function(){

	$("#change-event tbody").sortable({
		stop: function(event, ui) {
			console.log(event);
			$(".row-no").each(function(v){
				$(this).val(v);
			});
		}
	});

	$(document).on("click", ".add", function(){
		var $modal = $('#change-event').modal();
		$(".modal-title", $modal).text("イベントを追加…");
		$("form", $modal).attr("action", "{{$url_event_add}}");
		$("#event-id", $modal).val("");
		$("#path", $modal).val("");
		$("#title", $modal).val("");
		$("#date", $modal).val("");
		$("#comment", $modal).val("");
		$("tbody", $modal).empty();
		$(".btn-danger", $modal).hide();
		var path = $("h2").html();
		$("#path", $modal).val(path);
	});

	$(document).on("dblclick", ".editable", function(){
		var $modal = $('#change-event').modal();
		$(".modal-title", $modal).text("イベントを変更…");
		var event_id = $(this).data("event-id");
		var path = $("h2", $(this)).html();
		var title = $("h3", $(this)).html();
		var date = $("time", $(this)).attr("datetime");
		var comment = $(".comment", $(this)).html();
		var url = "{{$url_event_edit}}".replace(":id", event_id);
		$("form", $modal).attr("action", url);
		$("#event-id", $modal).val(event_id);
		$("#path", $modal).val(path);
		$("#title", $modal).val(title);
		$("#date", $modal).val(date);
		$("#comment", $modal).val(comment);
		$(".btn-danger", $modal).show();

		$.ajax({
			type: "get",
			url: "{{$url_event_detail}}".replace(":id", event_id),
		})
		.done(function(data, message, xhr){
			var $tbody = $("tbody", $modal).empty();
			for (idx in data) {
				add_row($tbody, idx, data[idx]);
			}
		});
	});

	$(document).on("click", "#change-event .row_add", function() {
		var $modal = $('#change-event');
		var $tbody = $("tbody", $modal);

		var idx = $("tbody tr", $modal).length;
		add_row($tbody, idx, {});
	});

	$(document).on("click", "#change-event .btn-danger", function(){
		var $modal = $('#change-event');
		var event_id = $("#event-id", $modal).val();
		var url = "{{$url_event_delete}}".replace(":id", event_id);
		$("form", $modal).attr("action", url);
	});
});
			</script>
