new Promise(function(resolve, reject) {
onmessage = function(event) {
	resolve(event.data);
}
}).then(function(imports) {
	importScripts("pdfkit.js", "blob-stream.js");
	try {
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
		
		assetArray = imports.assetArray;
		assetIndices = imports.assetIndices;
		socialIconIndex = imports.socialIconIndex;
		page = imports.page;
	
		pageWidth = 210 * mm;
		pageHeight = 297 * mm;
		margin = 30 * mm;
		topMargin = 15 * mm;
		headerGap = 10 * mm;
		
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
		
		headerSize = socialMediaY + socialMediaDY * links.length + 8 * mm;
				
		pdf = new PDFDocument({
		size:[pageWidth,pageHeight],
		margins:{
			top:margin,
			left:margin,
			right:margin,
			bottom:margin},
		autoFirstPage: false});
		footerStartX = margin + 5 * mm;
		footerY = pageHeight - margin + 5 * mm;
		footerSpacing = 15 * mm;
		stream = pdf.pipe(blobStream());
		
		pdf.registerFont('F_Regular', assetArray[0]);
		pdf.registerFont('F_Bold', assetArray[1]);
		pdf.registerFont('F_Italic', assetArray[2]);
		pdf.registerFont('F_BoldItalic', assetArray[3]);
		
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
		pdf.font('F_Regular').fillColor("black");
		pdf.x = margin;
		pdf.y = headerSize + headerGap;
		
		page.forEach(function(pageItem) {
			console.log(pageItem);
		});
		
		pdf.end();
		stream.on("finish", function() {
			postMessage([true, stream.toBlobURL('application/pdf')]);
		});
	} catch (err) {
		postMessage([false, err.toString()]);
		throw err;
	}
});
