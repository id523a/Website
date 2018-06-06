<?php
$filename = preg_replace('/[^A-Za-z0-9]/', '', strval(empty($_GET['p']) ? '' : $_GET['p']));
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
<html lang="en-us">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" href="fonts.css" />
<link rel="stylesheet" href="style.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edward Giles</title>
<script type="text/javascript">
var page = <?php echo($loadedJson); ?>
</script>
</head>
<body>
<div class="mainContainer">
	<?php include('assets/header.php'); ?>
	<?php if (empty($_GET['e'])) { ?>
	<div class="thinPane">
		
		<a id="pdfd_init" class="pdfDownload" href="javascript:;" onclick="CreatePDF()">Generate PDF</a>
		<a id="pdfd" class="hide" href="#" download="<?php echo($filename); ?>.pdf">Download PDF</a>
		<span id="errordisplay" class="hide"></span>
	</div>
	<?php } ?>
	<div class="content">
	<?php if ($filename != 'index' && empty($_GET['e'])) echo('<p><a href="?p=index" style="font-style:italic;">&#8594; Index</a></p>'); ?>
	<?php
	$tag = '';
	foreach ($page as $k=>$pageItem) {
		$type = strval($pageItem['type']);
		$content = strval($pageItem['content']);
		$boldGiven = array_key_exists('bold', $pageItem);
		$bold = false;
		if ($boldGiven) {
			$bold = boolval($pageItem['bold']) ? 'bold' : 'normal';
		}
		$italicGiven = array_key_exists('italic', $pageItem);
		$italic = false;
		if ($italicGiven) {
			$italic = boolval($pageItem['italic']) ? 'italic' : 'normal';
		}
		$color = empty($pageItem['color']) ? '' : $pageItem['color'];
		$linkGiven = array_key_exists('link', $pageItem);
		$link = '#';
		if ($linkGiven) {
			$link = $pageItem['link'];
		}
		$formatted = $boldGiven || $italicGiven || !empty($color) || $linkGiven;
		if ($type != '') {
			if (!empty($tag)) {
				echo("</$tag>");
			}
			if ($type == 'img') {
				$tag = '';
			} else {
				$tag = $type;
				echo("<$tag>");
			}
		}
		if ($type == 'img') {
			echo('<div class="imgScroll">');
		}
		if ($formatted) {
			if ($linkGiven) {
				echo("<a href=\"$link\" style=\""); 
			} else {
				echo('<span style="');
			}
			if ($boldGiven) { echo("font-weight:$bold;"); }
			if ($italicGiven) { echo("font-style:$italic;"); }
			if (!empty($color)) { echo("color:$color;"); }
			if ($linkGiven && !empty($pageItem['download'])) {
				echo('" download="">');
			} else {
				echo('">');
			}
		}
		if ($type == 'img') {
			echo('<img src="');
			echo($content);
			echo('" alt="');
			if (!empty($pageItem['alt'])) {
				echo(strval($pageItem['alt']));
			} else {
				echo('Image');
			}
			echo('" />');
		} else {
			echo(htmlspecialchars($content));
		}
		if ($formatted) { echo($linkGiven ? '</a>' : '</span>'); }
		if ($type == 'img') { echo('</div>'); }
	}
	if (!empty($tag)) {
		echo("</$tag>");
	}
	?>
	<?php if ($filename != 'index') echo('<p><a href="?p=index" style="font-style:italic;">&#8594; Index</a></p>'); ?>
	</div>
	<?php include('assets/footer.php'); ?>
	<?php if (empty($_GET['e'])) { ?>
	<script type="text/javascript" src="js/pdfbutton.js"></script>
	<?php } ?>
</div>

</body>

</html>
