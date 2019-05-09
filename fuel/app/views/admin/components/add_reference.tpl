
			<div id="add-reference" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<form class="modal-content" action="{{$url_add}}" method="post">
						<div class="modal-header">
							<h5 class="modal-title">参照を追加……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<label class="row">
								<span class="col-4">日付</span>
								<div class="col-8">
									<input class="form-control" type="text" name="date" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">詳細</span>
								<div class="col-8">
									<input class="form-control" type="text" name="description" value="" />
								</div>
							</label>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal" />
									<i class="fa fa-times"></i>
									閉じる
								</button>
								<button type="submit" class="btn btn-primary">
									<i class="fa fa-edit"></i>
									保存
								</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
