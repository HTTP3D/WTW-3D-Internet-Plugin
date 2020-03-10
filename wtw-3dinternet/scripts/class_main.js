// All code is Copyright 2013-2020 Aaron Scott Dishno Ed.D., HTTP3D Inc. - WalkTheWeb, and the contributors
// "3D Browsing" is a USPTO Patented (Serial # 9,940,404) and Worldwide PCT Patented Technology by Aaron Scott Dishno Ed.D. and HTTP3D Inc. 
// Read the included GNU Ver 3.0 license file for details and additional release information.

function WTW_3DINTERNET() {
	/* Add your global variables as needed here */
	this.ver = "1.0.0";
	this.masterMove = '0';
	this.masterChat = '0';
	this.masterFranchising = '0';
	this.move = null;
	this.chat = null;
	this.lastAnimations = '';
	this.avatars = [];
	this.AvatarIDs = 1;
	this.multiPlayer = 20;
	this.multiPlayerOn = 1;
	this.typingTimer = null;
}

/* Create the class instance */
var wtw3dinternet = new WTW_3DINTERNET();

WTW_3DINTERNET.prototype.onClick = function(pickedname) {
	try {
		pickedname = pickedname.toLowerCase();
		let moldnameparts = WTW.getMoldnameParts(pickedname);
		if (pickedname.indexOf('person') > -1) {
			if (wtw3dinternet.masterChat == '1') {
				wtw3dinternet.startChat(moldnameparts.instanceid);
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-onClick=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.checkHovers = function(moldname, shape) {
	try {
		if (moldname.indexOf('person-') > -1) {
			if (wtw3dinternet.masterChat == '1') {
				WTW.showToolTip('Click to Chat');
			}
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-checkHovers=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.resetHovers = function(moldname, shape) {
	try {
		WTW.hideToolTip();
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-resetHovers=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.loadUserSettingsAfterEngine = function() {
	try {
		if (wtw3dinternet.masterMove == '1') {
			wtw3dinternet.initMoveSocket();
		}
		if (wtw3dinternet.masterChat == '1') {
			wtw3dinternet.initChatSocket();
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-loadUserSettingsAfterEngine=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.loadUserSettings = function() {
	try {
		WTW.getSettings("wtw3dinternet_masterMove, wtw3dinternet_masterChat, wtw3dinternet_masterFranchising", "wtw3dinternet.responseLoadUserSettings");
		var zavatarids = WTW.getCookie("AvatarIDs");
		if (zavatarids != null) {
			if (WTW.isNumeric(zavatarids)) {
				wtw3dinternet.AvatarIDs = Number(zavatarids);
			}
		}
		if (wtw3dinternet.AvatarIDs == 0) {
			dGet('wtw_submenuavataridstext').innerHTML = 'Avatar IDs are Off';
			dGet('wtw_submenuavatarids').src = '/content/system/images/menuavataridsoff.png';
			dGet('wtw_submenuavatarids').alt = 'Turn Avatar IDs On';
			dGet('wtw_submenuavatarids').title = 'Turn Avatar IDs On';
		}
		var multiplayeron = WTW.getCookie("multiplayeron");
		if (multiplayeron != null) {
			if (multiplayeron == "0") {
				wtw3dinternet.multiPlayerOn = 0;
				dGet('wtw_submenumultiplayertext').innerHTML = 'Multi-Player is Off';
				dGet('wtw_submenumultiplayer').src = '/content/system/images/menumultiplayeroff.png';
				dGet('wtw_submenumultiplayer').alt = 'Turn Multi-Player On';
				dGet('wtw_submenumultiplayer').title = 'Turn Multi-Player On';
			}
		}
		var multiplayer = WTW.getCookie("multiplayer");
		if (multiplayer != null) {
			if (WTW.isNumeric(multiplayer)) {
				wtw3dinternet.multiPlayer = Number(multiplayer);
			} else {
				wtw3dinternet.multiPlayer = 20;
			}
		} else {
			wtw3dinternet.multiPlayer = 20;
		}
		dGet('wtw_tavatarcount').value = wtw3dinternet.multiPlayer;
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-loadUserSettings=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.responseLoadUserSettings = function(zsettings, zparameters) {
	try {
		zsetting = JSON.parse(zsettings);
		if (zsetting.wtw3dinternet_masterMove != undefined) {
			wtw3dinternet.masterMove = zsetting.wtw3dinternet_masterMove;					
		}
		if (zsetting.wtw3dinternet_masterChat != undefined) {
			wtw3dinternet.masterChat = zsetting.wtw3dinternet_masterChat;					
		}
		if (zsetting.wtw3dinternet_masterFranchising != undefined) {
			wtw3dinternet.masterFranchising = zsetting.wtw3dinternet_masterFranchising;					
		}
		wtw3dinternet.setControlPanelSwitches();
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-responseLoadUserSettings=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.setControlPanelSwitches = function() {
	try {
		if (WTW.adminView == 1) {
			if (wtw3dinternet.masterMove == '1') {
				dGet('wtw3dinternet_enablemultiplayertext').className = 'wtw3dinternet-enablelabel';
				dGet('wtw3dinternet_enablemultiplayertext').innerHTML = 'Multiplayer Enabled';
				dGet('wtw3dinternet_enablemultiplayer').checked = true;
				wtw3dinternet.initMoveSocket();
			} else {
				dGet('wtw3dinternet_enablemultiplayertext').className = 'wtw3dinternet-disabledlabel';
				dGet('wtw3dinternet_enablemultiplayertext').innerHTML = 'Multiplayer Disabled';
				dGet('wtw3dinternet_enablemultiplayer').checked = false;
			}
			if (wtw3dinternet.masterChat == '1') {
				dGet('wtw3dinternet_enablechattext').className = 'wtw3dinternet-enablelabel';
				dGet('wtw3dinternet_enablechattext').innerHTML = 'Multiplayer Chat Enabled';
				dGet('wtw3dinternet_enablechat').checked = true;
			} else {
				dGet('wtw3dinternet_enablechattext').className = 'wtw3dinternet-disabledlabel';
				dGet('wtw3dinternet_enablechattext').innerHTML = 'Multiplayer Chat Disabled';
				dGet('wtw3dinternet_enablechat').checked = false;
			}
			if (wtw3dinternet.masterFranchising == '1') {
				dGet('wtw3dinternet_enablefranchisebuildingstext').className = 'wtw3dinternet-enablelabel';
				dGet('wtw3dinternet_enablefranchisebuildingstext').innerHTML = '3D Buildings Franchising Enabled';
				dGet('wtw3dinternet_enablefranchisebuildings').checked = true;
			} else {
				dGet('wtw3dinternet_enablefranchisebuildingstext').className = 'wtw3dinternet-disabledlabel';
				dGet('wtw3dinternet_enablefranchisebuildingstext').innerHTML = '3D Buildings Franchising Disabled';
				dGet('wtw3dinternet_enablefranchisebuildings').checked = false;
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-setControlPanelSwitches=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.changeSwitch = function(zobj) {
	try {
		let zchecked = '0';
		if (zobj.checked) {
			zchecked = '1';
		}
		switch (zobj.id) {
			case "wtw3dinternet_enablemultiplayer":
				wtw3dinternet.masterMove = zchecked;
				if (wtw3dinternet.masterMove == '1') {
					wtw3dinternet.initMoveSocket();
				}
				break;
			case "wtw3dinternet_enablechat":
				wtw3dinternet.masterChat = zchecked;
				break;
			case "masterFranchising":
				wtw3dinternet.masterFranchising = zchecked;
				break;
		}
		wtw3dinternet.setControlPanelSwitches();
		let zsettings = {
			'wtw3dinternet_masterMove': wtw3dinternet.masterMove,
			'wtw3dinternet_masterChat': wtw3dinternet.masterChat,
			'wtw3dinternet_masterFranchising': wtw3dinternet.masterFranchising
		};
		WTW.saveSettings(zsettings, null);		
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-changeSwitch=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.getAvatarDisplayName = function(instanceid) {
	var displayname = "";
	try {
		var avatar = scene.getMeshByID('person-' + instanceid);
		if (avatar != null) {
			if (avatar.WTW != null) {
				if (avatar.WTW.displayname != undefined) {
					displayname = avatar.WTW.displayname;
				}
			}
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-class_main.js-getAvatarDisplayName=" + ex.message);
	}
	return displayname;
}
