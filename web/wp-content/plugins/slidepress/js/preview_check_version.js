var current_version = "SlideShowPro Player SWF <strong>" + SlidePress.swfVersion + '</strong>';
var swf_ready = false;
var parameter_defined = false;
var movie;
var intervalId;
var running_version = 'an older version of SlideShowPro Player SWF';
var running_version_number = 0;

function swfReady() {
	swf_ready = true;
	if (movie.getParameter !== undefined)
	{
		running_version_number = movie.getParameter('VERSION').replace(' (AS3)', '');
		running_version = "SlideShowPro Player SWF <strong>" + running_version_number + '</strong>';
	}
}

function getVersion() {
	if (movie != undefined && movie.PercentLoaded() == 100)
	{
		setTimeout('swfReady()', 1000);
	}
	if (swf_ready && running_version_number < SlidePress.swfVersion) {
		var warning = jQuery('<div class="warning"></div>');
		warning.html("You are using " + running_version + ". SlidePress " + SlidePress.sspVersion + " works best with " + current_version + ".")
			.css({
				width : SlidePress.sspWidth - 46 + 'px',
				fontSize : '12px',
				fontFamily : 'Arial',
				backgroundColor : '#ffdfdf',
				backgroundImage : 'url(../css/error.png)',
				backgroundPosition : '10px center',
				backgroundRepeat : 'no-repeat',
				borderBottom : '1px solid #ff0000',
				borderTop : '1px solid #ff0000',
				lineHeight : '1.5em',
				marginBottom : '10px',
				padding: '10px 10px 10px 36px'
			})
			.hide()
			.insertBefore(movie)
			.fadeIn();

		if (window.resizeBy !== undefined){
			window.resizeBy(0, warning.height() + 32);
		}
		clearInterval(intervalId);
	}
}

jQuery(function(){
	movie = jQuery('#ssp_g_' + SlidePress.galleryId)[0];
	intervalId = setInterval("getVersion()", 1500);
});