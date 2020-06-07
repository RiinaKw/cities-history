
			<div id="restore-backup" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document" data-url="{{$url_restore}}">
					<form class="modal-content" action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">バックアップから復元……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<p>以下のファイルから復元してよろしいですか？</p>
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
								<button type="submit" class="btn btn-success">
									<i class="fa fa-upload"></i>
									復元
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
