<?php
$filename = preg_replace('/[^A-Za-z ]/', '', strval(empty($_GET['p']) ? '' : $_GET['p']));
$filename = $filename == '' ? 'index' : $filename;
$loadedJson = file_get_contents("projects/$filename.json");
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
	<div class="header">
		<div class="bio">
			<img class="thumbnail" alt="" src="face.jpg" />
			<h1>Edward Giles</h1>
		</div>
		<div class="socialMedia">
			<a class="mediaLink" href="mailto:edward@giles.net.au"><img class="icon" src="icons/mail.png" alt="E-mail" />edward@giles.net.au</a>
			<a class="mediaLink" href="skype:edward@giles.net.au"><img class="icon" src="icons/skype.png" alt="Skype" />edward@giles.net.au</a>
			<a class="mediaLink" href="https://www.youtube.com/channel/UCdWI3Bs_d_Z89sWYJz9unXQ"><img class="icon" src="icons/yt.png" alt="YouTube" />id523a</a>
			<a class="mediaLink" href="https://www.linkedin.com/in/id523a"><img class="icon" src="icons/in.png" alt="LinkedIn" />Edward Giles</a>
			<a class="mediaLink" href="https://github.com/id523a"><img class="icon" src="icons/gh.png" alt="GitHub" />id523a</a>
			<a class="mediaLink" href="https://soundcloud.com/id523a"><img class="icon" src="icons/sc.png" alt="SoundCloud" />id523a</a>
		</div>
	</div>
	<div class="thinPane">
		<script type="text/javascript" src="genpdf.js"></script>
		<a id="pdfd_init" class="pdfDownload" href="javascript:;" onclick="CreatePDF()">Download PDF</a>
		<a id="pdfd" class="hide" href="#" download="<?php echo($filename); ?>.pdf">Download PDF</a>
		<span id="errordisplay" class="hide"></span>
	</div>
	<div class="content">
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
		$formatted = $boldGiven || $italicGiven || !empty($color);
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
		if ($formatted) {
			echo('<span style="');
			if ($boldGiven) { echo("font-weight:$bold;"); }
			if ($italicGiven) { echo("font-style:$italic;"); }
			if (!empty($color)) { echo("color:$color;"); }
			echo('">');
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
		if ($formatted) { echo('</span>'); }
	}
	?>
	</div>
	<div class="thinPane">
		<a href="mailto:edward@giles.net.au"><img class="icon" src="icons/mail.png" alt="E-mail" /></a>
		<a href="skype:edward@giles.net.au"><img class="icon" src="icons/skype.png" alt="Skype" /></a>
		<a href="https://www.youtube.com/channel/UCdWI3Bs_d_Z89sWYJz9unXQ"><img class="icon" src="icons/yt.png" alt="YouTube" /></a>
		<a href="https://www.linkedin.com/in/id523a"><img class="icon" src="icons/in.png" alt="LinkedIn" /></a>
		<a href="https://github.com/id523a"><img class="icon" src="icons/gh.png" alt="GitHub" /></a>
		<a href="https://soundcloud.com/id523a"><img class="icon" src="icons/sc.png" alt="SoundCloud" /></a>
	</div>
</div>

</body>

</html>
