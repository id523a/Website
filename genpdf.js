function loadUri(uri) {
	return new Promise(function(resolve, reject) {
		req = new XMLHttpRequest();
	    req.open("GET", uri, true);
	    req.responseType = "arraybuffer";
	    req.onload = function() {
	        var arrayBuffer = this.response;
	        if (arrayBuffer) {
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

function appendScript(uri) {
	return new Promise(function(resolve, reject) {
		console.log("appendScript " + uri); 
		var el2 = document.createElement('script');
		el2.type = 'text/javascript';
		el2.src = uri;
		el2.onload = function() {
			resolve(el2);
		};
		document.head.appendChild(el2);
	});
}
function CreatePDF() {
	document.getElementById("errordisplay").innerText = "Fetching assets...";
	document.getElementById("errordisplay").className = "pdfErrorDisplay";
	document.getElementById("pdfd_init").className = "hide";
	mm = 2.834646;
	links = [
		'mailto:edward@giles.net.au',
		'skype:edward@giles.net.au',
		'https://www.youtube.com/channel/UCdWI3Bs_d_Z89sWYJz9unXQ',
		'https://www.linkedin.com/in/id523a',
		'https://github.com/id523a',
		'https://soundcloud.com/id523a'
	];
	defaultUris = [
		'icons/mail.png',
		'icons/skype.png',
		'icons/yt.png',
		'icons/in.png',
		'icons/gh.png',
		'icons/sc.png',
		'face.jpg',
	];
	loadAssets = [
		appendScript("lib/blob-stream.js"),
		appendScript("lib/pdfkit.js"),
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
	Promise.all(loadAssets).then(function(assetArray) {
		document.getElementById("errordisplay").innerText = "Generating PDF...";
		pageWidth = 210 * mm;
		pageHeight = 297 * mm;
		margin = 30 * mm;
		topMargin = 12 * mm;
		headerSize = topMargin + 72 * mm;
		headerImageSize = 48 * mm;
		headerImageBorder = 1.5 * mm;
		headerImageBorderRadius = 2.5 * mm;
		headerImageX = margin + 3 * mm;
		headerImageY = topMargin + 3 * mm;
		headerFontSize = 20;
		headerColSpacing = 8 * mm;
		pdf = new PDFDocument({
		size:[pageWidth,pageHeight],
		margins:{
			top:headerSize,
			left:margin,
			right:margin,
			bottom:margin},
		autoFirstPage: false});
		footerStartX = margin + 5 * mm;
		footerY = pageHeight - margin + 5 * mm;
		footerSpacing = 15 * mm;
		stream = pdf.pipe(blobStream());
		pdf.registerFont('F_Regular', assetArray[2]);
		pdf.registerFont('F_Bold', assetArray[3]);
		pdf.registerFont('F_Italic', assetArray[4]);
		pdf.registerFont('F_BoldItalic', assetArray[5]);
		function pdfAddImage(index, x, y, options) {
			if (options == null) { options = {}; }
			if (typeof index === 'string') {
				index = assetIndices[index];
			}
			options.imageRegistryIndex = index;
			pdf.image(assetArray[index], x, y, options);
		}
		pdf.on("pageAdded", function() {
			pdf.save();
			for (var i = 0; i < links.length; i++) {
				iconX = footerStartX + footerSpacing * i;
				pdfAddImage(i + socialIconIndex, iconX, footerY, {width:10*mm});
				pdf.link(iconX, footerY, 10*mm, 10*mm, links[i]);
			}
			pdf.restore();
		});
		pdf.addPage();
		// Draw header
		pdf.rect(
			0, 0, pageWidth, headerSize
		).fill("#336699");
		pdf.roundedRect(
			headerImageX, headerImageY,
			headerImageSize, headerImageSize,
			headerImageBorderRadius
		).fill("white");
		headerImageX += headerImageBorder;
		headerImageY += headerImageBorder;
		headerImageSize -= 2 * headerImageBorder;
		pdf.save();
		pdf.roundedRect(
			headerImageX, headerImageY,
			headerImageSize, headerImageSize,
			headerImageBorderRadius - headerImageBorder
		).clip();
		pdfAddImage('face.jpg', headerImageX, headerImageY, {width:headerImageSize});
		pdf.restore();
		pdf.fillColor('white').font('F_Bold').fontSize(headerFontSize)
		pdf.text('Edward Giles', headerImageX, headerImageY + headerImageSize + 2 * headerImageBorder);
		// Draw content
		stream.on("finish", function() {
			document.getElementById("errordisplay").className = "hide";
			link = document.getElementById("pdfd");
			link.href = stream.toBlobURL('application/pdf');
			link.className = "pdfDownload";
			link.click();
		});
		pdf.end();
	}).catch(function(error) {
		document.getElementById("errordisplay").innerText = "Error when creating PDF: " + error.toString();
	});
}