<?php
#	pluginname = wtw-3dinternet
#	title = WalkTheWeb 3D Internet
#	description = 3D Internet connects your WalkTheWeb instance to the World with Multiplayer and Chat!
#	author = Aaron Dishno Ed.D.
# 	version = 1.0.0

/* change the information above for your plugin */
/* then search and replace the following with your DEVID and PLUGIN Name: */
/* 		WTW_3DINTERNET */
/*		wtw3dinternet */
/*		wtw-3dinternet */

/* for more information about 3D plugins and the latest updates, see: */
/* https://www.walktheweb.com/wiki/3d-plugin-template/ */
/* WalkTheWeb uses BabylonJS.com game engine */
/* https://doc.babylonjs.com/babylon101/ */

global $wtwplugins;

if (!defined('wtw_serverinstanceid')) exit; // Exit if accessed directly

if (!defined('WTW_3DINTERNET_FILE')) {
	define('WTW_3DINTERNET_FILE', __FILE__ );
}

if (!class_exists('wtw3dinternet')) {
	require_once($wtwplugins->contentpath."\\plugins\\wtw-3dinternet\\functions\\class_plugin.php");
}
?>
