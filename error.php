<?php
$filename = preg_replace('/[^A-Za-z ]/', '', strval(empty($_GET['p']) ? '' : $_GET['p']));
$filename = $filename == '' ? 'index' : $filename;
$loadedJson = @file_get_contents("projects/$filename.json");
if (!$loadedJson) {
		$loadedJson = <<<default_doc
[
	{"type":"h1", "content":"Error"},
	{"type":"p", "content":"No page called '$filename' could be found."}
]
default_doc;
}
$page = json_decode($loadedJson, true);
?>
<!DOCTYPE html>
<html>
<head>
<meta content="en-us" http-equiv="Content-Language">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" href="fonts.css" />
<link rel="stylesheet" href="style.css" />
<title>Edward Giles</title>
<script type="text/javascript">
var page = <?php echo($loadedJson); ?>
</script>
</head>
<body>
<div class="mainContainer">
	<?php include('assets/header.php'); ?>
	<div class="thinPane">
	</div>
	<div class="content">
	<?php if ($filename != 'index') echo('<p><a href="?p=index" style="font-style:italic;">&#8594; Index</a></p>'); ?>
	<h1>Error: 404 Not Found</h1>
	</div>
	<?php include('assets/footer.php'); ?>
</div>

</body>

</html>
