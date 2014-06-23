<?php get_header(); ?>

<!-- Show the welcome box and slideshow only on first page.  Makes for better pagination. -->
<?php if ( $paged < 1 ) { ?>

<!-- Begin Welcome Box -->
<?php if (is_home()) include (THEMELIB . '/apps/welcomebox.php'); ?>

<!-- Begin Slideshow:  Instructions:  By default, the Slideshow section below pulls in the five photos in the images/slideshow/ folder inside your theme folder.  Replace the images with your own (950 pixels wide max, keep filenames the same). If you prefer to manage your slideshow images from within a Wordpress post, you can delete -static.php from the Slideshow section below, which would pull in the slideshow.php file instead of the slideshow-static.php file.  The slideshow.php file pulls in the latest photo uploaded using the "Add Media" button into each of the latest five posts in the category that you choose on the Homepage Settings page.  You will then need to paste the url to your slideshow images to a custom field key called "slideshow". And lastly, don't forget to set the height of the slideshow images on the Theme Options page. -->
<?php include (THEMELIB . '/apps/slideshow-static.php'); ?>

<!-- End Better Pagination -->
<?php } ?>

<!-- Begin Blog -->
<?php include (THEMELIB . '/apps/blog.php'); ?>

<!-- Begin Footer -->
<?php get_footer(); ?>