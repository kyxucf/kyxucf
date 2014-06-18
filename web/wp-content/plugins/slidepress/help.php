<div class="wrap">
<!-- <span class="close-button" onclick="jQuery(document).trigger('close.facebox');">[close]</span> -->
<?php 
switch ($_GET['topic']):
	case 'ssp_crossDomain' : ?>
		<h2>Cross-Domain Configuration</h2> 
		
		<p>The Flash Player has a security wall that prevents the loading of data from a domain that is different than the one where a SWF is embedded. This also applies to subdomains. For example, if a SWF is embedded in an HTML document that is loaded using "a.com" but data is requested from "www.a.com", the Flash Player will not load the data unless it has permission to do so.</p>
		
		<p>So how is permission granted? It's handled with a "cross-domain policy file", which is a small XML document placed in the root of a domain (where the Flash Player looks for it by default). To prevent potential cross-domain errors, SlidePress automatically created a crossdomain.xml file in the root of your web site when the plugin was activated. The file includes both the non-"www" and "www" variants of this site's domain, for example:
		  </p>
		  
        <pre>
mydomain.com
www.mydomain.com</pre>
		
		<p>
		    These defaults should cover nearly everyone using SlidePress. If for some reason you need to allow other domains besides the default, enter one on each line using a format like the above.
		</p>
    
		<p>
			If you plan on publishing slideshows that request data from a completely different domain, that site will need a crossdomain.xml file in its root that grants permission to this site. See <a href="http://wiki.slideshowpro.net/SSPfl/CP-Crossdomain" title="Crossdomain" target="_blank">these cross-domain instructions</a> for assistance in creating that file.  
			</p>
     
	<?php break;?>
	
	<?php case 'xmlFilePath' : ?>
		<h2>XML Source</h2>
		<p>SlideShowPro accepts incoming XML image data from a variety of sources.</p>
        
        <p><strong>Default -</strong> Feed from a static XML file created in a text editor, uploaded to server.</p>

        <p><strong>Director -</strong> Dynamic XML file created created by SlideShowPro Director.  The XML to be used for SlidePress can be found in SlideShowPro Director, either from the landing page dashboard or from the album section.  Click the 'copy' button which copies the XML path to your clipboard for use in the SlidePress plugin.</p>

        <p><strong>Media RSS -</strong> Feed from a Flickr API or other RSS 2.0 media source.<br />
        	<em>Note for Flickr photostreams:</em> Make sure to have <code>&format=rss_200</code> at the end of the feed URL so that SSP can correctly process the feed.</p>

        <p><strong>OMPL -</strong> Allows loading of multiple XML files. For more information, visit the <a target="_blank" href="http://wiki.slideshowpro.net/SSPfl/UG-LoadingOPML">SlideShowPro Support Wiki</a>.</p>

        <p><strong>Manual Entry -</strong> Gives users the opportunity to create XML data from within the SlidePress interface.  Visit the <a target="_blank" href="http://wiki.slideshowpro.net/SSPfl/UG-CreatingXML">SlideShowPro Support Wiki</a> for information on proper usage and formatting.</p>        

        <p><strong>WordPress Gallery -</strong> Upload image content directly through WordPress posts.  Use the Add Images feature and create a gallery based on all images in a particular post.  Use the WordPress post ID as the gallery source, thumbnail generation is activated by default to create smaller images for gallery previews.</p>
        
        <!---Added Single Content to Director Help CCR ---> 
         <p><strong>Single Content -</strong> Allows you to assign an absolute URL directly to an image or video as the value of XML File Path (instead of assigning an XML file) <em>1.9.8.4 only</em>.</p>        
        
	<?php break;?>
	
	<?php case 'ssp_purgeUponDeactivation' : ?>
		<h2>SlidePress Deactivation</h2>
		<p>Selecting this option will remove all data from mysql tables.  Any existing SlidePress gallery and configuration information will be lost.</p>
	<?php break;?>
	
	<?php default: ?>
		<h2>Invalid help request.</h2>
	<?php break; ?>
<?php endswitch;?>
</div>