
			<h2>{{$path}}</h2>
			<div class="col-md-6 offset-md-3">
				<div class="timeline">
{{foreach name=events from=$events item=event}}
{{if $smarty.foreach.events.index != 0}}
					<p class="add"><i class="fas fa-plus"></i></p>
{{/if}}
					<article class="clearfix editable">
						<header>
							<h3 class="float-left">{{$event.type}}</h3>
							<time class="float-right">{{$event.date|date_format:'Y-m-d'}}</time>
						</header>
					</article>
{{/foreach}}
				</div>
			</div>

			<div id="change-event" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<form class="modal-content" action="#" method="post">
						<div class="modal-header">
							<h5 class="modal-title">イベントを変更……</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
								<span aria-hidden="true">&times;</span>
							</button>
						</div><!-- /.modal-header -->
						<div class="modal-body">
							<p>モーダルボディの本文。</p>
						</div><!-- /.modal-body -->
						<div class="modal-footer">
							<nav>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
								<button type="submit" name="mode" value="submit" class="btn btn-primary">変更を保存</button>
								<button type="submit" name="mode" value="delete" class="btn btn-danger">削除</button>
							</nav>
						</div><!-- /.modal-footer -->
					</form><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<script>
$(document).on("click", ".add", function(){
	var $modal = $('#change-event').modal();
	$(".modal-title", $modal).text("イベントを追加…");
	$(".btn-danger", $modal).hide();
});

$(document).on("click", ".editable", function(){
	var $modal = $('#change-event').modal();
	$(".modal-title", $modal).text("イベントを変更…");
	$(".btn-danger", $modal).show();
});

$(document).on("click", "#change-event .btn-danger", function(){
	alert(0);
	return false;
});
			</script>
