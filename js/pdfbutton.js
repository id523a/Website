function loadUri(uri) {
	return new Promise(function(resolve, reject) {
		req = new XMLHttpRequest();
	    req.open("GET", uri, true);
	    req.responseType = "arraybuffer";
	    req.onload = function() {
	        var arrayBuffer = this.response;
	        if (arrayBuffer) {
	        	document.getElementById("errordisplay").innerText = "Fetched: " + uri;
	            resolve(arrayBuffer);
	        } else {
	        	reject(new Error(this.statusText));
	        }
	    };
	    req.onerror = function() {
	    	reject(new Error("Network error"));
	    }
	    req.send(null);
	});
}

function CreatePDF() {
	defaultUris = [
		'icons/net.png',
		'icons/mail.png',
		'icons/skype.png',
		'icons/yt.png',
		'icons/in.png',
		'icons/gh.png',
		'icons/sc.png'
	];
	loadAssets = [
		loadUri('fonts/OpenSans-Regular.ttf'),
		loadUri('fonts/OpenSans-Bold.ttf'),
		loadUri('fonts/OpenSans-Italic.ttf'),
		loadUri('fonts/OpenSans-BoldItalic.ttf'),
	]
	assetIndices = {};
	nextIndex = loadAssets.length;
	socialIconIndex = nextIndex;
	defaultUris.forEach(function(uri) {
		loadAssets[nextIndex] = loadUri(uri);
		assetIndices[uri] = nextIndex;
		nextIndex++;
	});
	page.forEach(function(pageItem) {
		if (pageItem.type === "img") {
			uri = pageItem.content;
			if (!assetIndices.hasOwnProperty(uri)) {
				loadAssets[nextIndex] = loadUri(uri);
				assetIndices[uri] = nextIndex;
				nextIndex++;
			}
		}
	});
	document.getElementById("errordisplay").innerText = "Fetching assets...";
	document.getElementById("errordisplay").className = "pdfErrorDisplay";
	document.getElementById("pdfd_init").className = "hide";
	Promise.all(loadAssets).then(function(assetArray) {
		return new Promise(function(resolve, reject) {
			document.getElementById("errordisplay").innerText = "Generating PDF...";
			pdfworker = new Worker("js/renderpdf.js");
			pdfworker.postMessage({"page":page, "assetIndices":assetIndices, "socialIconIndex":socialIconIndex, "assetArray":assetArray}, assetArray);
			pdfworker.onmessage = function (ev) {
				if (ev.data[0]) {
					document.getElementById("errordisplay").className = "hide";
					link = document.getElementById("pdfd");
					link.href = ev.data[1];
					link.className = "pdfDownload";
					link.click();
					resolve(0);
				} else {
					reject(new Error(ev.data[1]));
				}
				pdfworker.terminate();
			};
		});
	}).catch(function(error) {
		document.getElementById("errordisplay").innerText = "Error when creating PDF: " + error.toString();
	});
}