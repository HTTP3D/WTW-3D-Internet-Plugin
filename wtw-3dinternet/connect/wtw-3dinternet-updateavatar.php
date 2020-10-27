<?php
global $wtwconnect;
try {
	/* google analytics tracking (if defined in wtw_config.php) */
	$wtwconnect->trackPageView($wtwconnect->domainurl."/connect/wtw-3dinternet-updateavatar.php");
	
	/* get values from querystring or session */
	$zinstanceid = base64_decode($wtwconnect->getVal('i',''));
	$zuseravatarid = base64_decode($wtwconnect->getVal('u',''));
	$zglobaluseravatarid = base64_decode($wtwconnect->getVal('g',''));
	$zavatarid = base64_decode($wtwconnect->getVal('ad',''));
	$zuserid = base64_decode($wtwconnect->getVal('d',''));
	$zobjectfolder = base64_decode($wtwconnect->getVal('o',''));
	$zobjectfile = base64_decode($wtwconnect->getVal('f',''));
	$zdomain = base64_decode($wtwconnect->getVal('m',''));
	$zsecure = base64_decode($wtwconnect->getVal('s',''));
	$zscalingx = base64_decode($wtwconnect->getVal('x',''));
	$zscalingy = base64_decode($wtwconnect->getVal('y',''));
	$zscalingz = base64_decode($wtwconnect->getVal('z',''));
	$zdisplayname = base64_decode($wtwconnect->getVal('n',''));
	$zprivacy = base64_decode($wtwconnect->getVal('p',''));
	$zenteranimation = base64_decode($wtwconnect->getVal('en','1'));
	$zenteranimationparameter = base64_decode($wtwconnect->getVal('enp',''));
	$zexitanimation = base64_decode($wtwconnect->getVal('ex','1'));
	$zexitanimationparameter = base64_decode($wtwconnect->getVal('exp',''));
	$zwalkspeed = base64_decode($wtwconnect->getVal('w','1'));
	$zwalkanimationspeed = base64_decode($wtwconnect->getVal('v','1'));
	$zturnspeed = base64_decode($wtwconnect->getVal('t','1'));
	$zturnanimationspeed = base64_decode($wtwconnect->getVal('r','1'));
	$zip = base64_decode($wtwconnect->getVal('a',''));
	$zserverinstanceid = base64_decode($wtwconnect->getVal('si',''));
	$zusertoken = $wtwconnect->getVal('at','');
	$zrefresh = $wtwconnect->getVal('refresh','');

	if (is_numeric($zenteranimation) == false) {
		$zenteranimation = '1';
	}
	if (is_numeric($zexitanimation) == false) {
		$zexitanimation = '1';
	}
	if (is_numeric($zprivacy) == false) {
		$zprivacy = '0';
	}
	if (is_numeric($zscalingx) == false) {
		$zscalingx = '0.07';
	}
	if (is_numeric($zscalingy) == false) {
		$zscalingy = '0.07';
	}
	if (is_numeric($zscalingz) == false) {
		$zscalingz = '0.07';
	}

	$zfounduseravatarid = "";
	$zanonuseravatarid = "";
	$zuseruseravatarid = "";

	if (empty($zuseravatarid) || !isset($zuseravatarid)) {
		$zuseravatarid = $wtwconnect->getRandomString(16,1);
	}

	if (empty($zanonuseravatarid) || !isset($zanonuseravatarid)) {
		/* select useravatarid data */
		$zresults = $wtwconnect->query("
			select useravatarid 
			from ".WTW_3DINTERNET_PREFIX."useravatars 
			where instanceid='".$zinstanceid."' 
				and userid='' 
			order by updatedate desc limit 1;");
		foreach ($zresults as $zrow) {
			$zanonuseravatarid = $zrow["useravatarid"];
		}
	}
	if (empty($zuseruseravatarid) || !isset($zuseruseravatarid)) {
		$zresults = $wtwconnect->query("
			select useravatarid 
			from ".WTW_3DINTERNET_PREFIX."useravatars 
			where useravatarid='".$zuseravatarid."' 
				and userid='".$zuserid."' 
				and (not userid='') 
			order by updatedate desc limit 1;");
		foreach ($zresults as $zrow) {
			$zuseruseravatarid = $zrow["useravatarid"];
		}
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
			update ".WTW_3DINTERNET_PREFIX."useravatars
			set  userid='".$zuserid."',
				 userip='".$zip."',
				 globaluseravatarid='".$zglobaluseravatarid."',
				 avatarid='".$zavatarid."',
				 objectfolder='".$zobjectfolder."',
				 objectfile='".$zobjectfile."',
				 domain='".$wtwconnect->domainname."',
				 secureprotocol='".$zsecure."',
				 scalingx=".$zscalingx.",
				 scalingy=".$zscalingy.",
				 scalingz=".$zscalingz.",
				 displayname='".$zdisplayname."',
				 privacy=".$zprivacy.",
				 enteranimation=".$zenteranimation.",
				 enteranimationparameter='".$zenteranimationparameter."',
				 exitanimation=".$zexitanimation.",
				 exitanimationparameter='".$zexitanimationparameter."',
				 walkspeed=".$zwalkspeed.",
				 walkanimationspeed=".$zwalkanimationspeed.",
				 turnspeed=".$zturnspeed.",
				 turnanimationspeed=".$zturnanimationspeed.",
				 lastip='".$zip."',
				 lastdate=now(),
				 updatedate=now(),
				 updateuserid='".$wtwconnect->userid."',
				 deleteddate=null,
				 deleteduserid='',
				 deleted=0
			where useravatarid='".$zfounduseravatarid."';
		");
		$zuseravatarid = $zfounduseravatarid;
	} else {
		$wtwconnect->query("
			insert into ".WTW_3DINTERNET_PREFIX."useravatars
				(instanceid,
				 useravatarid,
				 globaluseravatarid,
				 userid,
				 userip,
				 avatarid,
				 objectfolder,
				 objectfile,
				 domain,
				 secureprotocol,
				 scalingx,
				 scalingy,
				 scalingz,
				 displayname,
				 privacy,
				 enteranimation,
				 enteranimationparameter,
				 exitanimation,
				 exitanimationparameter,
				 walkspeed,
				 walkanimationspeed,
				 turnspeed,
				 turnanimationspeed,
				 lastip,
				 lastdate,
				 createdate,
				 createuserid,
				 updatedate,
				 updateuserid)
				values
				('".$zinstanceid."',
				 '".$zuseravatarid."',
				 '".$zglobaluseravatarid."',
				 '".$zuserid."',
				 '".$zip."',
				 '".$zavatarid."',
				 '".$zobjectfolder."',
				 '".$zobjectfile."',
				 '".$wtwconnect->domainname."',
				 '".$zsecure."',
				 ".$zscalingx.",
				 ".$zscalingy.",
				 ".$zscalingz.",
				 '".$zdisplayname."',
				 ".$zprivacy.",
				 ".$zenteranimation.",
				 '".$zenteranimationparameter."',
				 ".$zexitanimation.",
				 '".$zexitanimationparameter."',
				 ".$zwalkspeed.",
				 ".$zwalkanimationspeed.",
				 ".$zturnspeed.",
				 ".$zturnanimationspeed.",
				 '".$zip."',
				 now(),
				 now(),
				 '".$wtwconnect->userid."',
				 now(),
				 '".$wtwconnect->userid."');		
		");
	}
	$zavatardata = array();
	$zavatarparts = array();
	$zanimationdefs = array();
	/* get latest user avatar settings */
	if(ini_get('allow_url_fopen') ) {
		if (!isset($zglobaluseravatarid) || empty($zglobaluseravatarid)) {
			$avatarurl = $wtwconnect->domainurl."/connect/useravatar.php?a=".base64_encode($zuseravatarid)."&i=".base64_encode($zinstanceid)."&d=".base64_encode($zuserid)."&p=".base64_encode($zip);
			$zavatardata = file_get_contents($avatarurl);
		} else {
			$avatarurl = "https://3dnet.walktheweb.com/connect/globalavatar.php?usertoken=".$zusertoken."&globaluseravatarid=".base64_encode($zglobaluseravatarid)."&serverinstanceid=".base64_encode($zserverinstanceid);
			$zavatardata = file_get_contents($avatarurl);
		}
	}
	$zavatardata = json_decode($zavatardata);
	if (isset($zavatardata->avatar->avatarparts) && !empty($zavatardata->avatar->avatarparts)) {
		/* get array of parts (meshes) for colors */
		$zavatarparts = $zavatardata->avatar->avatarparts;
		/* get array of animations */
		$zanimationdefs = $zavatardata->avatar->avataranimationdefs;
		/* cycle through the animations to update each animation in multiplyer table */
		foreach($zanimationdefs as $zanimationdef) {
			$zfounduseravataranimationid = "";
			$zuseravataranimationid = $zanimationdef->useravataranimationid;
			if (!empty($zuseravataranimationid) && isset($zuseravataranimationid)) {
				$zloadpriority = "";
				$zanimationfriendlyname = "";
				$zanimationicon = "";
				$zobjectfolder = "";
				$zobjectfile = "";
				$zstartframe = "";
				$zendframe = "";
				$zanimationloop = "";
				$zsoundid = "";
				$zsoundpath = "";
				$zsoundmaxdistance = "";
				/* check if user avatar animation exists in multiplayer table */
				$zresults = $wtwconnect->query("
					select useravataranimationid 
					from ".WTW_3DINTERNET_PREFIX."useravataranimations 
					where useravatarid='".$zuseravatarid."' 
						and useravataranimationid='".$zuseravataranimationid."'
					limit 1;");
				foreach ($zresults as $zrow) {
					$zfounduseravataranimationid = $zrow["useravataranimationid"];
				}
				/* get avatar animation details */
				$zresults = $wtwconnect->query("
					select * 
					from ".wtw_tableprefix."avataranimations 
					where avataranimationid='".$zanimationdef->avataranimationid."'
					limit 1;");
				foreach ($zresults as $zrow) {
					$zloadpriority = $zrow["loadpriority"];
					$zanimationfriendlyname = $zrow["animationfriendlyname"];
					$zanimationicon = $zrow["animationicon"];
					$zobjectfolder = $zrow["objectfolder"];
					$zobjectfile = $zrow["objectfile"];
					$zstartframe = $zrow["startframe"];
					$zendframe = $zrow["endframe"];
					$zanimationloop = $zrow["animationloop"];
					$zsoundid = $zrow["soundid"];
					$zsoundpath = $zrow["soundpath"];
					$zsoundmaxdistance = $zrow["soundmaxdistance"];
				}				
				
				if (!empty($zfounduseravataranimationid) && isset($zfounduseravataranimationid)) {
					/* if user animation was found, update it */
					$wtwconnect->query("
						update ".WTW_3DINTERNET_PREFIX."useravataranimations 
						set avataranimationid = '".$zanimationdef->avataranimationid."',
							globaluseravatarid = '".$zglobaluseravatarid."',
							useravatarid='".$zuseravatarid."',
							avataranimationevent='".$zanimationdef->animationevent."',
							speedratio=".$zanimationdef->speedratio.",
							walkspeed=".$zanimationdef->walkspeed.",
							loadpriority=".$zloadpriority.",
							animationfriendlyname='".$zanimationfriendlyname."',
							animationicon='".$zanimationicon."',
							objectfolder='".$zobjectfolder."',
							objectfile='".$zobjectfile."',
							startframe=".$zstartframe.",
							endframe=".$zendframe.",
							animationloop=".$zanimationloop.",
							soundid='".$zsoundid."',
							soundpath='".$zsoundpath."',
							soundmaxdistance='".$zsoundmaxdistance."',
							updatedate=now(),
							updateuserid='',
							deleteddate=null,
							deleteduserid='',
							deleted=0
						where instanceid='".$zinstanceid."' 
						and useravatarid='".$zuseravatarid."' 
						and useravataranimationid='".$zuseravataranimationid."'
					");
				} else {
					/* if user animation was not found, add it */
					$wtwconnect->query("
						insert into ".WTW_3DINTERNET_PREFIX."useravataranimations 
						   (useravataranimationid,
							useravatarid,
							globaluseravatarid,
							instanceid,
						    avataranimationid,
							avataranimationevent,
							speedratio,
							walkspeed,
							loadpriority,
							animationfriendlyname,
							animationicon,
							objectfolder,
							objectfile,
							startframe,
							endframe,
							animationloop,
							soundid,
							soundpath,
							soundmaxdistance,
							createdate,
							createuserid,
							updatedate,
							updateuserid)
						values	
						   ('".$zuseravataranimationid."',
						    '".$zuseravatarid."',
							'".$zglobaluseravatarid."',
							'".$zinstanceid."',
							'".$zanimationdef->avataranimationid."',
							'".$zanimationdef->animationevent."',
							".$zanimationdef->speedratio.",
							".$zanimationdef->walkspeed.",
							".$zloadpriority.",
							'".$zanimationfriendlyname."',
							'".$zanimationicon."',
							'".$zobjectfolder."',
							'".$zobjectfile."',
							".$zstartframe.",
							".$zendframe.",
							".$zanimationloop.",
							'".$zsoundid."',
							'".$zsoundpath."',
							'".$zsoundmaxdistance."',
							now(),
							'".$wtwconnect->userid."',
							now(),
							'".$wtwconnect->userid."');");
				} 
			}
		}
		/* cycle through the parts to update avatar colors in multiplayer table */
		foreach($zavatarparts as $zavatarpart) {
			$zfoundavatarpartid = "";
			$zavatarpartid = $zavatarpart->avatarpartid;
			if (!empty($zavatarpartid) && isset($zavatarpartid)) {
				/* check of part exists in multiplayer table */
				$zresults = $wtwconnect->query("
					select avatarpartid 
					from ".WTW_3DINTERNET_PREFIX."useravatarcolors 
					where useravatarid='".$zuseravatarid."' 
						and userid='".$wtwconnect->userid."' 
						and avatarpartid='".$zavatarpartid."'
					limit 1;");
				foreach ($zresults as $zrow) {
					$zfoundavatarpartid = $zrow["avatarpartid"];
				}
				if (!empty($zfoundavatarpartid) && isset($zfoundavatarpartid)) {
					/* if part found, update the colors */
					$wtwconnect->query("
						update ".WTW_3DINTERNET_PREFIX."useravatarcolors 
						set avatarpart = '".$zavatarpart->avatarpart."',
							diffusecolor='".$zavatarpart->diffusecolor."',
							specularcolor='".$zavatarpart->specularcolor."',
							emissivecolor='".$zavatarpart->emissivecolor."',
							ambientcolor='".$zavatarpart->ambientcolor."',
							globaluseravatarid='".$zglobaluseravatarid."',
							updatedate=now(),
							updateuserid='',
							deleteddate=null,
							deleteduserid='',
							deleted=0
						where useravatarid='".$zuseravatarid."' 
						and userid='".$wtwconnect->userid."' 
						and avatarpartid='".$zavatarpartid."'
					");
				} else {
					/* if part is not found, add the colors */
					$wtwconnect->query("
						insert into ".WTW_3DINTERNET_PREFIX."useravatarcolors 
						   (avatarpartid,
						    userid,
							useravatarid,
							globaluseravatarid,
							instanceid,
							avatarpart,
							diffusecolor,
							specularcolor,
							emissivecolor,
							ambientcolor,
							createdate,
							createuserid,
							updatedate,
							updateuserid)
						values	
						   ('".$zavatarpartid."',
						    '".$wtwconnect->userid."',
							'".$zuseravatarid."',
							'".$zglobaluseravatarid."',
							'".$zinstanceid."',
							'".$zavatarpart->avatarpart."',
							'".$zavatarpart->diffusecolor."',
							'".$zavatarpart->specularcolor."',
							'".$zavatarpart->emissivecolor."',
							'".$zavatarpart->ambientcolor."',
							now(),
							'".$wtwconnect->userid."',
							now(),
							'".$wtwconnect->userid."');");
				}
			}
		}
	}

	echo $wtwconnect->addConnectHeader($wtwconnect->domainname);

	$zresponse = array(
		'refresh'=> $zrefresh
	);

	echo json_encode($zresponse);	
} catch (Exception $e) {
	$wtwconnect->serror("connect-wtw-3dinternet-updateavatar.php=".$e->getMessage());
}
?>
