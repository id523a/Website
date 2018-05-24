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
	mm = 2.834646;
	links = [
		'mailto:edward@giles.net.au',
		'skype://edward@giles.net.au',
		'https://www.youtube.com/channel/UCdWI3Bs_d_Z89sWYJz9unXQ',
		'https://www.linkedin.com/in/id523a',
		'https://github.com/id523a',
		'https://soundcloud.com/id523a',
	];
	socialMediaTexts = [
		'edward@giles.net.au',
		'edward@giles.net.au',
		'id523a',
		'Edward Giles',
		'id523a',
		'id523a',
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
	document.getElementById("errordisplay").innerText = "Fetching assets...";
	document.getElementById("errordisplay").className = "pdfErrorDisplay";
	document.getElementById("pdfd_init").className = "hide";
	Promise.all(loadAssets).then(function(assetArray) {
		document.getElementById("errordisplay").innerText = "Generating PDF...";
		pageWidth = 210 * mm;
		pageHeight = 297 * mm;
		margin = 30 * mm;
		topMargin = 15 * mm;
		
		headerImageSize = 55 * mm;
		headerImageBorder = 1.5 * mm;
		headerImageBorderRadius = 2.5 * mm;
		headerImageX = margin + 3 * mm;
		headerImageY = topMargin + 3 * mm;
		headerFontSize = 20;
		headerColSpacing = 10 * mm;
		
		socialMediaX = headerImageX + headerImageSize + headerColSpacing;
		socialMediaY = headerImageY;
		socialMediaWidth = 65 * mm;
		socialMediaIconSize = 8 * mm;
		socialMediaPadding = 1.5 * mm;
		socialMediaHeight = socialMediaIconSize + 2 * socialMediaPadding;
		socialMediaBorderRadius = 2 * mm;
		socialMediaLeftBorder = 1.5 * mm;
		socialMediaLeftPadding = socialMediaLeftBorder + socialMediaPadding;
		socialMediaFontSize = 12;
		socialMediaSpacing = 2.5 * mm;
		socialMediaDY = socialMediaHeight + socialMediaSpacing;
		
		headerSize = socialMediaY + socialMediaDY * links.length + 10 * mm;
				
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
		
		// Draw social media buttons
		for (var i = 0; i < links.length; i++) {
			pdf.roundedRect(
			socialMediaX, socialMediaY + 0.1,
			socialMediaHeight, socialMediaHeight - 0.2,
			socialMediaBorderRadius
			).fill("darkblue");
			pdf.roundedRect(
			socialMediaX + socialMediaLeftBorder, socialMediaY,
			socialMediaWidth - socialMediaLeftBorder, socialMediaHeight,
			socialMediaBorderRadius
			).fill("white");
			iconX = socialMediaX + socialMediaLeftPadding;
			iconY = socialMediaY + socialMediaPadding;
			pdfAddImage(i + socialIconIndex, iconX, iconY, {width:socialMediaIconSize});
			pdf.font('F_Regular').fontSize(socialMediaFontSize).fillColor("black");
			textX = iconX + socialMediaIconSize + socialMediaSpacing;
			textY = iconY + 0.5 * (socialMediaIconSize - pdf.currentLineHeight());
			pdf.text(socialMediaTexts[i], textX, textY);
			pdf.link(socialMediaX, socialMediaY, socialMediaWidth, socialMediaHeight, links[i]);
			socialMediaY += socialMediaDY;
		}
		
		// Draw content
		
		pdf.end();
		stream.on("finish", function() {
			document.getElementById("errordisplay").className = "hide";
			link = document.getElementById("pdfd");
			link.href = stream.toBlobURL('application/pdf');
			link.className = "pdfDownload";
			link.click();
		});
	}).catch(function(error) {
		document.getElementById("errordisplay").innerText = "Error when creating PDF: " + error.toString();
	});
}