<?php

MAX_Dal_Delivery_Include();

function Plugin_deliveryAdRender_GoogleAnalytics_GoogleAnalytics_Delivery_postAdRender(&$code, $aBanner) {
	$conf = $GLOBALS['_MAX']['CONF']['GoogleAnalytics'];
	
	// if no GA id has been set we can't do anything
	// also if no track options are set in plugin settings, do nothing
	//
	if( ($conf['analyticid'] == '') || (!$conf['trackClick'] && !$conf['trackDisplay']) )
		return;

	$url = $aBanner['url'];		// click destination url for this banner
	$id = $conf['analyticid'];	// Google Analytics ID from plugin settings

	// text fields used as Category, Event Action, and Event Label for events sent to GA
	// these are configured in plugin settings
	//
	$categoryImpression = addslashes($conf['impressionCatName']);
	$actionImpression = addslashes($conf['impressionAction']);
	$categoryClick = addslashes($conf['clickCatName']);
	$actionClick = addslashes($conf['clickAction']);

	$label = addslashes($aBanner['name']).( ($conf['bannerSize']) ? (' '.$aBanner['width']).'x'.($aBanner['height']) : '');

	// Build $aGcode script to be inserted in postAdRender.  Load standard GA analytics.js and initializes tracker if needed.
	// If plugin is configured to track impressions, send impression GA event after initialization. 
	//
	$aGcode = "
		<script>
		if (typeof ga == 'undefined') {
			(function(i, s, o, g, r, a, m) {i['markAnalyticsObject'] = r;i[r] = i[r] || function() {(i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date(); a = s.createElement(o),m = s.getElementsByTagName(o)[0];a.async = 1;a.src = g;m.parentNode.insertBefore(a, m)})   (window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
			ga('create', '".$id."', 'auto');
		}
		";
	if ($conf['trackDisplay']) {
		$aGcode .= "ga('send', 'event', '$categoryImpression', '$actionImpression', '$label',"
			." {'transport': 'beacon', nonInteraction: true});";
	}
	$aGcode .= "</script>" ;

	// Splice onclick javascript for GA event into html link element already in $code.
	// the "GA event send" callback navigates to new page.  Using a callback assures
	// that the event will be sent before navigating away from page
	//
	if ($conf['trackClick']) {
		$target = get_string_between($code, 'target=\'', '\'');
		$search = 'target=';
		$replace = "onclick=\""
			." ga('send','event','$categoryClick','$actionClick','$label',"
			."{'transport':'beacon','hitCallback':function(){window.open('$url',target='$target');}});"
			."return false; \" "
			."target=";
		
		$code = str_replace($search, $replace, $code);
	}

	$code = $code.$aGcode; 
}

function get_string_between($string, $start, $end) {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
