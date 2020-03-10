<?php
class wtw3dinternet {
	protected static $_instance = null;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		global $wtwplugins;
		try {
			$this->defineConstants();
			$this->initClass();
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-construct=".$e->getMessage());
		}
	}	
	
	public $version = "1.0.0";

	public $dbversion = "1.0.0";
	
	public function __call ($method, $arguments)  {
		if (isset($this->$method)) {
			call_user_func_array($this->$method, array_merge(array(&$this), $arguments));
		}
	}
	
	private function define($name, $value) {
		global $wtwplugins;
		try {
			if (!defined($name)) {
				define($name, $value);
			}
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-define=".$e->getMessage());
		}
	}

	public function defineConstants() {
		global $wtwplugins;
		try {
			$this->define('WTW_3DINTERNET_PLUGIN', basename(strtolower(WTW_3DINTERNET_FILE),".php"));
			$this->define('WTW_3DINTERNET_PATH', dirname(WTW_3DINTERNET_FILE));
			$this->define('WTW_3DINTERNET_URL', $wtwplugins->contenturl.'/plugins/' . WTW_3DINTERNET_PLUGIN);
			$this->define('WTW_3DINTERNET_PREFIX', str_replace("wtw_wtw-","wtw_",wtw_tableprefix . WTW_3DINTERNET_PLUGIN)."_");
			$this->define('WTW_3DINTERNET_VERSION', $this->version);
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-defineConstants=".$e->getMessage());
		}
	}

	public function initClass() {
		global $wtwplugins;
		try {
			$this->initAdminOnlyHooks();
			$this->initHooks();
			$this->checkTablesForUpdates();
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-initClass=".$e->getMessage());
		}
	}
	
	public function initAdminOnlyHooks() {
		global $wtwplugins;
		try {
			/* Admin only hooks */
			if ($wtwplugins->pagename == "admin.php") {
				/* add admin menu items */
				/* wtwplugins class -> addAdminMenuItem function (menu item id, menu text, level 1 sort, level 1 id, level 2 sort, level 2 id, level 1 icon, allowed roles array - null for all, onclick JavaScript function) */
				/* $wtwplugins->addAdminMenuItem('wtw_adminpaintball', '3D Stores', 95, 'wtw_paintball', 0, '', wtw3dinternet_URL.'/assets/images/menustore.png', array('admin','developer','architect'), null); */
				/* $wtwplugins->addAdminMenuItem('wtw_adminliststores', 'List Stores', 95, 'wtw_paintball', 1, 'wtw_liststores', '', array('admin','developer','architect'), "WTW.openFullPageForm('fullpage','List Stores','wtw_liststorespage');wtw3dinternet.getStores();"); */
				
				$wtwplugins->addAdminMenuItem('wtw_3dinternetmenu', '3D Internet', -1, 'wtw_3dinternetmenu', 0, '', '/content/plugins/wtw-3dinternet/assets/images/menuworld.png', array('admin','developer'), null);
				$wtwplugins->addAdminMenuItem('wtw_3dinternetsettings', 'Control Panel', -1, 'wtw_3dinternetmenu', 1, 'wtw_3dinternetsettings', '', array('admin','developer'), "WTW.openFullPageForm('fullpage','3D Internet','wtw_3dinternetsettingspage');");

				/* admin full page settings forms */
				/* wtwplugins class -> addFullPageForm function (form id, allowed roles array - null for all, form html string) */
				$wtwplugins->addFullPageForm('wtw_3dinternetsettingspage', array('admin','developer'), $this->admin3dInternetSettingsForm());
				
			}
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-initAdminOnlyHooks=".$e->getMessage());
		}
	}	
	
	public function initHooks() {
		global $wtwplugins;
		try {
			/* Browse and Admin hooks  (admin inherrits all browse functions) */
			/* css stylesheets */
			/* wtwplugins class -> addStylesheet function (stylesheet id, '1' for admin only, stylesheet url) */
			$wtwplugins->addStylesheet('wtw-3dinternet-style-css', null, WTW_3DINTERNET_URL . "/styles/style.css");

			/* javascripts */
			/* wtwplugins class -> addScript function (script id, '1' for admin only, script browse url) */
//			$wtwplugins->addScript('wtw-3dinternet-socket-io', null, WTW_3DINTERNET_URL . "/scripts/socket.io.js");
			$wtwplugins->addScript('wtw-3dinternet-script', null, WTW_3DINTERNET_URL . "/scripts/class_main.js");
			$wtwplugins->addScript('wtw-3dinternet-chat', null, WTW_3DINTERNET_URL . "/scripts/chat.js");
			$wtwplugins->addScript('wtw-3dinternet-move', null, WTW_3DINTERNET_URL . "/scripts/move.js");
//			$wtwplugins->addScript('wtw-3dinternet-moldsscript', null, WTW_3DINTERNET_URL . "/scripts/custom_molds.js");
//			$wtwplugins->addScript('wtw-3dinternet-actionzonesscript', null, WTW_3DINTERNET_URL . "/scripts/custom_actionzones.js");
//			$wtwplugins->addScript('wtw-3dinternet-coveringsscript', null, WTW_3DINTERNET_URL . "/scripts/custom_coverings.js");
			
			/* browse menu (bottom) settings Menu Items */
			/* wtwplugins class -> addSettingsMenuItem function (menu item id, menu text, sort order, level 1 id, level 1 icon, allowed roles array - null for all, onclick JavaScript function) */
			$wtwplugins->addSettingsMenuItem("wtw_3dinternetmultiplayer", "Multiplayer Settings", 50, "wtw_3dinternetmultiplayer", "/content/system/images/menumultiplayer.png", null, "WTW.showSettingsMenu('wtw_3dinternetmuliplayerform');");

			/* browse menu (bottom) settings Menu Forms */
			/* wtwplugins class-> addMenuForm function (form id, title text, form html string, allow roles array - null for all, cssclass) */
			$wtwplugins->addMenuForm("wtw_3dinternetmuliplayerform", "Multiplayer Settings", $this->_3dInternetSettingsForm(), null, 'wtw-slideupmenuright');
			$wtwplugins->addMenuForm("wtw_menuchat", "Chat", $this->_3dInternetChatForm(), null, 'wtw-slideupmenuleft');


			/* hook plugin script functions into existing wtw functions */
			/* $wtwplugins->addScriptFunction('hookname', 'function(parameters);'); */

			$wtwplugins->addScriptFunction("myavataranimationsloaded", "wtw3dinternet.activateMultiplayer();");
			$wtwplugins->addScriptFunction("setupmodeclosed", "wtw3dinternet.activateMultiplayer();");
			$wtwplugins->addScriptFunction("savedavatarretrieved", "wtw3dinternet.activateMultiplayer();");

			$wtwplugins->addScriptFunction("avatarbeforecreate", "wtw3dinternet.showAvatarIDs(avatarname, avatardef);");
			$wtwplugins->addScriptFunction("checkactionzonetrigger", "wtw3dinternet.multiPersonInActionZone(zactionzone);");

			$wtwplugins->addScriptFunction("pluginsloadusersettingsafterengine", "wtw3dinternet.loadUserSettingsAfterEngine();"); 

			$wtwplugins->addScriptFunction("loadusersettings", "wtw3dinternet.loadUserSettings();");

			$wtwplugins->addScriptFunction("moveavatar", "wtw3dinternet.moveAvatar(zavatar, zmoveevents);");
			
			$wtwplugins->addScriptFunction("closemenus", "wtw3dinternet.closeMenus(zmenuid);");
			$wtwplugins->addScriptFunction("onunload", "wtw3dinternet.onUnload();");
			
			$wtwplugins->addScriptFunction("onclick", "wtw3dinternet.onClick(pickedname);");
			$wtwplugins->addScriptFunction("checkhovers", "wtw3dinternet.checkHovers(moldname, shape);");
			$wtwplugins->addScriptFunction("resethovers", "wtw3dinternet.resetHovers(moldname, shape);");

			/* examples: */
			/* $wtwplugins->addScriptFunction("setnewactionzonedefaults", "wtw3dinternet.setNewActionZoneDefaults(actionzonetype);"); */
			/* $wtwplugins->addScriptFunction("setactionzoneformfields", "wtw3dinternet.setNewActionZoneFormFields(actionzonetype);"); */
			/* $wtwplugins->addScriptFunction("checkactionzone", "wtw3dinternet.checkActionZone(zactionzonename, zactionzoneind, zmeinzone, zothersinzone);"); */
			/* $wtwplugins->addScriptFunction("setavatarmovement", "wtw3dinternet.setAvatarMovement(zavatar, zkey, zweight);"); */
			/* $wtwplugins->addScriptFunction("disposeclean", "wtw3dinternet.disposeClean(moldname);"); */
			
			
			/* Custom Molds (meshes) */
			/* The following create the list of new molds added by this plugin and assign the script to create the mold */
			/* $wtwplugins->addMoldDef("My Custom Mold - NAME FOR THE LIST", "webmold or mold - LIST", "wtw3dinternet.functionname(passed, values);"); */
//			$wtwplugins->addMoldDef("My Custom Mold", "webmold", "wtw3dinternet.addMoldMyCustomMold(moldname, molddef, lenx, leny, lenz);");
			/* Set the custom mold defaults and show-hide form fields as needed */
//			$wtwplugins->addScriptFunction("setnewmolddefaults", "wtw3dinternet.setNewMoldDefaults(shape, positionX, positionY, positionZ, rotationY);");
//			$wtwplugins->addScriptFunction("setmoldformfields", "wtw3dinternet.setMoldFormFields(shape);");

			/* Custom action zones */
			/* The following create the list of new action zones added by this plugin and assign the script to create the action zone */
//			$wtwplugins->addActionZoneDef("My Custom Zone", "wtw3dinternet.addActionZoneMyCustomZone(actionzonename, actionzoneind, actionzonedef);");
			/* Set the custom action zone defaults and show-hide form fields as needed */
//			$wtwplugins->addScriptFunction("setnewactionzonedefaults", "wtw3dinternet.setNewActionZoneDefaults(actionzonetype);");
//			$wtwplugins->addScriptFunction("setactionzoneformfields", "wtw3dinternet.setActionZoneFormFields(actionzonetype);");
			
			/* Custom coverings (materials) */
			/* The following create the list of new coverings added by this plugin and assign the script to create the covering */
//			$wtwplugins->addCoveringDef("My Custom Covering", "wtw3dinternet.addCoveringMyCustomCovering(moldname, molddef, lenx, leny, lenz, special1, special2);");
			/* Set the custom covering defaults and show-hide mold form fields as needed */
//			$wtwplugins->addScriptFunction("setcoveringformfields", "wtw3dinternet.setCoveringFormFields(coveringname);");
			
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php.php-initHooks=".$e->getMessage());
		}
	}	


	public function _3dInternetChatForm() {
		$zformdata = "";
		try {
			$zformdata .= "	<div id=\"wtw_menuchatmaxdiv\">\r\n";
			$zformdata .= "	<div id=\"wtw_menuchatscroll\" class=\"wtw-mainmenuscroll\">\r\n";
			$zformdata .= "		<div id=\"wtw_chatsendrequests\"></div>\r\n";
			$zformdata .= "	</div>\r\n";
			$zformdata .= "	</div>\r\n";
		} catch (Exception $e) {
			$wtwplugins->serror("plugins-3dinternet.php-_3dInternetChatForm=".$e->getMessage());
		}
		return $zformdata;
	}

	public function admin3dInternetSettingsForm() {
		global $wtwplugins;
		$zformdata = "";
		try {
			$zformdata .= "<div class=\"wtw-dashboardboxleftfull\">\r\n";
			$zformdata .= "	<div class=\"wtw-dashboardboxtitle\">Control Panel - Server-wide Settings</div>\r\n";
			$zformdata .= "		<div class=\"wtw-dashboardbox\">\r\n";

			$zformdata .= "			<div class=\"wtw3dinternet-controlpaneldiv\">\r\n";
			$zformdata .= "				<div class=\"wtw3dinternet-controlpaneltitlediv\">Multiplayer Settings</div>\r\n";
			$zformdata .= "				<label class=\"wtw3dinternet-switch\"><input id=\"wtw3dinternet_enablemultiplayer\" type=\"checkbox\" onclick=\"wtw3dinternet.changeSwitch(this);\"><span class=\"wtw3dinternet-slider wtw3dinternet-round\"></span></label><div id=\"wtw3dinternet_enablemultiplayertext\" class=\"wtw3dinternet-disabledlabel\">Multiplayer Disabled</div><br /><br />\r\n";
			$zformdata .= "				<label class=\"wtw3dinternet-switch\"><input id=\"wtw3dinternet_enablechat\" type=\"checkbox\" onclick=\"wtw3dinternet.changeSwitch(this);\"><span class=\"wtw3dinternet-slider wtw3dinternet-round\"></span></label><div id=\"wtw3dinternet_enablechattext\" class=\"wtw3dinternet-disabledlabel\">Multiplayer Chat Disabled</div><br />\r\n";
			$zformdata .= "			</div>\r\n";

			$zformdata .= "			<div class=\"wtw3dinternet-controlpaneldiv\">\r\n";
			$zformdata .= "				<div class=\"wtw3dinternet-controlpaneltitlediv\">Franchising Settings</div>\r\n";
			$zformdata .= "				<label class=\"wtw3dinternet-switch\"><input id=\"wtw3dinternet_enablefranchisebuildings\" type=\"checkbox\" onclick=\"wtw3dinternet.changeSwitch(this);\"><span class=\"wtw3dinternet-slider wtw3dinternet-round\"></span></label><div id=\"wtw3dinternet_enablefranchisebuildingstext\" class=\"wtw3dinternet-disabledlabel\">3D Buildings Franchising Disabled</div><br />\r\n";
			$zformdata .= "			</div>\r\n";

			$zformdata .= "WTWMULTIPLAYER_PATH=".WTW_3DINTERNET_PATH."<br />";
			$zformdata .= "WTWMULTIPLAYER_PLUGIN=".WTW_3DINTERNET_PLUGIN."<br />";
			$zformdata .= "WTWMULTIPLAYER_VERSION=".WTW_3DINTERNET_VERSION."<br />";
			$zformdata .= "WTWMULTIPLAYER_URL=".WTW_3DINTERNET_URL."<br />";
			$zformdata .= "WTW_3DINTERNET_PREFIX=".WTW_3DINTERNET_PREFIX."<br />";

			$zformdata .= "		</div>\r\n";
			$zformdata .= "	</div>\r\n";
			$zformdata .= "</div>\r\n";
		} catch (Exception $e) {
			$wtwplugins->serror("plugins-3dinternet.php-admin3dInternetSettingsForm=".$e->getMessage());
		}
		return $zformdata;
	}
		
	public function _3dInternetSettingsForm() {
		$zformdata = "";
		try {
			$zformdata .= "<div id=\"wtw_multiplayernote\" class=\"wtw-menunote\" style=\"display:none;visibility:hidden;\">Multi-Player will allow you to see other users' avatars Walk around in the 3D Community you are viewing.<br /><br />";
			$zformdata .= "	Works best if you have a fast Internet connection and quality graphics processor.<br /><br />";
			$zformdata .= "	If the animation gets too slow, lower the number of Avatars (closest show first) or turn this off.</div>";
			$zformdata .= "<ul class=\"wtw-menuli\">";
			$zformdata .= "	<li class=\"wtw-menuliholder\">";
			$zformdata .= "		<img src=\"/content/system/images/menuq.png\" alt=\"Show Help\" title=\"Show Help\" class='wtw-menuq' onclick=\"WTW.toggle('wtw_multiplayernote');\" />";
			$zformdata .= "		<img src=\"/content/system/images/menumaxavatars.png\" alt=\"Number of Avatars\" title=\"Number of Avatars\" class='wtw-menulefticon' />Max Number of Avatars</li>";
			$zformdata .= "	<li class=\"wtw-submenuli\">";
			$zformdata .= "		<input type=\"button\" class=\"wtw-smallprint\" value=\"-1\" onmousedown=\"WTW.changeNumberValue('wtw_tavatarcount', -1); return (false);\" onmouseup=\"WTW.changeStop();if (WTW.isNumeric(dGet('wtw_tavatarcount').value)) {WTW.multiPerson=Number(dGet('wtw_tavatarcount').value);WTW.setCookie('multiperson',WTW.multiPerson,30);}\" style=\"cursor: pointer;\" />";
			$zformdata .= "		<input type=\"text\" id=\"wtw_tavatarcount\" maxlength=\"16\" class=\"wtw-smallprintinput\" onclick=\"WTW.checkKey(this, 'number', 0, 0);\" onkeyup=\"WTW.checkKey(this, 'number', 0, 0);\" onblur=\"WTW.checkKey(this, 'number', 0, 1);if (WTW.isNumeric(dGet('wtw_tavatarcount').value)) {WTW.multiPerson=Number(dGet('wtw_tavatarcount').value);WTW.setCookie('multiperson',WTW.multiPerson,30);}\" style=\"text-align:center;background-color:#111111;color:#ffffff;\" />";
			$zformdata .= "		<input type=\"button\" class=\"wtw-smallprint\" value=\"+1\" onmousedown=\"WTW.changeNumberValue('wtw_tavatarcount', 1); return (false);\" onmouseup=\"WTW.changeStop();if (WTW.isNumeric(dGet('wtw_tavatarcount').value)) {WTW.multiPerson=Number(dGet('wtw_tavatarcount').value);WTW.setCookie('multiperson',WTW.multiPerson,30);}\" style=\"cursor: pointer;\" />";
			$zformdata .= "	</li>";
			$zformdata .= "</ul>";
			$zformdata .= "<ul class=\"wtw-menuli\">";
			$zformdata .= "	<li class=\"wtw-menuli\" onclick=\"wtw3dinternet.toggleAvatarIDs();\"><img id=\"wtw_submenuavatarids\" src=\"/content/system/images/menuavataridson.png\" alt=\"Turn Avatar IDs Off\" title=\"Turn Avatar IDs Off\" class='wtw-menulefticon' /><span id=\"wtw_submenuavataridstext\">Avatar IDs are On</span></li>";
			$zformdata .= "	<li class=\"wtw-menuli\" onclick=\"wtw3dinternet.toggleMultiPlayer();\"><img id=\"wtw_submenumultiplayer\" src=\"/content/system/images/menumultiplayer.png\" alt=\"Turn Multi-Player Off\" title=\"Turn Multi-Player Off\" class='wtw-menulefticon' /><span id=\"wtw_submenumultiplayertext\">Multi-Player is On</span></li>";
			$zformdata .= "</ul>";
			$zformdata .= "<div id=\"participantsMessage\"></div>";
		} catch (Exception $e) {
			$wtwplugins->serror("plugins-3dinternet.php-_3dInternetSettingsForm=".$e->getMessage());
		}
		return $zformdata;
	}
	


	
	public function checkTablesForUpdates() {
		/* Table definitions for plugin - used for new installs and updates */
		global $wtwplugins;
		try {
			/* to implement a table change or addition make the changes below */
			/* then update the $this->dbversion variable at the top of this file */
			/* deltaCreateTable will add, alter, or remove fields or add the table if it doesnt exist */
			/* check core/functions/class_wtwdb.php deltaCreateTable function for full support */
			if ($wtwplugins->pagename == "admin.php") {
				$dbversion = $wtwplugins->getSetting(WTW_3DINTERNET_PREFIX."dbversion");
				if ($dbversion != $this->dbversion) {
					$wtwplugins->deltaCreateTable("
						CREATE TABLE `".WTW_3DINTERNET_PREFIX."useravatars` (
						  `useravatarid` varchar(45) NOT NULL,
						  `userid` varchar(16) DEFAULT '',
						  `userip` varchar(64) DEFAULT '',
						  `instanceid` varchar(45) DEFAULT '',
						  `avatarind` int(11) DEFAULT '1',
						  `objectfolder` varchar(256) DEFAULT '',
						  `objectfile` varchar(256) DEFAULT '',
						  `domain` varchar(256) DEFAULT '3d.walktheweb.com',
						  `secureprotocol` int(11) DEFAULT '1',
						  `scalingx` decimal(18,2) DEFAULT '1.00',
						  `scalingy` decimal(18,2) DEFAULT '1.00',
						  `scalingz` decimal(18,2) DEFAULT '1.00',
						  `displayname` varchar(45) DEFAULT '',
						  `privacy` int(11) DEFAULT '0',
						  `enteranimation` int(11) DEFAULT '0',
						  `exitanimation` int(11) DEFAULT '0',
						  `enteranimationparameter` varchar(255) DEFAULT '',
						  `exitanimationparameter` varchar(255) DEFAULT '',
						  `walkspeed` decimal(18,2) DEFAULT '1.00',
						  `walkanimationspeed` decimal(18,2) DEFAULT '1.00',
						  `turnspeed` decimal(18,2) DEFAULT '1.00',
						  `turnanimationspeed` decimal(18,2) DEFAULT '1.00',
						  `lastdate` datetime DEFAULT NULL,
						  `lastip` varchar(45) DEFAULT '',
						  `createdate` datetime DEFAULT NULL,
						  `createuserid` varchar(16) DEFAULT '',
						  `updatedate` datetime DEFAULT NULL,
						  `updateuserid` varchar(16) DEFAULT '',
						  `deleteddate` datetime DEFAULT NULL,
						  `deleteduserid` varchar(16) DEFAULT '',
						  `deleted` int(11) DEFAULT '0',
						  PRIMARY KEY (`useravatarid`),
						  UNIQUE KEY `".WTW_3DINTERNET_PREFIX."useravatarid_UNIQUE` (`useravatarid`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					");
					$wtwplugins->deltaCreateTable("
						CREATE TABLE `".WTW_3DINTERNET_PREFIX."useravatarcolors` (
						  `avatarpartid` varchar(40) NOT NULL,
						  `userid` varchar(16) DEFAULT '',
						  `useravatarid` varchar(16) DEFAULT '',
						  `instanceid` varchar(24) DEFAULT '',
						  `avatarpart` varchar(256) DEFAULT NULL,
						  `emissivecolorr` decimal(20,18) DEFAULT '1.000000000000000000',
						  `emissivecolorg` decimal(20,18) DEFAULT '1.000000000000000000',
						  `emissivecolorb` decimal(20,18) DEFAULT '1.000000000000000000',
						  `createdate` datetime DEFAULT NULL,
						  `createuserid` varchar(16) DEFAULT '',
						  `updatedate` datetime DEFAULT NULL,
						  `updateuserid` varchar(16) DEFAULT '',
						  `deleteddate` datetime DEFAULT NULL,
						  `deleteduserid` varchar(16) DEFAULT '',
						  `deleted` int(11) DEFAULT '0',
						  PRIMARY KEY (`avatarpartid`),
						  UNIQUE KEY `".WTW_3DINTERNET_PREFIX."useravatarcolorid_UNIQUE` (`avatarpartid`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					");
					$wtwplugins->deltaCreateTable("
						CREATE TABLE `".WTW_3DINTERNET_PREFIX."useravataranimations` (
						  `useravataranimationid` varchar(40) NOT NULL,
						  `avataranimationid` varchar(16) DEFAULT NULL,
						  `useravatarid` varchar(16) DEFAULT NULL,
						  `instanceid` varchar(24) DEFAULT '',
						  `avataranimationname` varchar(45) DEFAULT '',
						  `speedratio` decimal(18,2) DEFAULT '1.00',
						  `walkspeed` decimal(18,2) DEFAULT '1.00',
						  `loadpriority` int(11) DEFAULT '0',
						  `animationfriendlyname` varchar(255) DEFAULT '',
						  `animationicon` varchar(255) DEFAULT '',
						  `objectfolder` varchar(255) DEFAULT '',
						  `objectfile` varchar(255) DEFAULT '',
						  `startframe` int(11) DEFAULT '0',
						  `endframe` int(11) DEFAULT '0',
						  `animationloop` int(11) DEFAULT '1',
						  `soundid` varchar(16) DEFAULT '',
						  `soundpath` varchar(255) DEFAULT '',
						  `soundmaxdistance` decimal(18,2) DEFAULT '100.00',
						  `createdate` datetime DEFAULT NULL,
						  `createuserid` varchar(16) DEFAULT '',
						  `updatedate` datetime DEFAULT NULL,
						  `updateuserid` varchar(16) DEFAULT '',
						  `deleteddate` datetime DEFAULT NULL,
						  `deleteduserid` varchar(16) DEFAULT '',
						  `deleted` int(11) DEFAULT '0',
						  PRIMARY KEY (`useravataranimationid`),
						  UNIQUE KEY `".WTW_3DINTERNET_PREFIX."useravataranimationid_UNIQUE` (`useravataranimationid`),
						  KEY `".WTW_3DINTERNET_PREFIX."idx_useravataranimations` (`avataranimationid`,`useravatarid`,`avataranimationname`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					");
					$wtwplugins->saveSetting(WTW_3DINTERNET_PREFIX."dbversion", $this->dbversion);
				}
			}
		} catch (Exception $e) {
			$wtwplugins->serror("plugins:wtw-3dinternet:functions-class_plugin.php-checkTablesForUpdates=".$e->getMessage());
		}
	}

}

	function wtw3dinternet() {
		return wtw3dinternet::instance();
	}

	/* Global for backwards compatibility. */
	$GLOBALS['wtw3dinternet'] = wtw3dinternet();

?>