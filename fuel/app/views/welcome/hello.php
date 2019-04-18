
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>Hello, <?php echo $name; ?>! <small>Congratulations, you just used a Presenter!</small></h1>
				<hr>
				<p>The controller generating this page is found at <code>APPPATH/classes/controller/welcome.php</code>.</p>
				<p>This view is located at <code>APPPATH/views/welcome/hello.php</code>.</p>
				<p>It is loaded via a Presenter class with a name of <code>Presenter_Welcome_Hello</code>, located in <code>APPPATH/classes/presenter/welcome/hello.php</code></p>
			</div>
		</div>
		<footer>
			<p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
			<p>
				<a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
				<small>Version: <?php echo Fuel::VERSION; ?></small>
			</p>
		</footer>
	</div>
