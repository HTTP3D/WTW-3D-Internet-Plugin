<?php
global $wtwconnect;
try {
	/* google analytics tracking (if defined in wtw_config.php) */
	$wtwconnect->trackPageView($wtwconnect->domainurl."/connect/wtw-3dinternet-updateavatar.php");
	
	/* get values from querystring or session */
	$zinstanceid = base64_decode($wtwconnect->getVal('i',''));
	$zuseravatarid = base64_decode($wtwconnect->getVal('u',''));
	$zavatarind = base64_decode($wtwconnect->getVal('ai',''));
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
			where instanceid='".$zinstanceid."' 
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
			set userid='".$zuserid."',
				 userip='".$zip."',
				 avatarind='".$zavatarind."',
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
				 userid,
				 userip,
				 avatarind,
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
				 '".$zuserid."',
				 '".$zip."',
				 '".$zavatarind."',
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
	if(ini_get('allow_url_fopen') ) {
		$avatarurl = $wtwconnect->domainurl."/connect/avatars.php?i=".base64_encode($zinstanceid)."&d=".base64_encode($zuserid)."&p=".base64_encode($zip);
		$zavatardata = file_get_contents($avatarurl);
	}
	$zavatardata = json_decode($zavatardata);
	if (isset($zavatardata->avatar->avatarparts) && !empty($zavatardata->avatar->avatarparts)) {
		$zavatarparts = $zavatardata->avatar->avatarparts;
		$zanimationdefs = $zavatardata->avatar->avataranimationdefs;

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
				$zresults = $wtwconnect->query("
					select useravataranimationid 
					from ".WTW_3DINTERNET_PREFIX."useravataranimations 
					where instanceid='".$zinstanceid."' 
						and useravatarid='".$zuseravatarid."' 
						and useravataranimationid='".$zuseravataranimationid."'
					limit 1;");
				foreach ($zresults as $zrow) {
					$zfounduseravataranimationid = $zrow["useravataranimationid"];
				}
				
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
					$wtwconnect->query("
						update ".WTW_3DINTERNET_PREFIX."useravataranimations 
						set avataranimationid = '".$zanimationdef->avataranimationid."',
							useravatarid='".$zuseravatarid."',
							avataranimationname='".$zanimationdef->animationname."',
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
					$wtwconnect->query("
						insert into ".WTW_3DINTERNET_PREFIX."useravataranimations 
						   (useravataranimationid,
							useravatarid,
							instanceid,
						    avataranimationid,
							avataranimationname,
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
							'".$zinstanceid."',
							'".$zanimationdef->avataranimationid."',
							'".$zanimationdef->animationname."',
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
		
		foreach($zavatarparts as $zavatarpart) {
			$zfoundavatarpartid = "";
			$zavatarpartid = $zavatarpart->avatarpartid;
			if (!empty($zavatarpartid) && isset($zavatarpartid)) {
				$zresults = $wtwconnect->query("
					select avatarpartid 
					from ".WTW_3DINTERNET_PREFIX."useravatarcolors 
					where instanceid='".$zinstanceid."' 
						and userid='".$wtwconnect->userid."' 
						and avatarpartid='".$zavatarpartid."'
					limit 1;");
				foreach ($zresults as $zrow) {
					$zfoundavatarpartid = $zrow["avatarpartid"];
				}
				if (!empty($zfoundavatarpartid) && isset($zfoundavatarpartid)) {
					$wtwconnect->query("
						update ".WTW_3DINTERNET_PREFIX."useravatarcolors 
						set avatarpart = '".$zavatarpart->avatarpart."',
							emissivecolorr=".$zavatarpart->emissivecolorr.",
							emissivecolorg=".$zavatarpart->emissivecolorg.",
							emissivecolorb=".$zavatarpart->emissivecolorb.",
							updatedate=now(),
							updateuserid='',
							deleteddate=null,
							deleteduserid='',
							deleted=0
						where instanceid='".$zinstanceid."' 
						and userid='".$wtwconnect->userid."' 
						and avatarpartid='".$zavatarpartid."'
					");
				} else {
					$wtwconnect->query("
						insert into ".WTW_3DINTERNET_PREFIX."useravatarcolors 
						   (avatarpartid,
						    userid,
							useravatarid,
							instanceid,
							avatarpart,
							emissivecolorr,
							emissivecolorg,
							emissivecolorb,
							createdate,
							createuserid,
							updatedate,
							updateuserid)
						values	
						   ('".$zavatarpartid."',
						    '".$wtwconnect->userid."',
							'".$zuseravatarid."',
							'".$zinstanceid."',
							'".$zavatarpart->avatarpart."',
							".$zavatarpart->emissivecolorr.",
							".$zavatarpart->emissivecolorg.",
							".$zavatarpart->emissivecolorb.",
							now(),
							'".$wtwconnect->userid."',
							now(),
							'".$wtwconnect->userid."');");
				}
			}
		}
	}
	
//	echo $wtwconnect->addConnectHeader($wtwconnect->domainname);

	$zresponse = array('updated'=>date("Y-m-d H:i:s"));

//	echo json_encode($zanimationdefs);	
} catch (Exception $e) {
	$wtwconnect->serror("connect-wtw-3dinternet-updateavatar.php=".$e->getMessage());
}
?>
