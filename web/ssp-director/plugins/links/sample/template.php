<?php
	
	/*
		This is a sample link template for SlideShowPro Director.
		To create your own link template, make a copy of this file
		and give it a unique name.
		
		This example shows the template for links opening the original
		image in the same browser window.
	*/
	
	/*
		The displayName variable is the label used to identify
		this template in the dropdown menu.
	*/
	$displayName = 'Open original image in same browser window';

	/*
		The template variable is the actual link template to create.
		The following variables are available to use:
		
		[full_hr_url] = The full, absolute link to the highest resolution copy
		available for the image. e.g. http://www.myhost.com/ssp_director/albums/album-1/hr/myimage.jpg
		
		[width:n,height:n,crop:n,quality:n,sharpening:n] = An optimized copy of the image returned
		by Director's on-demand image publishing system. 
		
		Example: [width:800,height:600,crop:0,quality:95,sharpening:1]
		The above will return the URL for an 800x600 image, proportionally scaled, with a 
		quality setting of 95 and sharpening factor of 1.
		
		[album_name] = The album name that the image belongs to.
		
		[img_src] = the filename of the image (e.g. myimage.jpg)
		
		[img_title] = the currently assigned title of the image
		
		[img_caption] = the currently assigned caption of the image
		
		[img_w] = the original width of the image in pixels (e.g. 400)
		
		[img_h] = the original height of the image in pixels (e.g. 300)
	*/
	$template = '[full_hr_url]';
	
	/*
		The target variable defines whether the link will open in a new window (0) or the same browser window as the slideshow (1).
		Templates that utilize javascript are not affected by this parameter (you can just set it to 0).
	*/
	$target = 1;

?>