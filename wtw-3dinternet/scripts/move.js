WTW_3DINTERNET.prototype.initMoveSocket = function() {
	try {
		if (wtw3dinternet.move == null) {
			wtw3dinternet.move = io.connect('https://3dnet.walktheweb.network/move');
			
			wtw3dinternet.move.on('serror', function(message) {
				WTW.log("error = " + message,'red');
			}); 
			
			wtw3dinternet.move.on('login', function(data) {
				if (wtw3dinternet.masterMove == '1') {
					// Whenever the server emits 'login', add user to count
					wtw3dinternet.addParticipantsMessage(data); 
				}
			}); 

			wtw3dinternet.move.on('user joined', function(data) {
				if (wtw3dinternet.masterMove == '1') {
					// Whenever the server emits 'user joined', log it in the chat body
					//log(data.username + ' joined');
					wtw3dinternet.addParticipantsMessage(data);
				}
			});

			wtw3dinternet.move.on('user left', function(data) {
				// Whenever the server emits 'user left', fade and remove the avatar
				wtw3dinternet.addParticipantsMessage(data);
				wtw3dinternet.removeAvatar(data.avatarname);
			});

			wtw3dinternet.move.on('set disabled', function(data) {
				// Whenever the server emits 'user left', fade and remove the avatar
				wtw3dinternet.addParticipantsMessage(data);
				wtw3dinternet.removeAllAvatars();
			});

			wtw3dinternet.move.on('reconnect', function() {
				wtw3dinternet.move.emit('add user', {
					'serverinstanceid':dGet('wtw_serverinstanceid').value,
					'roomid':communityid + buildingid + thingid,
					'instanceid':dGet('wtw_tinstanceid').value,
					'userid':dGet('wtw_tuserid').value,
					'username':dGet('wtw_tusername').value
				});
			});

			wtw3dinternet.move.on('reconnect_error', function() {
				WTW.log('reconnect failed');
			});

			wtw3dinternet.move.on('avatar movement', function(zmovedata) {
				/* process runs when another avatar in the scene moves */
				if (wtw3dinternet.masterMove == '1') {
					var avatarname = "person-" + zmovedata.instanceid;
					var zavatar = scene.getMeshByID(avatarname);
					if (zavatar != null) {
						if (zavatar.position.x != zmovedata.position.x || zavatar.position.y != zmovedata.position.y || zavatar.position.z != zmovedata.position.z) {
							zavatar.position.x = zmovedata.position.x;
							zavatar.position.y = zmovedata.position.y;
							zavatar.position.z = zmovedata.position.z;
							WTW.checkZones = true;
						}
						zavatar.rotation.x = zmovedata.rotation.x;
						zavatar.rotation.y = zmovedata.rotation.y;
						zavatar.rotation.z = zmovedata.rotation.z;
						for (var i=0;i<zmovedata.moveevents.length;i++) {
							if (zmovedata.moveevents[i] != null && zavatar.WTW.animations.running != null) {
								if (zavatar.WTW.animations.running[zmovedata.moveevents[i].key] != null) {
									zavatar.WTW.animations.running[zmovedata.moveevents[i].key].weight = zmovedata.moveevents[i].weight;
									switch (zmovedata.moveevents[i].key) {
										case 'onwait':
										case 'onrotateup':
										case 'onrotatedown':
											break;
										case 'onturnleft':
										case 'onturnright':
											zavatar.WTW.animations.running[zmovedata.moveevents[i].key].speedRatio = zmovedata.turnanimationspeed;
											break;
										case 'onrunturnleft':
										case 'onrunturnright':
											zavatar.WTW.animations.running[zmovedata.moveevents[i].key].speedRatio = zmovedata.turnanimationspeed * 1.5;
											break;
										default:
											zavatar.WTW.animations.running[zmovedata.moveevents[i].key].speedRatio = zmovedata.walkanimationspeed;
											break;
									}
								}
							}
						}
					} else if (wtw3dinternet.isLoading(zmovedata.instanceid) == false) {
						wtw3dinternet.avatars[wtw3dinternet.avatars.length] = {
							'instanceid':zmovedata.instanceid,
							'userid':zmovedata.userid,
							'position':{
								'x':WTW.init.startPositionX,
								'y':WTW.init.startPositionY,
								'z':WTW.init.startPositionZ
							},
							'rotation':{
								'x':WTW.init.startRotationX,
								'y':WTW.init.startRotationY,
								'z':WTW.init.startRotationZ
							},
							'loaded':'0'
						};
						wtw3dinternet.getMultiUserAvatar(zmovedata.instanceid, zmovedata.userid);
					} else {
						let zavatarind = wtw3dinternet.getAvatarInd(zmovedata.instanceid);
						if (wtw3dinternet.avatars[zavatarind] != null) {
							wtw3dinternet.avatars[zavatarind].position.x = zmovedata.position.x;
							wtw3dinternet.avatars[zavatarind].position.y = zmovedata.position.y;
							wtw3dinternet.avatars[zavatarind].position.z = zmovedata.position.z;
							wtw3dinternet.avatars[zavatarind].rotation.x = zmovedata.rotation.x;
							wtw3dinternet.avatars[zavatarind].rotation.y = zmovedata.rotation.y;
							wtw3dinternet.avatars[zavatarind].rotation.z = zmovedata.rotation.z;
						}
					}
				}
			});
			
			wtw3dinternet.move.emit('add user', {
				'serverinstanceid':dGet('wtw_serverinstanceid').value,
				'roomid':communityid + buildingid + thingid,
				'instanceid':dGet('wtw_tinstanceid').value,
				'userid':dGet('wtw_tuserid').value,
				'username':dGet('wtw_tusername').value
			});
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-initMoveSocket=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.moveAvatar = function(zavatar, zmoveevents) {
	try {
		if (wtw3dinternet.masterMove == '1') {
			if (wtw3dinternet.move != null) {
				/* send multiplayer the position and animations */
				let zmovedata = {
					'serverinstanceid':dGet('wtw_serverinstanceid').value,
					'roomid':communityid + buildingid + thingid,
					'instanceid':dGet('wtw_tinstanceid').value,
					'userid':dGet('wtw_tuserid').value,
					'username':dGet('wtw_tusername').value,
					'walkspeed':WTW.walkSpeed,
					'walkanimationspeed':WTW.walkAnimationSpeed,
					'turnspeed':WTW.turnSpeed,
					'turnanimationspeed':WTW.turnAnimationSpeed,
					'position':{
						'x':WTW.myAvatar.position.x,
						'y':WTW.myAvatar.position.y,
						'z':WTW.myAvatar.position.z
					},
					'rotation':{
						'x':WTW.myAvatar.rotation.x,
						'y':WTW.myAvatar.rotation.y,
						'z':WTW.myAvatar.rotation.z
					},
					'moveevents':zmoveevents
				};
				wtw3dinternet.move.emit('my avatar movement', zmovedata);
			}
		} else {
			if (wtw3dinternet.move != null) {
				wtw3dinternet.move.emit('disable',{});
			}
			wtw3dinternet.removeAllAvatars();
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-moveAvatar=" + ex.message);
	} 
}
		
WTW_3DINTERNET.prototype.getMoveEventsArray = function(zmoveeventscsv) {
	var zmoveevents = [];
	try {
		if (zmoveeventscsv != undefined) {
			if (zmoveeventscsv.indexOf(',') > -1) {
				let zmovearray = zmoveeventscsv.split(',');
				for (let i=0;i<zmovearray.length;i++) {
					if (zmovearray[i] != '') {
						zmoveevents[zmoveevents.length] = trim(zmovearray[i]);
					}
				}
			} else {
				zmoveevents[zmoveevents.length] = zmoveeventscsv;
			}
		} else {
			zmoveevents[zmoveevents.length] = 'onwait';
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-isLoading=" + ex.message);
	} 
	return zmoveevents;
}

WTW_3DINTERNET.prototype.isLoading = function(zinstanceid) {
	var zfound = false;
	try {
		for (var i=0;i<wtw3dinternet.avatars.length;i++) {
			if (wtw3dinternet.avatars[i] != null) {
				if (wtw3dinternet.avatars[i].instanceid != undefined) {
					if (wtw3dinternet.avatars[i].instanceid == zinstanceid) {
						zfound = true;
					}
				}
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-isLoading=" + ex.message);
	} 
	return zfound;
}

WTW_3DINTERNET.prototype.getAvatarInd = function(zinstanceid) {
	var zavatarind = false;
	try {
		for (var i=0;i<wtw3dinternet.avatars.length;i++) {
			if (wtw3dinternet.avatars[i] != null) {
				if (wtw3dinternet.avatars[i].instanceid != undefined) {
					if (wtw3dinternet.avatars[i].instanceid == zinstanceid) {
						zavatarind = i;
					}
				}
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-isLoading=" + ex.message);
	} 
	return zavatarind;
}

WTW_3DINTERNET.prototype.addParticipantsMessage = function(data) {
	try {
		if (data.usercount > 0 && wtw3dinternet.masterMove == '1') {
			document.getElementById('participantsMessage').innerHTML = "&nbsp;" + data.usercount + " Walkers";
		} else {
			document.getElementById('participantsMessage').innerHTML = '';
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-addParticipantsMessage=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.removeAvatar = function(avatarname) {
	try {
		var zfade = window.setInterval(function() {
			var zavatarparent = scene.getMeshByID(avatarname + "-scale");
			var zfaded = true;
			if (zavatarparent != null) {
			var zavatarparts = zavatarparent.getChildren();
				if (zavatarparts != null) {
					if (zavatarparts.length > 0) {
						for (var i=0;i< zavatarparts.length;i++) {
							if (zavatarparts.material != undefined) {
								if (zavatarparts.material.alpha > 0) {
									zfaded = false;
									zavatarparts.material.alpha -= .05;
								}
							}
						}
					}
				}
			}
			if (zfaded) {
				window.clearInterval(zfade);
				for (var i=wtw3dinternet.avatars.length;i>0;i--) {
					if (wtw3dinternet.avatars[i] != null) {
						if (wtw3dinternet.avatars[i].instanceid != undefined) {
							if (wtw3dinternet.avatars[i].instanceid == avatarname.replace("person-","")) {
								wtw3dinternet.avatars.splice(i,1);
							}
						}
					}
				}
				
			}
		},100);
		var zavatar = scene.getMeshByID(avatarname);
		if (zavatar != null) {
			WTW.disposeClean(avatarname);
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-removeAvatar=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.removeAllAvatars = function() {
	try {
		for (var i=0; i<wtw3dinternet.avatars.length;i++) {
			if (wtw3dinternet.avatars[i] != null) {
				if (wtw3dinternet.avatars[i].instanceid != undefined) {
					wtw3dinternet.removeAvatar("person-" + wtw3dinternet.avatars[i].instanceid);
				}
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-removeAllAvatars=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.initMultiuser = function() {

	try {
		var avatarind = 1;
		var objectfolder = "";
		var objectfile = "";
		var httpsecure = "0";
		var scalingx = "1";
		var scalingy = "1";
		var scalingz = "1";
		var privacy = "0";
		if (WTW.myAvatar != null) {
			if (WTW.myAvatar.WTW != null) {
				if (WTW.myAvatar.WTW.avatarind != undefined) {
					avatarind = WTW.myAvatar.WTW.avatarind;
				}
				if (WTW.myAvatar.WTW.object != null) {
					if (WTW.myAvatar.WTW.object.folder != undefined) {
						objectfolder = WTW.myAvatar.WTW.object.folder;
					}
					if (WTW.myAvatar.WTW.object.file != undefined) {
						objectfile = WTW.myAvatar.WTW.object.file;
					}
				}
				if (WTW.myAvatar.WTW.scaling.x != undefined) {
					scalingx = WTW.myAvatar.WTW.scaling.x;
				}
				if (WTW.myAvatar.WTW.scaling.y != undefined) {
					scalingy = WTW.myAvatar.WTW.scaling.y;
				}
				if (WTW.myAvatar.WTW.scaling.z != undefined) {
					scalingz = WTW.myAvatar.WTW.scaling.z;
				}
			}
		}
		if (dGet('wtw_menudisplayname').innerHTML == '' && dGet('wtw_tusername').value != '') {
			dGet('wtw_menudisplayname').innerHTML = dGet('wtw_tusername').value;
		}
		if (dGet('wtw_menudisplayname').innerHTML == '' && dGet('wtw_tuseremail').value != '') {
			var emailbase = dGet('wtw_tuseremail').value.split('@');
			dGet('wtw_menudisplayname').innerHTML = emailbase[0];
		}
		if (dGet('wtw_menudisplayname').innerHTML == '') {
			dGet('wtw_menudisplayname').innerHTML = 'Anonymous';
		}
		if (wtw_protocol.toLowerCase().indexOf('https') > -1) { httpsecure = "1"; }
		var zenteranimation = WTW.getDDLValue('wtw_tselectavataranimation-enter');
		var zenteranimationparameter = "";
		var zexitanimation = "1";
		var zexitanimationparameter = "";
		var myavatarid = dGet('wtw_tmyavatarid').value;
		if (dGet('wtw_tuserid').value == '') {
			myavatarid = dGet('wtw_tmyavataridanon').value;
		}
		if (WTW.isNumeric(zenteranimation) == false) {
			zenteranimation = '1';
		}
		var surl = wtw_domainurl + "/connect/wtw-3dinternet-updateavatar.php?" +
			"&i=" + btoa(dGet('wtw_tinstanceid').value) + 
			"&u=" + btoa(myavatarid) + 
			"&ai=" + btoa(avatarind) + 
			"&d=" + btoa(dGet('wtw_tuserid').value) + 
			"&o=" + btoa(objectfolder) + 
			"&f=" + btoa(objectfile) + 
			"&m=" + btoa(wtw_domainname) + 
			"&s=" + btoa(httpsecure) + 
			"&x=" + btoa(scalingx) + 
			"&y=" + btoa(scalingy) + 
			"&z=" + btoa(scalingz) + 
			"&n=" + btoa(dGet('wtw_menudisplayname').innerHTML) + 
			"&p=" + btoa(privacy) + 
			"&en=" + btoa(zenteranimation) + 
			"&enp=" + btoa(zenteranimationparameter) + 
			"&ex=" + btoa(zexitanimation) + 
			"&exp=" + btoa(zexitanimationparameter) + 
			"&w=" + btoa(WTW.walkSpeed) + 
			"&v=" + btoa(WTW.walkAnimationSpeed) + 
			"&t=" + btoa(WTW.turnSpeed) + 
			"&r=" + btoa(WTW.turnAnimationSpeed) + 
			"&a=" + btoa(dGet('wtw_tuserip').value);
		WTW.getJSON(surl, 
			function(response) {}
		);
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-initMultiuser=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.getMultiUserAvatar = function(instanceid, zuserid) {
	try {
		for (var i = 0; i < scene.meshes.length; i++) {
			var moldname = scene.meshes[i].name;
			if (moldname.indexOf("person-") > -1 && moldname.indexOf(instanceid) > -1) {
				WTW.addDisposeMoldToQueue(moldname);
			}
		}
		var surl = wtw_domainurl + "/connect/wtw-3dinternet-getavatar.php?" + 
			"i=" + btoa(instanceid) + 
			"&d=" + btoa(zuserid) +
			"&c=" + btoa(communityid) + 
			"&b=" + btoa(buildingid); 
		WTW.getJSON(surl, 
			function(response) {
				avatardef = JSON.parse(response);
				if (avatardef != null) {
					if (avatardef.instanceid != undefined) {
						var avatarname = "person-" + avatardef.instanceid;
						let zavatarind = wtw3dinternet.getAvatarInd(avatardef.instanceid);
						if (wtw3dinternet.avatars[zavatarind] != null) {
							avatardef.position.x = wtw3dinternet.avatars[zavatarind].position.x;
							avatardef.position.y = wtw3dinternet.avatars[zavatarind].position.y;
							avatardef.position.z = wtw3dinternet.avatars[zavatarind].position.z;
							wtw3dinternet.avatars[zavatarind].loaded = '1';
						}
						var avatar1 = WTW.addAvatar(avatarname, avatardef, WTW.mainParent); 
						if (wtw3dinternet.avatars[zavatarind] != null) {
							avatar1.rotation.x = wtw3dinternet.avatars[zavatarind].rotation.x;
							avatar1.rotation.y = wtw3dinternet.avatars[zavatarind].rotation.y;
							avatar1.rotation.z = wtw3dinternet.avatars[zavatarind].rotation.z;
						}
						var par = WTW.getMainParent();
						if (par != null) {
							avatar1.parent = par;
						}
					}
				}
			}
		); 
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-getMultiUserAvatar=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.clearMultiuser = function(instance, zuserid) {
	try {
		for (var i = 0; i < scene.meshes.length; i++) {
			var moldname = scene.meshes[i].name;
			if (moldname.indexOf("person-") > -1) {
				WTW.addDisposeMoldToQueue(moldname);
			}
		}
		var surl = wtw_domainurl + "/connect/wtw-3dinternet-clearavatar.php?" + 
			"i=" + btoa(instance) + 
			"&d=" + btoa(zuserid) + 
			"&c=" + btoa(communityid) + 
			"&b=" + btoa(buildingid);
		WTW.getJSON(surl, 
			function(response) {}
		);
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-clearMultiuser=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.toggleMultiPlayer = function() {
	try {
		if (dGet('wtw_submenumultiplayertext').innerHTML == 'Multi-Player is Off') {
			dGet('wtw_submenumultiplayertext').innerHTML = 'Multi-Player is On';
			dGet('wtw_submenumultiplayer').src = '/content/system/images/menumultiplayer.png';
			dGet('wtw_submenumultiplayer').alt = 'Turn Multi-Player Off';
			dGet('wtw_submenumultiplayer').title = 'Turn Multi-Player Off';
			if (dGet('wtw_tavatarcount').value == '' || WTW.isNumeric(dGet('wtw_tavatarcount').value) == false) {
				dGet('wtw_tavatarcount').value = '20';
			}
			wtw3dinternet.multiPlayer = dGet('wtw_tavatarcount').value;
			wtw3dinternet.multiPlayerOn = 1;
			WTW.setCookie("multiplayeron","1",30);
			WTW.setCookie("multiplayer",wtw3dinternet.multiPlayer,30);
			if (WTW.setupMode == 0) {
				wtw3dinternet.initMultiuser();
			}
		} else {
			dGet('wtw_submenumultiplayertext').innerHTML = 'Multi-Player is Off';
			dGet('wtw_submenumultiplayer').src = '/content/system/images/menumultiplayeroff.png';
			dGet('wtw_submenumultiplayer').alt = 'Turn Multi-Player On';
			dGet('wtw_submenumultiplayer').title = 'Turn Multi-Player On';
			if (dGet('wtw_tavatarcount').value == '' || WTW.isNumeric(dGet('wtw_tavatarcount').value) == false) {
				dGet('wtw_tavatarcount').value = '0';
			}
			wtw3dinternet.multiPlayer = dGet('wtw_tavatarcount').value;
			wtw3dinternet.multiPlayerOn = 0;
			WTW.setCookie("multiplayeron","0",30);
			WTW.setCookie("multiplayer",wtw3dinternet.multiPlayer,30);
			wtw3dinternet.clearMultiuser(dGet('wtw_tinstanceid').value, dGet('wtw_tuserid').value);
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-toggleMultiPlayer=" +ex.message);
	}
}

WTW_3DINTERNET.prototype.toggleAvatarIDs = function() {
	try {
		var setvisibility = false;
		if (dGet('wtw_submenuavataridstext').innerHTML == 'Avatar IDs are Off') {
			dGet('wtw_submenuavataridstext').innerHTML = 'Avatar IDs are On';
			dGet('wtw_submenuavatarids').src = '/content/system/images/menuavataridson.png';
			dGet('wtw_submenuavatarids').alt = 'Turn Avatar IDs Off';
			dGet('wtw_submenuavatarids').title = 'Turn Avatar IDs Off';
			wtw3dinternet.AvatarIDs = 1;
			WTW.setCookie("AvatarIDs",wtw3dinternet.AvatarIDs,30);
			setvisibility = true;
		} else {
			dGet('wtw_submenuavataridstext').innerHTML = 'Avatar IDs are Off';
			dGet('wtw_submenuavatarids').src = '/content/system/images/menuavataridsoff.png';
			dGet('wtw_submenuavatarids').alt = 'Turn Avatar IDs On';
			dGet('wtw_submenuavatarids').title = 'Turn Avatar IDs On';
			wtw3dinternet.AvatarIDs = 0;
			WTW.setCookie("AvatarIDs",wtw3dinternet.AvatarIDs,30);
		}
		var meshes = scene.meshes;
		if (meshes != null) {
			for (var i=0;i<meshes.length;i++) {
				if (meshes[i] != null) {
					if (meshes[i].name.indexOf("person-") > -1 && meshes[i].name.indexOf("-nameplate") > -1) {
						meshes[i].isVisible = setvisibility;
					}
				}
			}
		}
    } catch (ex) {
        WTW.log("plugins:wtw-3dinternet:scripts-move.js-toggleAvatarIDs=" +ex.message);
    }
}

WTW_3DINTERNET.prototype.showAvatarIDs = function(avatarname, avatardef) {
	try {
		var displayname = 'Anonymous';
		if (avatardef.displayname != undefined) {
			displayname = avatardef.displayname;
		}
		if (displayname != '' && avatarname.indexOf('person-') > -1) {
			var avatar = scene.getMeshByID(avatarname);
			var molddef = WTW.newMold();
			molddef.webtext.webtext = displayname;
			molddef.webtext.webstyle = JSON.stringify({
				"anchor":"center",
				"letter-height":1.00,
				"letter-thickness":.10,
				"color":"#0000ff",
				"alpha":1.00,
				"colors":{
					"diffuse":"#f0f0f0",
					"specular":"#000000",
					"ambient":"#808080",
					"emissive":"#0000ff"
				}
			});
			var namemold = WTW.addMold3DText(avatarname + '-nameplate', molddef, 1, 1, 1);
			namemold.parent = avatar;
			namemold.position.y = 16;
			namemold.billboardMode = 2;
			if (wtw3dinternet.AvatarIDs == 0) {
				namemold.isVisible = false;
			}
		}
    } catch (ex) {
        WTW.log("plugins:wtw-3dinternet:scripts-move.js-showAvatarIDs=" +ex.message);
    }
}

WTW_3DINTERNET.prototype.activateMultiplayer = function() {
	try {
		if (wtw3dinternet.multiPlayerOn == 1) {
			if (WTW.isNumeric(wtw3dinternet.multiPlayer)) {
				if (Number(wtw3dinternet.multiPlayer) > 0 && WTW.setupMode == 0) {
					window.setTimeout(function() {wtw3dinternet.initMultiuser();},1000);
				}
			}
		}
    } catch (ex) {
        WTW.log("plugins:wtw-3dinternet:scripts-move.js-activateMultiplayer=" +ex.message);
    }
}

WTW_3DINTERNET.prototype.multiPersonInActionZone = function(zactionzone) {
	var zintersects = false;
	try {
		for (var i=0; i < wtw3dinternet.avatars.length; i++) {
			if (wtw3dinternet.avatars[i] != null) {
				var avatarname = "person-" + wtw3dinternet.avatars[i].instanceid;
				var avatar1 = scene.getMeshByID(avatarname);
				if (avatar1 != null) {
					zintersects = avatar1.intersectsMesh(zactionzone, false); // precise false
				}
				if (zintersects) {
					i = wtw3dinternet.avatars.length;
				}
			}
		}
	} catch(ex){
		WTW.log("plugins:wtw-3dinternet:scripts-move.js-multiPersonInActionZone=" + ex.message);
	}
	return zintersects;
}
