
			<div id="edit-reference" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document" data-url="{{$url_edit}}">
					<form class="modal-content" action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">参照を変更……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<input type="hidden" id="reference-id" value="" />
							<label class="row">
								<span class="col-4">日付</span>
								<div class="col-8">
									<input class="form-control" type="text" id="reference-date" name="date" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">詳細</span>
								<div class="col-8">
									<input class="form-control" type="text" id="reference-description" name="description" value="" />
								</div>
							</label>
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
								<button type="button" class="btn btn-danger" data-dismiss="modal">
									<i class="fa fa-trash"></i>
									削除
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>
$(function(){
	$("tbody tr").on("dblclick", function(){
		var id = $(this).data("id");
		var date = $(this).data("date");
		var url = $("#edit-reference .modal-dialog").data("url").replace(":id", id);
		$("#edit-reference form").attr("action", url);

		$("#reference-id").val(id);
		$("#reference-date").val(date);
		$("#reference-description").val( $(".description", this).html() );
		$("#edit-reference").modal();
	});

	$("#edit-reference .btn-danger").on("click", function(){
		var id = $("#reference-id").val();
		var url = $("#delete-reference .modal-dialog").data("url").replace(":id", id);
		$("#delete-reference form").attr("action", url);

		$("#edit-reference").modal("hide");
		$("#reference-id").val(id);
		$("#delete-reference-description").html( $("#reference-description").val() );
		$("#delete-reference").modal();
	});
});
			</script>
