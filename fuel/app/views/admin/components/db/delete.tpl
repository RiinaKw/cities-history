
			<div id="delete-backup" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document" data-url="{{$url_delete}}">
					<form class="modal-content" action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">バックアップを削除……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<p>以下のファイルを削除してよろしいですか？</p>
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
								<button type="submit" class="btn btn-danger">
									<i class="fa fa-trash"></i>
									削除
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
