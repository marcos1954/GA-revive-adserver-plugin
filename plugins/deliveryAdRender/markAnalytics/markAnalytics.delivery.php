<?php

MAX_Dal_Delivery_Include();

function Plugin_deliveryAdRender_markAnalytics_markAnalytics_Delivery_postAdRender(&$code, $aBanner)
{
	$base = $GLOBALS['_MAX']['CONF']['markAnalytics'];
	if( ($base['analyticid'] == 'UA-45882599-1') || (!$base['trackClick'] && !$base['trackDisplay']))
		return;
	
	$url = $aBanner['url'];
	$id = $base['analyticid'];
	$category = addslashes($base['displayCatName']);
	$actionDisplay = addslashes($base['displayAction']);
	$actionClick = addslashes($base['clickAction']);
	$label = addslashes($aBanner['name']).(($base['bannerSize'])?(' '.$aBanner['width']).'x'.($aBanner['height']):'');				

	$aGcode = "
		<script>
		(function(i, s, o, g, r, a, m) {i['markAnalyticsObject'] = r;i[r] = i[r] || function() {(i[r].q = i[r].q || []).push(arguments)}, i[r].l = 1 * new Date(); a = s.createElement(o),m = s.getElementsByTagName(o)[0];a.async = 1;a.src = g;m.parentNode.insertBefore(a, m)})   (window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
		ga('create', '".$id."', 'auto');";
		
	if ($base['trackDisplay']) {
		$aGcode .= "ga('send', 'event', '$category', '$actionDisplay', '$label', {'transport': 'beacon', nonInteraction: true});";
	}
	$aGcode .= "</script>" ;

	// add onclick ga event to anchor html element, ga callback navigates to new page
	if ($base['trackClick'])
	{
		$search = 'target=';
		$replace = "onclick=\" ga('send', 'event', '$category', '$actionClick', '$label',{'transport':'beacon','hitCallback':function(){window.open('$url',target='_blank');}}); return false; \"   target=";
		$code = str_replace($search, $replace, $code);
	}
	
	$code = $code.$aGcode; 
}
