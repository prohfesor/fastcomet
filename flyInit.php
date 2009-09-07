<?php 
	require_once('flyFunc.php');
	
	require_once('flyEnv.php');

	require_once('flySession.php');
	
	require_once('flyDb.php');
	$db = flyDb::getInstance();
	
	require_once('flyDbTableQuery.php');
	
	if (get_magic_quotes_gpc()) {
		new flyError("Magic quotes are on!");
	}
?>