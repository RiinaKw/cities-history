
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
									<input class="form-control" type="text" id="date" name="date" required="required" pattern="^([0-9]{1,4}|[A-Z][0-9]{1,2})-[0-9]{1,2}-[0-9]{1,2}$" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">注釈</span>
								<div class="col-8">
									<textarea class="form-control" type="text" id="comment" name="comment"></textarea>
								</div>
							</label>
							<label class="row">
								<span class="col-4">出典</span>
								<div class="col-8">
									<textarea class="form-control" id="source" name="source"></textarea>
								</div>
							</label>
							<table class="table table-sm table-borderless">
								<thead>
									<tr>
										<th class="text-center" scope="col"></th>
										<th>
											<table class="table table-sm table-borderless mb-0">
												<tr>
													<th scope="col" style="">自治体</th>
													<th scope="col" style="width: 20%;">結果</th>
													<th class="text-center" scope="col" style="width: 6%;">新設</th>
													<th class="text-center" scope="col" style="width: 6%;">廃止 /<br />存続</th>
													<th class="text-center" scope="col" style="width: 6%;">参照</th>
													<th class="text-center" scope="col" style="width: 6%;">削除</th>
												</tr>
											</table>
										</th>
									</tr>
								</thead>
								<tbody id="change-event-sortable"></tbody>
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
	$input_no.attr("name", "order["+idx+"]").val(idx);

	var $handle = $("<th />").addClass("handle").appendTo($tr);
	$handle.append('<i class="fa fa-bars"></i>');

	var $td = $("<td />").appendTo($tr);

	var $table = $("<table />").appendTo($td);
	var $tr1 = $("<tr />").appendTo($table);
	var $tr2 = $("<tr />").appendTo($table);

	var $td_division = $("<td />").attr("colspan", 6).appendTo($tr1);
	var $input_division = $('<input type="text" />').addClass("form-control");
	$input_division.attr("name", "division["+idx+"]");
	if (detail.path) {
		$input_division.val(detail.path);
	}
	$input_division.appendTo($td_division);

	var $td_geoshape = $("<td />").appendTo($tr2);
	var $input_geoshape = $('<input type="text" />').addClass("form-control");
	$input_geoshape.attr("name", "geoshape["+idx+"]");
	$input_geoshape.attr("placeholder", "geoshape...");
	if (detail.geoshape) {
		$input_geoshape.val(detail.geoshape);
	}
	$input_geoshape.appendTo($td_geoshape);

	$input_division.devbridgeAutocomplete({
		serviceUrl: "{{\MyApp\Helper\Uri::restDivisionList()}}"
	});

	var $td_result = $("<td />").css("width", "20%").appendTo($tr2);
	var $input_result = $('<input type="text" />').addClass("form-control");
	$input_result.attr("value", detail.result).attr("name", "result["+idx+"]");
	if (detail.result) {
		$input_result.val(detail.result);
	}
	$input_result.appendTo($td_result);

	var $td_birth = $("<td />").addClass("text-center").addClass("checkbox-wrapper").css("width", "6%").appendTo($tr2);
	var $input_birth = $('<input type="checkbox" />');
	$input_birth.attr("value", "true").attr("name", "birth["+idx+"]");
	$input_birth.appendTo($td_birth);
	if (detail.birth) {
		$input_birth.prop("checked", true);
	}

	var $td_death = $("<td />").addClass("text-center").addClass("checkbox-wrapper").css("width", "6%").appendTo($tr2);
	var $input_death = $('<input type="checkbox" />');
	$input_death.attr("value", "true").attr("name", "death["+idx+"]");
	$input_death.appendTo($td_death);
	if (detail.death) {
		$input_death.prop("checked", true);
	}

	var $td_refer = $("<td />").addClass("text-center").addClass("checkbox-wrapper").css("width", "6%").appendTo($tr2);
	var $input_refer = $('<input type="checkbox" />');
	$input_refer.attr("value", "true").attr("name", "refer["+idx+"]");
	$input_refer.appendTo($td_refer);
	if (detail.is_refer) {
		$input_refer.prop("checked", true);
	}

	var $td_delete = $("<td />").addClass("text-center").addClass("checkbox-wrapper").css("width", "6%").appendTo($tr2);
	var $input_delete = $('<input type="checkbox" />');
	$input_delete.attr("value", "true").attr("name", "delete["+idx+"]");
	$input_delete.appendTo($td_delete);
} // function add_row()

$(function(){

	$("#change-event-sortable").sortable({
		handle: "th.handle i",
		cursor: "move",
		opacity: 0.5,
		stop: function(event, ui) {
			//console.log(event);
			$(".row-no").each(function(v){
				$(this).val(v);
			});
		}
	});

	$(document).on("click", ".add", function(){
		var $modal = $('#change-event').modal();
		$(".modal-title", $modal).text("イベントを追加…");
		$("form", $modal).attr("action", "{{\MyApp\Helper\Uri::create('admin.event.add')}}");
		$("#event-id", $modal).val("");
		$("#path", $modal).val("");
		$("#title", $modal).val("");
		$("#date", $modal).val("");
		$("#comment", $modal).val("");
		$("#source", $modal).val("");
		$("#change-event-sortable", $modal).empty();
		$(".btn-danger", $modal).hide();
		var path = $("h2").html();
		$("#path", $modal).val(path);
	});

	$(document).on("dblclick", ".editable", function(){
		let event_id = $(this).data("event-id");
		var $modal = $('#change-event').modal();
		$(".modal-title", $modal).text("イベントを変更…");
		var url = "{{\MyApp\Helper\Uri::create('admin.event.edit')}}".replace(":id", event_id);
		$("form", $modal).attr("action", url);

		$.ajax({
			type: "get",
			url: "{{\MyApp\Helper\Uri::create('event.detail')}}".replace(":id", event_id),
		})
		.done(function(data, message, xhr){
			$("#event-id", $modal).val(data['event']['id']);
			$("#title", $modal).val(data['event']['title']);
			$("#date", $modal).val(data['event']['date']);
			$("#comment", $modal).val(data['event']['comment']);
			$("#source", $modal).val(data['event']['source']);
			$(".btn-danger", $modal).show();

			var $tbody = $("#change-event-sortable", $modal).empty();
			for (idx in data["details"]) {
				add_row($tbody, idx, data["details"][idx]);
			}
		});
	});

	$(document).on("click", "#change-event .row_add", function() {
		var $modal = $('#change-event');
		var $tbody = $("#change-event-sortable", $modal);

		var idx = $("tr", $tbody).length;
		add_row($tbody, idx, {});
	});

	$(document).on("click", "#change-event .btn-danger", function(){
		var $modal = $('#change-event');
		var event_id = $("#event-id", $modal).val();
		var url = "{{\MyApp\Helper\Uri::create('admin.event.delete')}}".replace(":id", event_id);
		$("form", $modal).attr("action", url);
	});
});
			</script>
