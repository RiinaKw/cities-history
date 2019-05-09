
			<div id="delete-reference" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document" data-url="{{$url_delete}}">
					<form class="modal-content" action="" method="post">
						<div class="modal-header">
							<h5 class="modal-title">参照を削除……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<input type="hidden" id="reference-id" value="" />
							<p>「<span id="delete-reference-description"></span>」を削除してよろしいですか？</p>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">
									<i class="fa fa-times"></i>
									キャンセル
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
