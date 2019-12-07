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
