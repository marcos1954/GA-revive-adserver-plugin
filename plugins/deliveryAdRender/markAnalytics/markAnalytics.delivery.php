<?php

MAX_Dal_Delivery_Include();

function Plugin_deliveryAdRender_markAnalytics_markAnalytics_Delivery_postAdRender(&$code, $aBanner)
{
	$base = $GLOBALS['_MAX']['CONF']['markAnalytics'];
	if( ($base['analyticid'] == '') || (!$base['trackClick'] && !$base['trackDisplay']))
		return;
	
	$url = $aBanner['url'];
	$id = $base['analyticid'];

	// text fields used as Category, Event Action, and Event Label for events sent to GA
	// these are configured in plugin settings
	//
	$categoryImpression = addslashes($base['impressionCatName']);
	$actionImpression = addslashes($base['impressionAction']);
	
	$categoryClick = addslashes($base['clickCatName']);
	$actionClick = addslashes($base['clickAction']);
	
	$label = addslashes($aBanner['name']).( ($base['bannerSize']) ? (' '.$aBanner['width']).'x'.($aBanner['height']) : '');

	// script to be inserted in postAdRender.  Loads analytics.js and initializes tracker if needed.
	// if plugin is configured to track impressions, sends impression GA event after initialization. 
	//
	$aGcode = "
		<script>
		if (typeof ga == 'undefined') {
		(function(i, s, o, g, r, a, m) {i['markAnalyticsObject'] = r;i[r] = i[r] || function() {(i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date(); a = s.createElement(o),m = s.getElementsByTagName(o)[0];a.async = 1;a.src = g;m.parentNode.insertBefore(a, m)})   (window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
		ga('create', '".$id."', 'auto');
		}
		";
	if ($base['trackDisplay']) {
		$aGcode .= "ga('send', 'event', '$categoryImpression', '$actionImpression', '$label', {'transport': 'beacon', nonInteraction: true});";
	}
	$aGcode .= "</script>" ;

	// splice onclick javascript for ga event into anchor html element already in $code.
	// the GA callback navigates to new page.  using this callback assures event will be sent before
	// navigating to banner click url
	//
	if ($base['trackClick'])
	{
		$search = 'target=';
		$replace = "onclick=\" ga('send', 'event', '$categoryClick', '$actionClick', '$label',{'transport':'beacon','hitCallback':function(){window.open('$url',target='_blank');}}); return false; \"   target=";
		$code = str_replace($search, $replace, $code);
	}
	
	$code = $code.$aGcode; 
}
