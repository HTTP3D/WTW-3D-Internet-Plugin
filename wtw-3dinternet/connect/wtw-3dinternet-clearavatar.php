<?php
global $wtwconnect;
try {
	//define('WTW_3DINTERNET_PREFIX', 'wtw_multiplayer_');
	
	/* google analytics tracking (if defined in wtw_config.php) */
	$wtwconnect->trackPageView($wtwconnect->domainurl."/connect/wtw-3dinternet-clearavatar.php");
	
	/* get values from querystring or session */
	$zuseravatarid = base64_decode($wtwconnect->getVal('a',''));
	$zinstanceid = base64_decode($wtwconnect->getVal('i',''));
	$zuserid = base64_decode($wtwconnect->getVal('d',''));
	$zcommunityid = base64_decode($wtwconnect->getVal('c',''));
	$zbuildingid = base64_decode($wtwconnect->getVal('b',''));
	
	$zfounduseravatarid = "";
	$zanonuseravatarid = "";
	$zuseruseravatarid = "";
	
	/* select useravatarid data */
	$zresults = $wtwconnect->query("
		select useravatarid 
		from ".WTW_3DINTERNET_PREFIX."useravatars 
		where instanceid='".$zinstanceid."' 
			and userid='' 
			and deleted=0 
		order by updatedate desc limit 1;");
	foreach ($zresults as $zrow) {
		$zanonuseravatarid = $zrow["useravatarid"];
	}
	$zresults = $wtwconnect->query("
		select useravatarid 
		from ".WTW_3DINTERNET_PREFIX."useravatars 
		where instanceid='".$zinstanceid."' 
			and userid='".$zuserid."' 
			and (not userid='') 
			and deleted=0 
		order by updatedate desc limit 1;");
	foreach ($zresults as $zrow) {
		$zuseruseravatarid = $zrow["useravatarid"];
	}
	if (!empty($zuserid) && isset($zuserid)) {
		if (!empty($zuseruseravatarid) && isset($zuseruseravatarid)) {
			$zfounduseravatarid = $zuseruseravatarid;
		} else {
			$zfounduseravatarid = $zanonuseravatarid;
		}
	} else {
		$zfounduseravatarid = $zanonuseravatarid;
	}

	if (!empty($zfounduseravatarid) && isset($zfounduseravatarid)) {
		$wtwconnect->query("
			delete from ".WTW_3DINTERNET_PREFIX."useravatars
			where useravatarid='".$zfounduseravatarid."'
				and instanceid='".$zinstanceid."';");
		$wtwconnect->query("
			delete from ".WTW_3DINTERNET_PREFIX."useravatarcolors
			where useravatarid='".$zfounduseravatarid."'
				and instanceid='".$zinstanceid."';");
		$wtwconnect->query("
			delete from ".WTW_3DINTERNET_PREFIX."useravataranimations
			where useravatarid='".$zfounduseravatarid."'
				and instanceid='".$zinstanceid."';");
	}

	echo $wtwconnect->addConnectHeader($wtwconnect->domainname);

	$zresponse = array('cleared'=>date("Y-m-d H:i:s"));
	echo json_encode($zresponse);	
} catch (Exception $e) {
	$wtwconnect->serror("connect-wtw-3dinternet-clearavatar.php=".$e->getMessage());
}
?>
