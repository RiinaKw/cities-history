<?php
/* Smarty version 3.1.33, created on 2019-04-19 05:23:39
  from 'C:\xampp\htdocs\city-history\fuel\app\views\layout.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5cb8dccb9f56b0_52431959',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5b2ed241fce7a378ad3d2c741d2f0f5e92f309e7' => 
    array (
      0 => 'C:\\xampp\\htdocs\\city-history\\fuel\\app\\views\\layout.tpl',
      1 => 1555619017,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5cb8dccb9f56b0_52431959 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title>Hello</title>

	<?php echo Asset::css('bootstrap.min.css');?>

		<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


	<?php echo Asset::js('jquery-3.3.1.min.js');?>

	<?php echo Asset::js('jquery-ui.min.js');?>

	<?php echo Asset::js('bootstrap.min.js');?>

	</head>
	<body>

		<!-- Begin page content -->
		<main role="main" class="container">

<?php echo $_smarty_tpl->tpl_vars['content']->value;?>


		</main>

		<footer class="footer">
			<div class="container">
				<span class="text-muted">copyright</span>
			</div>
		</footer>
	</body>
</html>
<?php }
}
