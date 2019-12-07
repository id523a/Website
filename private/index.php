<?php setlocale(LC_ALL, "en_US.UTF-8");
?><!DOCTYPE html>
<html lang="en-us">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" href="../fonts.css" />
<link rel="stylesheet" href="../style.css" />
<style type="text/css">
.index_link {
	font-style:italic;
}
.action_button {
	width:7.2em;
	margin:0.2em;
}
.download_list {
	list-style:none;
	padding-left:0;
}
.download_list li {
	cursor:default;
}
.download_list li:hover {
	background-color:#EEEEFF;
}
.download_link {
	margin-left:0.5em;
}
#upload_file, #rename_to {
	width:16em;
}
.status_container {
	padding:0.5em 1em;
	background-color:#FFCCCC;
}
.status_container.success {
	background-color:#CCFFDD;
}
.after_status {
	margin-top:0.5em;
}
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Private</title>
<script type="text/javascript">
function renameChange() {
	var renameTo = document.getElementById('rename_to');
	var renameButton = document.getElementById('rename_button');
	renameTo.setCustomValidity('');
	renameButton.disabled = !renameTo.value;
}
function fileChange() {
	var renameTo = document.getElementById('rename_to');
	var fileActionButtons = document.getElementById('file_actions');
	var selectedFile = document.getElementById('file_select').elements['f'].value;
	if (selectedFile) {
		renameTo.value = selectedFile;
		renameChange();
		fileActionButtons.style.display = 'block';
	} else {
		fileActionButtons.style.display = 'none';
	}
}
function jsInit() {
	fileChange();
	document.querySelectorAll('#file_select li').forEach(function(el) {
		el.onclick = function(e) {
			el.querySelector('[name="f"]').checked = true;
			fileChange();
		}
	});
	document.querySelectorAll('input[name="f"]').forEach(function(el) {
		el.onchange = fileChange;
	});
	
	var uploadButton = document.getElementById('upload_button');
	var uploadFile = document.getElementById('upload_file');
	uploadButton.disabled = (uploadFile.files.length <= 0);
	uploadFile.onchange = function(e) {
		uploadButton.disabled = (uploadFile.files.length <= 0);
	};
}
function clearRename() {
	var renameTo = document.getElementById('rename_to');
	renameTo.setCustomValidity('');
}
function validateRename() {
	var renameTo = document.getElementById('rename_to');
	var renameToVal = renameTo.value;
	if (!renameToVal) {
		renameTo.setCustomValidity('Please specify a filename.');
	} else if (!renameToVal.match(/^[A-Za-z0-9_]([A-Za-z0-9_ .-]*[A-Za-z0-9_-])?$/)) {
		renameTo.setCustomValidity('Invalid filename.');
	} else {
		renameTo.setCustomValidity('');
	}
}
function confirmDelete(e) {
	var file = document.getElementById('file_select').elements['f'].value;
	if (!file ||
	!confirm('Are you sure you want to delete ' + file + '?')) {
		e.preventDefault();
	} else {
		clearRename();
	}
}
</script>
</head>
<body onload="jsInit();">
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
