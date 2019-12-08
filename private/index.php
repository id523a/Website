<?php setlocale(LC_ALL, "en_US.UTF-8");
?><!DOCTYPE html>
<html lang="en-us">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" href="../fonts.css" />
<link rel="stylesheet" href="../style.css" />
<link rel="stylesheet" href="private_styles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Private</title>
<script type="text/javascript" src="private_script.js"></script>
</head>
<body>
<div class="mainContainer" style="min-width:400px;">
<div class="content">
	<p><a href="../index" class="index_link">&#8594; Index</a></p>
	<h1 style="margin-top:0em;margin-bottom:0em;">Private Files</h1>
	<?php
		if (!empty($_GET["e"])) {
			echo('<div class="status_container">');
			echo(htmlspecialchars($_GET["e"]));
			echo('</div>');
		} else if (!empty($_GET["s"])) {
			echo('<div class="status_container success">');
			echo(htmlspecialchars($_GET["s"]));
			echo('</div>');
		}
	?>
	<noscript><div class="status_container js_disabled">JavaScript is disabled.</div></noscript>
	<form class="after_status" method="POST" action="upload.php" enctype="multipart/form-data">
		<input type="hidden" name="act" value="upload" />
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php
			$maxFileSize = trim(ini_get('upload_max_filesize'));
			$maxFileSizeV = intval($maxFileSize);
			switch (strtolower($maxFileSize[strlen($maxFileSize)-1])) {
			case 'g': $maxFileSizeV *= 1024;
			case 'm': $maxFileSizeV *= 1024;
			case 'k': $maxFileSizeV *= 1024;
			}
			echo($maxFileSizeV);
		?>" />
		<button id="upload_button" class="action_button" type="submit">Upload</button>
		<input id="upload_file" type="file" name="upload_file" />
	</form>
	<form id="file_select" method="POST" action="action.php">
	<input style="display:none;" type="radio" name="f" value=""/>
	<ul class="download_list">
	<?php
		$files = scandir('files');
		foreach ($files as $fname) {
			if (preg_match('/^[A-Za-z0-9_]([A-Za-z0-9_ .-]*[A-Za-z0-9_-])?$/', $fname)) {
				echo('<li><input type="radio" name="f" value="' . $fname . '"/><a href="action.php?act=download&f=' . $fname . '">' . $fname . '</a></li>');
			}
		}
	?>
	</ul>
	<div id="file_actions">
		<div>
			<button class="action_button" type="submit" name="act" value="download" onclick="clearRename();">Download</button>
		</div>
		<div>
			<button class="action_button" type="submit" name="act" value="delete" onclick="confirmDelete(event);">Delete</button>
		</div>
		<div>
			<button id="rename_button" class="action_button" type="submit" name="act" value="rename" onclick="validateRename();">Rename to</button>
			<input id="rename_to" type="text" name="rename_to" placeholder="New name" maxlength="48"
			onchange="validateRename();" oninput="clearRename();"/>
		</div>
	</div>
	</form>
	<p><a href="../index" class="index_link">&#8594; Index</a></p>
</div>
</div>

</body>

</html>
