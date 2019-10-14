
			<div id="add-division" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<form class="modal-content" action="{{$url_add}}" method="post">
						<div class="modal-header">
							<h5 class="modal-title">自治体を追加……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<label class="row">
								<span class="col-4">所属自治体</span>
								<div class="col-8">
									<input class="form-control" type="text" id="add-parent-division" name="parent" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">支庁・振興局</span>
								<div class="col-8">
									<input class="form-control" type="text" id="add-belongs-division" name="belongs" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">自治体名</span>
								<div class="col-8">
									<input class="form-control" type="text" name="name" value="" required="required" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">自治体名かな</span>
								<div class="col-8">
									<input class="form-control" type="text" name="name_kana" value="" required="required" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">接尾語</span>
								<div class="col-8">
									<input class="form-control" type="text" name="suffix" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">接尾語かな</span>
								<div class="col-8">
									<input class="form-control" type="text" name="suffix_kana" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">接尾語を使う</span>
								<div class="col-8 checkbox-wrapper">
									<input type="checkbox" name="show_suffix" value="true" checked="checked" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">識別名</span>
								<div class="col-8">
									<input class="form-control" type="text" name="identify" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">全国地方公共団体コード</span>
								<div class="col-8">
									<input class="form-control" type="text" name="government_code" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">表示順</span>
								<div class="col-8">
									<input class="form-control" type="text" name="display_order" value="" />
								</div>
							</label>
							<label class="row">
								<span class="col-4">未完成</span>
								<div class="col-8 checkbox-wrapper">
									<input type="checkbox" name="is_unfinished" value="true" checked="checked" />
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
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>
$(function(){
	$("#add-parent-division").devbridgeAutocomplete({
		serviceUrl: "{{$url_root}}/division/list.json"
	});
	$("#add-belongs-division").devbridgeAutocomplete({
		serviceUrl: "{{$url_root}}/division/list.json"
	});
});
			</script>
