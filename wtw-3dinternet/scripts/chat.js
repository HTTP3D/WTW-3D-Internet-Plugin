WTW_3DINTERNET.prototype.initChatSocket = function() {
	try {
		if (wtw3dinternet.chat == null) {
			wtw3dinternet.chat = io.connect('https://3dnet.walktheweb.network/chat');

			wtw3dinternet.chat.on('login', function(data) {
				if (wtw3dinternet.masterChat == '1') {
				}
			}); 

			wtw3dinternet.chat.on('user joined', function(data) {
				if (wtw3dinternet.masterChat == '1') {
				}
			});

			wtw3dinternet.chat.on('user left', function(data) {
			});

			wtw3dinternet.chat.on('reconnect', function() {
				if (wtw3dinternet.masterChat == '1') {
					wtw3dinternet.chat.emit('add user', {
						'serverinstanceid':dGet('wtw_serverinstanceid').value,
						'roomid':communityid + buildingid + thingid,
						'instanceid':dGet('wtw_tinstanceid').value,
						'userid':dGet('wtw_tuserid').value,
						'username':dGet('wtw_tusername').value
					});
				}
			});
			
			if (wtw3dinternet.masterChat == '1') {
				wtw3dinternet.chat.emit('add user', {
					'serverinstanceid':dGet('wtw_serverinstanceid').value,
					'roomid':communityid + buildingid + thingid,
					'instanceid':dGet('wtw_tinstanceid').value,
					'userid':dGet('wtw_tuserid').value,
					'username':dGet('wtw_tusername').value
				});
			}

			wtw3dinternet.chat.on('chat invite', function(data) {
				if (wtw3dinternet.masterChat == '1') {
					if (dGet('wtw_chatbox-' + data.chatid) == null) {
						var zdisplayname = wtw3dinternet.getAvatarDisplayName(data.frominstanceid);
						dGet('wtw_chatsendrequests').innerHTML += wtw3dinternet.addChatBox(data.chatid, zdisplayname, "Chat Request from " + zdisplayname + ".<br />");
						WTW.showInline('wtw_chataccept-' + data.chatid);
						WTW.showInline('wtw_chatdecline-' + data.chatid);
						wtw3dinternet.chatSetScroll(data.chatid, false);
					}
					wtw3dinternet.chatSetScroll(data.chatid, true);
					WTW.show('wtw_3dinternetchatform');
				}
			});
			
			wtw3dinternet.chat.on('receive chat', function(data) {
				if (wtw3dinternet.masterChat == '1') {
					var ztext = WTW.decode(data.text);
					var zdisplayname = wtw3dinternet.getAvatarDisplayName(data.frominstanceid);
					if (ztext.length > 0) {
						if (dGet('wtw_chattext-' + data.chatid) != null) {
							dGet('wtw_chattext-' + data.chatid).innerHTML += "<span class='wtw3dinternet-chatthem'><b>" + zdisplayname + "</b>: " + WTW.encode(ztext) + "</span><hr class='wtw3dinternet-chathr' />";
						}
					}
					wtw3dinternet.chatSetScroll(data.chatid, true);
				}
			});

			wtw3dinternet.chat.on('receive chat command', function(data) {
				if (wtw3dinternet.masterChat == '1') {
					wtw3dinternet.processChatCommand(data);
				}
			});

			wtw3dinternet.chat.on('typing', function(data) {
				if (wtw3dinternet.masterChat == '1') {
					wtw3dinternet.processChatCommand(data);
				}
			});

			wtw3dinternet.chat.on('stop typing', function(data) {
				if (wtw3dinternet.masterChat == '1') {
					wtw3dinternet.processChatCommand(data);
				}
			});
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-initChatSocket=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.onUnload = function() {
	try {
		var zchats = dGet('wtw_chatsendrequests').childNodes;
		for (var i=0; i<zchats.length; i++) {
			if (zchats[i] != null) {
				if (zchats[i].id != undefined) {
					if (zchats[i].id.indexOf('wtw_chatbox-') > -1) {
						let nameparts = zchats[i].id.split('-');
						if (nameparts[1] != null) {
							wtw3dinternet.sendMessage(nameparts[1], '', 'chat command', 'leave chat');
						}
					}
				}
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-onUnload=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.processChatCommand = function(data) {
	try {
		if (wtw3dinternet.masterChat == '1') {
			if (dGet('wtw_chatbox-' + data.chatid) != null) {
				var zdisplayname = wtw3dinternet.getAvatarDisplayName(data.frominstanceid);
				ztext = "";
				switch (data.text) {
					case "decline chat":
						ztext = "<span class='wtw3dinternet-chatrednote'><b>" + zdisplayname + ":</b> Sorry, I can not chat right now.</span><br />";
						ztext += "<div id='wtw_chatclosenow-" + data.chatid + "' class='wtw3dinternet-chatbuttondecline' onclick=\"wtw3dinternet.closeChat('" + data.chatid + "');\">Close</div>";
						break;
					case "leave chat":
						ztext = "<span class='wtw3dinternet-chatnote'><b>" + zdisplayname + "</b> has left the chat.</span>";
						WTW.hide('wtw_chattyping-' + data.chatid);
						WTW.hide('wtw_chatadd-' + data.chatid);
						WTW.hide('wtw_chattextsend-' + data.chatid);
						WTW.show('wtw_chatok-' + data.chatid);
						break;
					case "accept chat":
						wtw3dinternet.acceptChat(data.chatid, zdisplayname);
						break;
					case "typing":
						dGet('wtw_chattyping-' + data.chatid).innerHTML = zdisplayname + ' is typing...';
						dGet('wtw_chattyping-' + data.chatid).style.visibility = 'visible';
						break;
					case "stop typing":
						dGet('wtw_chattyping-' + data.chatid).innerHTML = '';
						dGet('wtw_chattyping-' + data.chatid).style.visibility = 'hidden';
						break;
				}
				if (ztext != "" && dGet('wtw_chattext-' + data.chatid) != null) {
					dGet('wtw_chattext-' + data.chatid).innerHTML += ztext + "<hr class='wtw3dinternet-chathr' />";
				}
				wtw3dinternet.chatSetScroll(data.chatid, true);
			}
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-processChatCommand=" + ex.message);
	} 
}
			
WTW_3DINTERNET.prototype.startChat = function(zinstanceid) {
	try {
		if (wtw3dinternet.masterChat == '1') {
			let zchatid = WTW.getRandomString(20);
			if (dGet('wtw_chatbox-' + zchatid) == null) {
				var zdisplayname = wtw3dinternet.getAvatarDisplayName(zinstanceid);
				dGet('wtw_chatsendrequests').innerHTML += wtw3dinternet.addChatBox(zchatid, zdisplayname, "Chat Request Sent to " + zdisplayname + ".<br />");
				WTW.show('wtw_menuchatmin');
			}
			wtw3dinternet.chatSetScroll(zchatid, true);
			WTW.show('wtw_3dinternetchatform');
			wtw3dinternet.sendMessage(zchatid, zinstanceid, 'start chat', '');
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-joinChat=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.sendMessage = function(zchatid, ztoinstanceid, zaction, ztext) {
	try {
		if (wtw3dinternet.masterChat == '1') {
			let zroomid = communityid + buildingid + thingid;
			wtw3dinternet.chat.emit(zaction, {
				'serverinstanceid':dGet('wtw_serverinstanceid').value,
				'roomid':zroomid,
				'chatid':zchatid,
				'userid':dGet('wtw_tuserid').value,
				'frominstanceid':dGet('wtw_tinstanceid').value,
				'toinstanceid':ztoinstanceid,
				'text':ztext
			});
		}
	} catch (ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-sendMessage=" + ex.message);
	} 
}

WTW_3DINTERNET.prototype.addChatBox = function(chatid, displayname, stext) {
	var chatbox = "";
	try {
		if (wtw3dinternet.masterChat == '1') {
			chatbox = 
				"<div id='wtw_chatbox-" + chatid + "' class='wtw3dinternet-chatbox'>" + 
					"<div class='wtw3dinternet-chatboxshadow'>" +
						"<img class='wtw-closeright' onclick=\"wtw3dinternet.closeChat('" + chatid + "',true);\" src='/content/system/images/menuclosegrey.png' alt='Leave Chat' title='Leave Chat' onmouseover=\"this.src='/content/system/images/menuclosehover.png';\" onmouseout=\"this.src='/content/system/images/menuclosegrey.png';\" />" + 
						"<img id='wtw_chatmin" + chatid + "' class='wtw-closeright' onclick=\"WTW.hide('wtw_chatmaxdiv" + chatid + "');WTW.hide('wtw_chatmin" + chatid + "');WTW.show('wtw_chatmax" + chatid + "');wtw3dinternet.chatSetScroll('" + chatid + "', true);\" src='/content/system/images/menuminimizegrey.png' alt='Minimize Chat' title='Minimize Chat' onmouseover=\"this.src='/content/system/images/menuminimizehover.png';\" onmouseout=\"this.src='/content/system/images/menuminimizegrey.png';\" />" + 
						"<img id='wtw_chatmax" + chatid + "' class='wtw-closeright' onclick=\"WTW.show('wtw_chatmaxdiv" + chatid + "');WTW.hide('wtw_chatmax" + chatid + "');WTW.show('wtw_chatmin" + chatid + "');wtw3dinternet.chatSetScroll('" + chatid + "', true);\" src='/content/system/images/menumaximizegrey.png' alt='Maximize Chat' title='Maximize Chat' onmouseover=\"this.src='/content/system/images/menumaximizehover.png';\" onmouseout=\"this.src='/content/system/images/menumaximizegrey.png';\" style='display:none;visibility:hidden;' />" +
						"<div id='wtw_chatdisplayname" + chatid + "' class='wtw3dinternet-chatdisplayname'>" + displayname + "</div>" + 
						"<div id='wtw_chatmaxdiv" + chatid + "'>" +
							"<div id='wtw_chattext-" + chatid + "' class='wtw3dinternet-chattext'>" + stext + "</div>" + 
							"<div class='wtw3dinternet-chatcenter'>" + 
								"<div id='wtw_chattyping-" + chatid + "' class='wtw3dinternet-chattyping'></div>" +
								"<textarea id='wtw_chatadd-" + chatid + "' rows='2' cols='39' class='wtw3dinternet-chattextarea' autocomplete='new-password' onkeyup=\"wtw3dinternet.chatCheckKey(this,'" + chatid + "');\" onfocus=\"wtw3dinternet.setChatNewInfo('" + chatid + "',false);\" style='display:none;visibility:hidden;'></textarea>" + 
								"<div id='wtw_chattextsend-" + chatid + "' class='wtw3dinternet-chattextsend' onclick=\"wtw3dinternet.sendChat('" + chatid + "');\" style='display:none;visibility:hidden;'>Send</div>" +
								"<div id='wtw_chataccept-" + chatid + "' class='wtw3dinternet-chatbuttonaccept' onclick=\"wtw3dinternet.acceptChat('" + chatid + "', '" + displayname + "', true);\" style='display:none;visibility:hidden;'>Accept</div>" + 
								"<div id='wtw_chatdecline-" + chatid + "' class='wtw3dinternet-chatbuttondecline' onclick=\"wtw3dinternet.closeChat('" + chatid + "',true,true);\" style='display:none;visibility:hidden;'>Decline</div>" + 
								"<div id='wtw_chatok-" + chatid + "' class='wtw3dinternet-chatbuttonaccept' onclick=\"wtw3dinternet.closeChat('" + chatid + "');\" style='display:none;visibility:hidden;'>OK</div>" + 
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>";
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-addChatBox=" + ex.message);
	}
	return chatbox;
}

WTW_3DINTERNET.prototype.acceptChat = function(zchatid, zdisplayname, zresponse) {
	try {
		if (zresponse == undefined) {
			zresponse == false;
		}
		if (zresponse) {
			wtw3dinternet.sendMessage(zchatid, '', 'chat command', 'accept chat');
		}
		WTW.hide('wtw_chataccept-' + zchatid);
		WTW.hide('wtw_chatdecline-' + zchatid);
		WTW.hide('wtw_chatok-' + zchatid);
		WTW.showInline('wtw_chatadd-' + zchatid);
		WTW.showInline('wtw_chattextsend-' + zchatid);
		if (dGet('wtw_chattext-' + zchatid) != null) {
			var sdate = new Date();
			dGet('wtw_chattext-' + zchatid).innerHTML = "<span class='wtw3dinternet-chatgreennote'><b>" + zdisplayname + "</b> Entered Chat: " + sdate.toLocaleString() + "</span><hr class='wtw3dinternet-chathr' />";
		}
		wtw3dinternet.chatSetScroll(zchatid, true);
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-acceptChat=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.sendChat = function(zchatid) {
	try {
		var ztext = "";
		if (dGet('wtw_chatadd-' + zchatid) != null) {
			ztext = dGet('wtw_chatadd-' + zchatid).value;
		}
		if (ztext.length > 0 && dGet('wtw_chattext-' + zchatid) != null) {
			dGet('wtw_chattext-' + zchatid).innerHTML += "<span class='wtw3dinternet-chatme'>Me:</span> " + WTW.encode(ztext) + "<hr class='wtw3dinternet-chathr' />";
			dGet('wtw_chatadd-' + zchatid).value = "";
			wtw3dinternet.sendMessage(zchatid, '', 'send chat', WTW.encode(ztext));
		}
		wtw3dinternet.chatSetScroll(zchatid, true);
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-sendChat=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.closeChat = function(zchatid, zresponse, zdecline) {
	try {
		var ztext = 'leave chat';
		if (zresponse == undefined) {
			zresponse == false;
		}
		if (zdecline == undefined) {
			zdecline == false;
		}
		if (zdecline) {
			ztext = 'decline chat';
		}
		if (zresponse) {
			wtw3dinternet.sendMessage(zchatid, '', 'chat command', ztext);
		} else if (ztext == 'leave chat') {
			wtw3dinternet.sendMessage(zchatid, '', 'chat command', 'left chat');
		}
		if (dGet('wtw_chatsendrequests') != null && dGet('wtw_chatbox-' + zchatid) != null) {
			dGet('wtw_chatsendrequests').removeChild(dGet('wtw_chatbox-' + zchatid));
		}
		if (dGet('wtw_chatsendrequests').innerHTML == '') {
			WTW.hide('wtw_menuchat');
		} else {
			wtw3dinternet.chatSetScroll(zchatid, false);
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-closeChat=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.closeMenus = function(zmenuid) {
	try {
		if (zmenuid == 'wtw_menuchat') {
			var zchats = dGet('wtw_chatsendrequests').childNodes;
			for (var i=0; i<zchats.length; i++) {
				if (zchats[i] != null) {
					if (zchats[i].id != undefined) {
						if (zchats[i].id.indexOf('wtw_chatbox-') > -1) {
							let nameparts = zchats[i].id.split('-');
							if (nameparts[1] != null) {
								wtw3dinternet.closeChat(nameparts[1], true, false);
							}
						}
					}
				}
			}
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-closeMenus=" + ex.message);
	}
}
	
WTW_3DINTERNET.prototype.chatCheckKey = function(obj, zchatid) {
	try {
		var e = window.event;
		if (e != undefined) {
			if (e.keyCode == 13) {
				wtw3dinternet.sendChat(zchatid);
				wtw3dinternet.chat.emit('stop typing', {
					'chatid':zchatid,
					'userid':dGet('wtw_tuserid').value,
					'username':dGet('wtw_tusername').value,
					'frominstanceid':dGet('wtw_tinstanceid').value,
					'text':'stop typing'
				});
			} else {
				WTW.checkKey(obj, 'text', 0, 0);
				if (wtw3dinternet.typingTimer != null) {
					window.clearTimeout(wtw3dinternet.typingTimer);
				}
				wtw3dinternet.typingTimer = window.setTimeout(function(){
					wtw3dinternet.chat.emit('stop typing', {
						'chatid':zchatid,
						'userid':dGet('wtw_tuserid').value,
						'username':dGet('wtw_tusername').value,
						'frominstanceid':dGet('wtw_tinstanceid').value,
						'text':'stop typing'
					});
				},1000);
				wtw3dinternet.chat.emit('typing', {
					'chatid':zchatid,
					'userid':dGet('wtw_tuserid').value,
					'username':dGet('wtw_tusername').value,
					'frominstanceid':dGet('wtw_tinstanceid').value,
					'text':'typing'
				});
			}
			e.preventDefault();
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-chatCheckKey=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.chatSetScroll = function(chatid, setfocus) {
	try {
		WTW.showSettingsMenu('wtw_menuchat');
		if (dGet('wtw_chattext-' + chatid) != null) {
			dGet('wtw_chattext-' + chatid).scrollTop = dGet('wtw_chattext-' + chatid).scrollHeight;
		}
		if (setfocus) {
			if (dGet('wtw_chatadd-' + chatid) != null) {
				dGet('wtw_chatadd-' + chatid).focus();
			}
		} else {
			wtw3dinternet.setChatNewInfo(chatid,true);
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-chatSetScroll=" + ex.message);
	}
}

WTW_3DINTERNET.prototype.setChatNewInfo = function(chatid, setNew) {
	try {
		if (setNew) {
			if (dGet('wtw_chatdisplayname' + chatid) != null) {
				dGet('wtw_chatdisplayname' + chatid).className = 'wtw3dinternet-chatdisplaynameblink';
			}
		} else {
			if (dGet('wtw_chatdisplayname' + chatid) != null) {
				dGet('wtw_chatdisplayname' + chatid).className = 'wtw3dinternet-chatdisplayname';
			}
		}
	} catch(ex) {
		WTW.log("plugins:wtw-3dinternet:scripts-chat.js-setChatNewInfo=" + ex.message);
	}
}
