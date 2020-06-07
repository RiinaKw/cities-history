
			<div id="db-detail" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document" data-url="{{$url_download}}">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">バックアップの詳細……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<p>詳細</p>
							<label class="row">
								<span class="col-4">ファイル名</span>
								<div class="col-8">
									<b class="file"></b>
								</div>
							</label>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal" />
									<i class="fa fa-times"></i>
									閉じる
								</button>
								<button type="button" class="btn btn-primary">
									<i class="fa fa-download"></i>
									Download
								</button>
								<button type="submit" class="btn btn-success">
									<i class="fa fa-upload"></i>
									復元
								</button>
								<button type="submit" class="btn btn-danger">
									<i class="fa fa-trash"></i>
									削除
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>
$(function(){
	$("tr").on("dblclick", function(){
		var file = $(this).data("file");

		//$("#restore-backup form").attr("action", url);
		$("#db-detail b.file").html(file);
		$("#db-detail").modal();
	});

	$("#db-detail .btn-primary").on("click", function(){
		var file = $("#db-detail b.file").html();
		var url = $("#db-detail .modal-dialog").data("url").replace(":file", file);

		location.href = url;
	});

	$("#db-detail .btn-success").on("click", function(){
		var file = $("#db-detail b.file").html();
		var url = $("#restore-backup .modal-dialog").data("url").replace(":file", file);

		$("#db-detail").modal("hide");
		$("#restore-backup form").attr("action", url);
		$("#restore-backup b.file").html(file);
		$("#restore-backup").modal();
	});

	$("#db-detail .btn-danger").on("click", function(){
		var file = $("#db-detail b.file").html();
		var url = $("#delete-backup .modal-dialog").data("url").replace(":file", file);

		$("#db-detail").modal("hide");
		$("#delete-backup form").attr("action", url);
		$("#delete-backup b.file").html(file);
		$("#delete-backup").modal();
	});

	$("#restore-backup .btn-secondary").on("click", function(){
		$("#restore-backup").modal("hide");
		$("#db-detail").modal();
	});

	$("#delete-backup .btn-secondary").on("click", function(){
		$("#delete-backup").modal("hide");
		$("#db-detail").modal();
	});
});
			</script>
