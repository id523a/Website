<?php setlocale(LC_ALL, "en_US.UTF-8");

function validateFileName($fname) {
	return strlen($fname) <= 48 &&
		boolval(mb_ereg_match('^[A-Za-z0-9_]([A-Za-z0-9_ .-]*[A-Za-z0-9_-])?$', $fname));
}

function reportSuccess($msg) {
	header('Location: index.php?s=' . rawurlencode($msg));
	exit();
}

function reportError($errmsg) {
	header('Location: index.php?e=' . rawurlencode($errmsg));
	exit();
}

if (empty($_POST) && empty($_FILES)) {
	$maxPostSize = trim(ini_get('post_max_size'));
	$maxPostSizeV = intval($maxPostSize);
	switch (strtolower($maxPostSize[strlen($maxPostSize)-1])) {
	case 'g': $maxPostSizeV *= 1024;
	case 'm': $maxPostSizeV *= 1024;
	case 'k': $maxPostSizeV *= 1024;
	}
	if ($_SERVER['CONTENT_LENGTH'] > $maxPostSizeV) {
		reportError('Upload error: File too large.');
	}
}

if (empty($_POST['act']) || $_POST['act'] !== 'upload') {
	reportError('Invalid action.');
}

if (empty($_FILES['upload_file'])) {
	reportError('Upload error: No file selected.');
}

function makeValidName($fname) {
	$fname = strval($fname);
	$fname_len = mb_strlen($fname);
	if ($fname_len > 48) {
		$fname = mb_substr($fname, 0, 48);
	} else if ($fname_len <= 0) {
		return '_';
	}
	$fname = mb_ereg_replace('\\s', ' ', $fname);
	$fname = mb_ereg_replace('[^A-Za-z0-9_ .-]+', '_', $fname);
	if (strpos(' .-', $fname[0]) !== FALSE) {
		$fname[0] = '_';
	}
	$fname_len = strlen($fname);
	if (strpos(' .', $fname[$fname_len - 1]) !== FALSE) {
		$fname[$fname_len - 1] = '_';
	}
	return $fname;
}

$uploadFile = $_FILES['upload_file'];

switch ($uploadFile['error']) {
case UPLOAD_ERR_OK:
	break;
case UPLOAD_ERR_INI_SIZE:
case UPLOAD_ERR_FORM_SIZE:
	reportError('Upload error: File too large.');
	break;
case UPLOAD_ERR_PARTIAL:
	reportError('Upload error: File only partially uploaded.');
	break;
case UPLOAD_ERR_NO_FILE:
	reportError('Upload error: No file selected.');
	break;
case UPLOAD_ERR_NO_TMP_DIR:
case UPLOAD_ERR_CANT_WRITE:
	reportError('Upload error: Could not write uploaded file to disk.');
	break;
default:
	reportError('Upload error: Unknown error.');
	break;
}

$uploadNewName = makeValidName(basename($uploadFile['name']));
$uploadNewPath = "files/$uploadNewName";

if (file_exists($uploadNewPath)) {
	reportError("Upload error: $uploadNewName already exists.");
}

if (move_uploaded_file($uploadFile['tmp_name'], $uploadNewPath)) {
	reportSuccess("Successfully uploaded $uploadNewName.");
} else {
	reportError("Upload error: Unknown error.");
}
?>
