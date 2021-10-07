
			<div id="add-divisions-csv" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<form class="modal-content"
							action="{{\MyApp\Helper\Uri::create('division.add_csv')}}"
							method="post">
						<div class="modal-header">
							<h5 class="modal-title">自治体を一括追加……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<fieldset class="form-group">
								<div class="row">
									<legend class="col-form-label col-sm-2 pt-0">Input type</legend>
									<div class="col-sm-10">
										<label class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="type" value="csv" checked="checked"/>
											CSV
										</label>
										<label class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="type" value="tsv" />
											TSV
										</label>
									</div>
								</div>
							</fieldset>
							<div><label for="add-csv">Input as CSV……</label></div>
							<textarea class="form-control form-control-lg" id="add-csv" name="body"></textarea>
							<table class="preview mt-2"></table>
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
				function preview() {
					const $preview = $('#add-divisions-csv .preview').empty();
					const separator = ($('#add-divisions-csv input[name=type]:checked').val() === 'tsv' ? '\t' : ',');
					const csv = $('#add-csv').val();
					const lines = csv.split('\n');

					let $tr = $('<tr />').appendTo($preview);
					lines[0].split(separator).forEach(item => {
						$('<th />').text(item).appendTo($tr);
					});

					lines.shift();
					lines.forEach(line => {
						let $tr = $('<tr />').appendTo($preview);
						line.split(separator).forEach(item => {
							$('<td />').text(item).appendTo($tr);
						});
					});
				}
				$('#add-csv').on('input', function() {
					preview();
				});
				$('#add-divisions-csv input[name=type]').on('change', function() {
					preview();
				});
			</script>
