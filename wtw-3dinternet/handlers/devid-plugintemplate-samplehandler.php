<?php
global $wtwhandlers;
try {
	/* include the class for the functions to save, edit, or delete the records in the database */
	require_once(WTW_3DINTERNET_PATH . '/functions/class_functions.php');
	global $wtw3dinternet_functions;
	/* get sent data */
	$zrequest = file_get_contents('php://input');
	$zrequest = json_decode($zrequest, TRUE);
	/* get the requested function name for the switch case below */
	$zfunction = strtolower($wtwhandlers->getPost('function',''));
	
	/* get form Posted Values - your passed data */
	/* $wtwhandlers->getPost(fieldname, defaultvalue); */
	$zfieldid = $wtwhandlers->getPost('fieldid','');
	$zfieldname = $wtwhandlers->getPost('fieldname','');

	/* set response array of values - customize as needed */
	$zresponse = array(
		'serror'=> ''
	);
	switch ($zfunction) {
		case "savesampledata":
			/* Sample function, you can save records in the database and respond with error message if there is an error */
			if ($wtw3dinternet_functions->saveSample($zfieldid, $zfieldname) == false) {
				$zresponse = array(
					'serror'=> 'Could not save Sample Data'
				);
			}
			break;
		case "getsampledata":
			/* Sample function, you can return records from the database as an array */
			$zresponse = $wtw3dinternet_functions->getSample($zfieldid);
			break;
	}

	echo $wtwhandlers->addHandlerHeader($wtwhandlers->domainname);
	echo json_encode($zresponse);

} catch (Exception $e) {
	$wtwhandlers->serror("core-handlers-wtw-3dinternet-samplehandler.php=".$e->getMessage());
}
?>