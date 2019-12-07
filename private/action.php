<?php setlocale(LC_ALL, "en_US.UTF-8");

$fname = '';
if (!empty($_GET['f'])) {
	$fname = strval($_GET['f']);
} else if (!empty($_POST['f'])) {
	$fname = strval($_POST['f']);
}

$act = '';
if (!empty($_GET['act']) && $_GET['act'] === 'download') {
	$act = 'download';
} else if (!empty($_POST['act'])) {
	$act = strval($_POST['act']);
}

$fname2 = '';
if (!empty($_POST['rename_to'])) {
	$fname2 = strval($_POST['rename_to']);
}

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

function download($fname, $fname_real) {
	header('Content-Description: Download');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$fname.'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Content-Length: ' . filesize($fname_real));
	readfile($fname_real);
	exit();
}

if (validateFileName($fname)) {
	$fname_real = "files/$fname";
	$fname2_real = "files/$fname2";
	switch ($act) {
	case 'download':
		if (file_exists($fname_real)) {
			download($fname, $fname_real);
		} else {
			reportError("File $fname not found.");
		}
		break;
	case 'delete':
		if (file_exists($fname_real)) {
			if (unlink($fname_real)) {
				reportSuccess("Successfully deleted $fname.");
			} else {
				reportError("Could not delete $fname: unknown error.");
			}
		} else {
			reportError("Could not delete $fname: file not found.");
		}
		break;
	case 'rename':
		if ($fname2 === '' || $fname2 === $fname) {
			reportSuccess('');
		} else if (!validateFileName($fname2)) {
			reportError("Could not rename $fname: new name is invalid.");
		} else if (!file_exists($fname_real)) {
			reportError("Could not rename $fname: file not found.");
		} else if (file_exists($fname2_real)) {
			reportError("Could not rename $fname: $fname2 already exists.");
		} else {
			if (rename($fname_real, $fname2_real)) {
				reportSuccess("Successfully renamed $fname to $fname2.");
			} else {
				reportError("Could not rename $fname: unknown error.");
			}
		}
	default:
		reportError('Invalid action.');
		break;
	}
} else {
	reportError($fname ? 'Invalid file name.' : 'No file selected.');
}
?>
